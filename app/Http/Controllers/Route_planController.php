<?php
namespace App\Http\Controllers;
use DB;
use Auth;
use Validator;
use DataTables;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Order_history;
use App\Models\Route_plan;
use App\Models\Rider_has_zone;
use App\Models\Hub_has_rider;
use App\Models\Distribution_hub;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;


class Route_planController extends Controller
{
    public $today;
    function __construct()
    {
        $this->middleware('permission:schedule_route_plan-list', ['only' => ['index','show']]);
        $this->middleware('permission:schedule_route_plan-create', ['only' => ['create','store']]);
        $this->middleware('permission:schedule_route_plan-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:schedule_route_plan-delete', ['only' => ['destroy']]);
        $this->today =  date('Y-m-d');
    }

    public function index(Request $request)
    {
        $user_id                = Auth::user()->id;
        $user                   = DB::table('users')
                                    ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                    ->leftjoin('hub_has_users', 'hub_has_users.user_id', '=', 'users.id')
                                    ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                                    ->where('users.id', $user_id)
                                    ->select(
                                                'users.id as user_id',
                                                'hub_has_users.user_id as hub_user_id',
                                                'users.name as user_name',
                                                'roles.id as role_id',
                                                'roles.name as role_name',
                                            )
                                    ->first();

            if($user->role_id == 1){
                $hubs           = Distribution_hub::pluck('name','id')->all();
            }else{
                $hubs           = DB::table('distribution_hubs')
                                    ->leftjoin('hub_has_users', 'hub_has_users.hub_id', '=', 'distribution_hubs.id')
                                    ->where('hub_has_users.user_id', $user->user_id)
                                    ->select(
                                        'distribution_hubs.id as id',
                                        'distribution_hubs.name as name'
                                        )
                                    ->pluck('distribution_hubs.name','distribution_hubs.id')
                                    ->all();
            }
        return view('route_plans.index',
                        compact('hubs')
                    );
    }
  
    public function fetch_route_summary(Request $request)
    {
        
        if($request->ajax()){

            if(isset($request->p_date)){
                $plan_date  = $request->p_date;
            }else{
                return response()->json(['error'=>"Please select plan date!!!"]);
            }

            $record         = DB::table('route_plans')
                                    ->where('route_plans.schedule',1)
                                    ->whereDate('route_plans.updated_at','=', $plan_date)  
                                    ->get();

            if(!$record->isEmpty()){
                return response()->json(['error'=>"Today's route plan is already scheduled"]);
            }
         
            // $dt             = "2021-03-16";
            $hub_id         = $request->hub_id;
        
            $orders         = DB::table('route_plans')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                                    ->select('status_id', DB::raw('count(*) as total'),'statuses.name')
                                    ->groupBy('status_id','statuses.name')
                                    ->where('route_plans.schedule',0)
                                    ->whereDate('route_plans.created_at','=', $plan_date)  
                                    ->where('route_plans.hub_id',$hub_id)
                                    ->get();
                                    // dd($orders);

            $zones              = DB::table('hub_has_zones')
                                    ->leftjoin('zones', 'zones.id', '=', 'hub_has_zones.zone_id')
                                    ->where('hub_has_zones.hub_id',$hub_id)
                                    ->pluck('zones.name','zones.id')
                                    ->all();

            $riders              = DB::table('hub_has_riders')
                                    ->leftjoin('riders', 'riders.id', '=', 'hub_has_riders.rider_id')
                                    ->where('hub_has_riders.hub_id',$hub_id)
                                    ->select('riders.name','riders.id','riders.max_loc')
                                    // ->pluck('riders.name','riders.id')
                                    ->get()
                                    ->all();


            $time_slots         = DB::table('time_slots')
                                    ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                    ->pluck('name','id')
                                    ->all();


            $rd_orders          = array();
            foreach ($riders as $r_key => $r_value) {
                $rec            = DB::table('route_plans')
                                    ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                                    ->select(
                                                'route_plans.timeslot_id',
                                                 DB::raw('count(DISTINCT orders.customer_id) as total'
                                            ))
                                    ->groupBy('route_plans.timeslot_id')
                                    ->where('route_plans.rider_id',$r_value->id)     // rider rides 
                                    ->where('route_plans.schedule',0)
                                    ->whereDate('route_plans.created_at','=', $plan_date)  
                                    ->where('route_plans.hub_id',$hub_id)
                                    ->get();
                        
                foreach ($time_slots as $t_key => $t_value) {
                    $rd_orders[$r_value->id][$t_key] =  0;
                    foreach($rec as $key =>$value){
                        if($value->timeslot_id ==  $t_key){
                            $rd_orders[$r_value->id][$t_key] = $value->total;
                        }
                    }
                }
            }

            // dd($rd_orders);

            $zn_orders          = array();
            foreach ($zones as $z_key => $z_value) {
                $rec            = DB::table('route_plans')
                                    // ->select('timeslot_id', DB::raw('count(*) as total'))
                                    ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                                    ->select(
                                                'route_plans.timeslot_id',
                                                 DB::raw('count(DISTINCT orders.customer_id) as total'
                                            ))
                                    ->groupBy('route_plans.timeslot_id')
                                    ->where('route_plans.zone_id',$z_key)
                                    ->where('route_plans.schedule',0)
                                    ->whereDate('route_plans.created_at','=', $plan_date)  
                                    ->where('route_plans.hub_id',$hub_id)
                                    ->get();
                              

                foreach ($time_slots as $t_key => $t_value) {
                    $zn_orders[$z_key][$t_key] =  0;
                    foreach($rec as $key =>$value){
                        if($value->timeslot_id ==  $t_key){
                            $zn_orders[$z_key][$t_key] = $value->total;
                        }
                    }
                }
            }
           
            $act_orders      = DB::table('route_plans')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                                    ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                                    ->select(
                                                'route_plans.status_id as id',
                                                 DB::raw('count(DISTINCT orders.customer_id) as total',
                                                 'statuses.statuses.name'
                                            ))
                                    // ->select('status_id as id', DB::raw('count(*) as total'),'statuses.name')
                                    ->groupBy('route_plans.status_id','statuses.name')
                                    ->where('route_plans.schedule',0)
                                    ->whereDate('route_plans.created_at','=', $plan_date)  
                                    ->where('route_plans.hub_id',$hub_id)
                                    ->get();


                $act_orders      = DB::table('route_plans')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                                    ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                                    ->orderBy('route_plans.status_id','DESC')
                                    ->select(
                                                'route_plans.id as id',
                                                'orders.customer_id',
                                                'route_plans.status_id'
                                            )
                                    // ->select('status_id as id', DB::raw('count(*) as total'),'statuses.name')
                                    // ->groupBy('route_plans.status_id','statuses.name')
                                    ->where('route_plans.schedule',0)
                                    ->whereDate('route_plans.created_at','=', $plan_date)  
                                    ->where('route_plans.hub_id',$hub_id)
                                    ->get();


                                    // dd($act_orders);
                
                $record = array();
                $record[0]['id'] = 1;
                $record[1]['id'] = 2;
                $record[2]['id'] = 3;

                $record[0]['total'] = 0;
                $record[1]['total'] = 0;
                $record[2]['total'] = 0;

                $cus_rec         = array();
                
                foreach ($act_orders as $key => $value) {
                   

                    if(!(in_array($value->customer_id,$cus_rec))){
                        if($value->status_id == 3){ // pick & drop
                            // $record[2]['id'] = 2;
                            $record[2]['total']++;
                        }else{
                            if($value->status_id == 1){ //pickup
                                $record[0]['total']++;
                            }
                            if($value->status_id == 2){ //drop off
                                $record[1]['total']++;
                            }
                            // if($value->status_id == 3){  // pick & drop
                            //     $record[2]['id']++;
                            // }
                        }
                        $cus_rec[] = $value->customer_id;
                    }
                    

                }

                $aa = array();
                foreach ($record as $key => $value) {
                    # code...
                    $aa[] = (object)($value);
                    // dd($record);
                }


            $act_orders = collect($aa);
            // dd($act_orders);
                                   
            if($act_orders){

                $statuses           = DB::table('statuses')
                                        ->orderBy('statuses.id','ASC')
                                        ->select(
                                                    'statuses.id',
                                                    'statuses.name'
                                                )
                                                
                                        ->whereIn('statuses.id', [1,2,3])
                                        ->get();
                
                $details            = view('route_plans.summary_table',
                                        compact( 
                                                'time_slots',
                                                'zones',
                                                'zn_orders',
                                                'statuses',
                                                'act_orders',
                                                'rd_orders',
                                                'riders'
                                                )
                                        )
                                        ->render();
                return response()->json(['data'=>$orders,'details'=>$details]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
            
        }

    }

    public function get_route(Request $request){
        
        if($request->ajax()){

            if(isset($request->plan_date)){
                $plan_date  = $request->plan_date;
            }else{
                return response()->json(['error'=>"Please select plan date!!!"]);
            }
             //   dd($request->hub_id);
            $riders     = DB::table("riders")
                            ->select(
                                'riders.id as rider_id',
                            )
                            // ->where('riders.id','1')
                            ->where('riders.status','1')
                            ->get();

            $record     = DB::table("route_plans")
                            ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                            ->leftjoin('distribution_hubs', 'distribution_hubs.id', '=', 'route_plans.hub_id')
                            ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                            ->select(
                                    'route_plans.id',
                                    'route_plans.route',
                                    'route_plans.order_id',
                                    'route_plans.rider_id',
                                    'route_plans.timeslot_id',
                                    'customer_has_addresses.latitude',
                                    'customer_has_addresses.longitude',
                                    'distribution_hubs.lat as hub_latitude',
                                    'distribution_hubs.lng as hub_longitude',
                                    DB::raw('CONCAT(distribution_hubs.lat,  ", ", distribution_hubs.lng) as sr'),
                                    DB::raw('CONCAT(customer_has_addresses.latitude,  ", ", customer_has_addresses.longitude) as des'),
                                    )
                            // ->orderBy('riders.id')
                            
                            ->where('route_plans.hub_id',$request->hub_id)
                            ->whereDate('route_plans.updated_at','=', $plan_date)
                            // ->where('riders.id','=','1')
                            ->where('route_plans.schedule','0')
                            ->get();

                          



            if($record){
                foreach ($record as $key => $value) {
                    // BEGIN:: Has latitude and longitude set of every order, if not, set them
                    if((($value->latitude) == null) || (($value->longitude) == null)){
                    
                        // Get primary address of the customer
                        $add_rec    = DB::table('orders')
                                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                        ->leftjoin('customer_has_addresses', 'customer_has_addresses.customer_id', '=', 'customers.id')
                                        ->select(
                                                    'customers.id as customer_id',
                                                    'customers.name as customer_name',
                                                    'customer_has_addresses.latitude',
                                                    'customer_has_addresses.longitude',
                                                    'customer_has_addresses.id as cus_address_id',
                                                    'customer_has_addresses.address as cus_address',
                                                )
                                        ->where('customer_has_addresses.status', 0) 
                                        ->where('orders.id',$value->order_id)
                                        ->first();
                        
                        // Set the address values of customer in the array
                        if(($add_rec)){
                            // $record[$key]->customer_id       = $add_rec->customer_id;
                            // $record[$key]->customer_name     = $add_rec->customer_name;
                            $record[$key]->latitude          = $add_rec->latitude;
                            $record[$key]->longitude         = $add_rec->longitude;
                            $record[$key]->des               = (($add_rec->latitude). ", ".($add_rec->longitude) );
                            // $record[$key]->cus_address_id    = $add_rec->cus_address_id;

                            // Updating address id in route_plan too.
                            $chk                             = Route_plan::where('id', $value->id)
                                                                ->update([
                                                                            'address_id'    => $add_rec->cus_address_id,
                                                                            'updated_at'    => $plan_date
                                                                        ]);
                        }
                    }
                }
            }

            $rds = array();
            if(!$record->isEmpty()){
                foreach ($riders as $key => $rider_value) {
                
                    foreach ($record as $key => $value) {
                        if($value->rider_id == $rider_value->rider_id){
                            $rds[$value->rider_id][$value->route][$value->timeslot_id][] = $value; 
                        }
                    }
                }
            }else{
                return response()->json(['error'=>[0=>"Data not found!"  ]]);
            }
            

            // dd($rds);
            
            return response()->json(['data'=>$riders,'rds'=>$rds]);
        }else{
            return response()->json(['error'=>[0=>"Data not found"  ]]);
        }
    }
    
    
    public function fetch_route_riders(Request $request)
    {
        
        if($request->ajax()){

            if(isset($request->p_date)){
                $plan_date  = $request->p_date;
            }else{
                return response()->json(['error'=>"Please select plan date!!!"]);
            }
            
            $record         = DB::table('route_plans')
                                ->where('route_plans.schedule',1)
                                ->whereDate('route_plans.updated_at','=', $plan_date)  
                                ->get();

            if(!$record->isEmpty()){
                return response()->json(['error'=>"Today's route plan is already scheduled"]);
            }

            $hub_id             = $request->hub_id;
            $riders             = DB::table('hub_has_riders')
                                    ->leftjoin('riders', 'riders.id', '=', 'hub_has_riders.rider_id')
                                    ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                                    ->select(
                                                'riders.id',
                                                'riders.name',
                                                'riders.max_loc',
                                                'riders.max_drop_weight',
                                                'riders.max_route',
                                                'riders.max_pick',
                                                'riders.status',
                                                'riders.vehicle_type_id',
                                                'riders.max_drop_size as max_drop_size',
                                            )
                                    ->where('hub_has_riders.hub_id',$hub_id)
                                    ->get();

            if($riders){
                $rider_zones    = DB::table('rider_has_zones')
                                    ->leftjoin('zones', 'zones.id', '=', 'rider_has_zones.zone_id')
                                    ->select(
                                                'rider_has_zones.rider_id',
                                                'rider_has_zones.zone_id',
                                                'zones.name',
                                                 DB::raw('CASE WHEN rider_has_zones.priority = 1 THEN  "Primary"  ELSE "Secondary" END AS zone_type')
                                            )
                                    ->orderBy('rider_has_zones.priority','DESC')
                                    ->get(); 

                $vehicle_types  = DB::table('vehicle_types')
                                    ->pluck('vehicle_types.name','vehicle_types.id')
                                    ->all();

                $details        = view('route_plans.rider_table',
                                        compact( 
                                                'riders',
                                                'rider_zones',
                                                'vehicle_types'
                                                )
                                        )
                                        ->render();

                return response()->json(['data'=>$riders,'details'=>$details]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
        }
    }

    public function fetch_plan(){
        //  dd("dasdf");
        $riders             = DB::table("riders")
                                ->select(
                                    'riders.id as rider_id',
                                )
                                ->where('riders.status','1')
                                ->get();
                                
        $record             = DB::table('route_plans')
                                ->leftjoin('distribution_hubs', 'distribution_hubs.id', '=', 'route_plans.hub_id')
                                ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                                ->leftjoin('areas', 'areas.id', '=', 'route_plans.area_id')
                                ->leftjoin('zones', 'zones.id', '=', 'route_plans.zone_id')
                                // ->leftjoin('riders', 'riders.id', '=', 'route_plans.rider_id')
                                ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                                ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                                ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                                ->orderBy('orders.customer_id')
                                ->select(
                                            'route_plans.*',
                                            // 'riders.color_code',
                                            'areas.name as area_name',
                                            'zones.name as zone_name',
                                            'orders.ref_order_id',
                                            'customer_has_addresses.customer_id as customer_id',
                                            'statuses.name as status_name',
                                            'customer_has_addresses.latitude',
                                            'customer_has_addresses.longitude',
                                            'distribution_hubs.lat as hub_latitude',
                                            'distribution_hubs.lng as hub_longitude',
                                            'customer_has_addresses.address as cus_address',
                                                DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                        )
                                ->where('route_plans.schedule', 0) 
                                ->get()
                                ->all();

            // $orders         = ($this->fn_sort($orders));

            $rds = array();
            foreach ($riders as $key => $rider_value) {
                
                foreach ($record as $key => $value) {
                    if($value->rider_id == $rider_value->rider_id){
                        $rds[$value->rider_id][$value->route][] = $value; 
                    }
                }
            }
            return response()->json(['data'=>$record,'details'=>$rds]);
   
    }

    public function fetch_route_orders(Request $request)
    {
        $cus_rider = array();
        if($request->ajax()){

            if(isset($request->p_date)){
                $plan_date  = $request->p_date;
            }else{
                return response()->json(['error'=>"Please select plan date!!!"]);
            }

            $record         = DB::table('route_plans')
                                ->where('route_plans.schedule',1)
                                ->whereDate('route_plans.updated_at','=', $plan_date)  
                                ->get();

            if(!$record->isEmpty()){
                return response()->json(['error'=>"Today's route plan is already scheduled"]);
            }

            $hub_id                 = $request->hub_id;

            $orders                 = DB::table('route_plans')
                                        ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                                        ->leftjoin('areas', 'areas.id', '=', 'route_plans.area_id')
                                        ->leftjoin('zones', 'zones.id', '=', 'route_plans.zone_id')
                                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                        ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                                        ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                                        ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                                        ->orderBy('orders.customer_id')
                                        ->select(
                                                    'route_plans.*',
                                                    // 'riders.color_code',
                                                    'areas.name as area_name',
                                                    'zones.name as zone_name',
                                                    'orders.ref_order_id',
                                                    'customers.name as customer_name',
                                                    'customer_has_addresses.customer_id as customer_id',
                                                    'statuses.name as status_name',
                                                    'customer_has_addresses.latitude',
                                                    'customer_has_addresses.longitude',
                                                    'customer_has_addresses.address as cus_address',
                                                     DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                                )
                                        ->where('route_plans.schedule', 0) 
                                        ->where('orders.hub_id',$hub_id)
                                        ->whereDate('route_plans.created_at','=', $plan_date)  
                                        ->get()
                                        ->all();
         

            if($orders){
                foreach ($orders as $key => $value) {
                    $p_date = $value->updated_at;
                    // BEGIN:: Has latitude and longitude set of every order, if not, set them
                    if((($value->latitude) == null) || (($value->longitude) == null)){
                  
                        // Get primary address of the customer
                        $add_rec    = DB::table('orders')
                                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                        ->leftjoin('customer_has_addresses', 'customer_has_addresses.customer_id', '=', 'customers.id')
                                        ->select(
                                                    'customers.id as customer_id',
                                                    'customers.name as customer_name',
                                                    'customer_has_addresses.latitude',
                                                    'customer_has_addresses.longitude',
                                                    'customer_has_addresses.id as cus_address_id',
                                                    'customer_has_addresses.address as cus_address',
                                                )
                                        ->where('customer_has_addresses.status', 0) 
                                        ->where('orders.id',$value->order_id)
                                        ->first();
                        
                        // Set the address values of customer in the array
                        if(($add_rec)){
                            $orders[$key]->customer_id       = $add_rec->customer_id;
                            $orders[$key]->customer_name     = $add_rec->customer_name;
                            $orders[$key]->latitude          = $add_rec->latitude;
                            $orders[$key]->longitude         = $add_rec->longitude;
                            $orders[$key]->cus_address       = $add_rec->cus_address;
                            $orders[$key]->cus_address_id    = $add_rec->cus_address_id;

                            // Updating address id in route_plan too.
                            $chk                             = Route_plan::where('id', $value->id)
                                                                ->update([
                                                                                'address_id'  => $add_rec->cus_address_id,
                                                                                'updated_at' =>$p_date
                                                                        ]);
                        }
                    }
                    // END:: Has latitude and longitude set of every order, if not, set them
                    
                    // echo "Id: $value->zone_id";
                    // echo "<br>id: $value->id";
                    // echo ", Assigning: $asgn_rider";
                    $p_rider                            = ($this->get_rider_primary_zone($value->zone_id)); 
                    $s_rider                            = ($this->get_rider_secondary_zone($value->zone_id));
                    $h_rider                            = ($this->get_rider_has_hanger($value->hub_id));

                    $asgn_rider                         = ($this->fn_assign_rider(($value->customer_id),($value->id),($value->hanger),($value->weight),$p_rider,$s_rider, $h_rider));        
                    // $cus_rider[$value->customer_id][$value->id] = 1;
                    // if($value->id  == 27){
                    //     dd($asgn_rider);
                    // }
                    
                    if(isset( $asgn_rider->rider_id)){
                        $orders[$key]->assign_rider     = $asgn_rider->rider_id;
                        $orders[$key]->rider_id         = $asgn_rider->rider_id;
                    }else{
                        $orders[$key]->assign_rider     = null;
                    }

                    if(isset($asgn_rider->hanger)){
                        $orders[$key]->rider_hanger     = $asgn_rider->hanger;
                    }else{
                        $orders[$key]->rider_hanger     = null;
                    }

                    
                    if(isset( $asgn_rider->color_code)){
                        $orders[$key]->color_code       = $asgn_rider->color_code;
                    }else{
                        $orders[$key]->color_code       = null;
                    }
                    
                    if(isset( $asgn_rider->max_drop_weight)){
                        $orders[$key]->max_drop_weight  = $asgn_rider->max_drop_weight;
                    }else{
                        $orders[$key]->max_drop_weight  = 0;
                    }

                    if(isset( $asgn_rider->max_drop_size)){
                        $orders[$key]->max_drop_size    = $asgn_rider->max_drop_size;
                    }else{
                        $orders[$key]->max_drop_size    = 0;
                    }

                    if(isset( $asgn_rider->max_pick)){
                        $orders[$key]->max_pick         = $asgn_rider->max_pick;
                    }else{
                        $orders[$key]->max_pick         = 0;
                    }

                    if(isset( $asgn_rider->max_loc)){
                        $orders[$key]->max_loc         = $asgn_rider->max_loc;
                    }else{
                        $orders[$key]->max_loc         = 0;
                    }
                    
                    $orders[$key]->primary_rider        = $p_rider;
                    $orders[$key]->secondary_rider      = $s_rider;
                    $orders[$key]->hanger_rider         = $h_rider;
                   

                }

                $orders         = ($this->fn_sort($orders));

                $details        = view('route_plans.order_table',
                                        compact( 
                                                    'orders',
                                                )
                                        )
                                        ->render();
                                        
                return response()->json(['data'=>$orders,'details'=>$details]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
        }
    }
    



    public function resort(Request $request){
        $record = array();
        if(isset($request['rider_id'] )){
            
            // BEGIN :: Setting rides element to 0;
            foreach($request['rider_id'] as $key => $outer_id){
                
                // BEGIN :: checking have riders are assigned to every order.
                if(isset($outer_id) && ($outer_id == 0)){
                    return response()->json(['error'=>[0=>"Please select rider of order no# " . $request['odr_id'][$key] ]]);
                }
                // END :: checking have riders are assigned to every order.
                
                $riders     = DB::table('riders')
                                ->select('riders.max_pick','riders.max_drop_size','riders.max_loc','riders.name')
                                ->where('riders.id',$outer_id)
                                ->first();
                                
               $record[$outer_id]["Pickup"]             = 0;
               $record[$outer_id]["Drop Off"]           = 0;
               $record[$outer_id]["Pick & Drop"]        = 0;
               $record[$outer_id]["tot_loc"]            = 0;
               $record[$outer_id]["name"]               = $riders->name;
               $record[$outer_id]["max_loc"]            = $riders->max_loc;
               $record[$outer_id]["max_pick"]           = $riders->max_pick;
               $record[$outer_id]["max_drop_size"]      = $riders->max_drop_size;
            }
            // END :: Setting rides element to 0;
            
            // BEGIN :: counting the rides
            foreach($request['rider_id'] as $key => $outer_id){
                
                $status = $request['status_name'][$key];
                $record[$outer_id][$status]     += 1;
                $record[$outer_id]['tot_loc']   += 1;
                
                
                     if(($record[$outer_id]['tot_loc']) > ($record[$outer_id]["max_loc"])){
                        return response()->json(['error'=>[0=>($record[$outer_id]['name'])." Max location exceeded"]]);
                    }
                
                
                 $route      = DB::table('route_plans')
                                        ->select('route_plans.weight')
                                        ->where('route_plans.id',$request['order_id'][$key])
                                        ->first();

                if( ($record[$outer_id]['max_drop_size']) < ($route->weight) ){
                    return response()->json(['error'=>[0=>($record[$outer_id]['name'])." drop size is less than the order no# " . $request['odr_id'][$key]  ]]);
                }
                
               
            }
        }
        
        // Checking:: same customer must have same riders
        if(isset($request['rider_id'] )){
            foreach($request['customer_id'] as $key => $outer_id){
                foreach($request['customer_id'] as $k => $inner_id){
                    if( $outer_id  == $inner_id){
                        if($request['rider_id'][$key] != $request['rider_id'][$k]){
                            $customer   = DB::table('customers')
                                            ->select('customers.name')
                                            ->where('customers.id', $request['customer_id'][$k])
                                            ->first();
                            if(isset($customer)){
                                return response()->json(['error'=>[0=>'Order no: '.$request['odr_id'][$key].' & '.$request['odr_id'][$k]. ' cannot have different riders because they belongs to same customer: '.$customer->name]]);
                            }else{
                                return response()->json(['error'=>[0=>"Something went wrong!!!!"]]);
                            }
                        }
                    }
                }  
            }
        }else{
            return response()->json(['error'=>[0=>"No orders found!!!!"]]);  
        }

        if(isset($request['order_id'])){
            $validator = Validator::make($request->all(), [
                    'order_id'                    => 'required|array',
                    'order_id.*'                  => 'required|min:1|numeric',
                    'rider_id'                    => 'required|array',
                    'rider_id.*'                  => 'required|min:1|numeric'
                    
                ],
                [
                    'rider_id.*.min'              => 'Please select rider of yellow orders'
                ]
            );
    
        if ($validator->passes()) {
            // Update Order data 
            foreach ($request['order_id'] as $key => $id) {
                $data                               = Route_plan::select(
                                                                    'route_plans.id',
                                                                    'route_plans.seq',
                                                                    'route_plans.schedule',
                                                                    'route_plans.route',
                                                                    'route_plans.hanger',
                                                                    'route_plans.order_id',
                                                                    'route_plans.rider_id',
                                                                    'route_plans.status_id',
                                                                    'route_plans.created_by',
                                                                    'route_plans.updated_at',
                                                                )
                                                        ->find($id);
                  
                if($data){
                    $up_date                        = $data->updated_at;
                    $rider                          = DB::table('riders')
                                                        ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                                                        ->select('vehicle_types.hanger','riders.name')
                                                        ->where('riders.id', $request['rider_id'][$key])
                                                        ->first();
                    if((($data->hanger ) == 1 ) && (($rider->hanger)== 0 )){
                        return response()->json(['error'=>[0=>$rider->name. " has hangerless vehicle. Order no# ". ($data->order_id) ." requires hanger rider"]]);
                    }

                    
                    // $req['id']                      = $id;
                    // $req['rider_id']                = $request['rider_id'][$key];
                    // $req['route']                   = $request['route_no'][$key];
                    // $req['updated_at']              = $up_date;
                 
                    
                    // $req['schedule']                = 1;
                    // $req['created_by']              = Auth::user()->id;
                    $upd                            = $data->update([
                                                                        // 'id'            => $id,
                                                                        'rider_id'      => $request['rider_id'][$key],
                                                                        'route'         => $request['route_no'][$key]
                                                                    ]);

                   
                    // END:: Update order table columns "pickup_rider / delivery_rider" and "pickup_timeslot/ delivery_timeslot" 
                }else{
                    return response()->json(['error'=>[0=>"No record found."]]);
                }
            }
            return response()->json(['success'=>'Riders have been assigned successfully.']);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
            
        }
    }

    public function store_resort(Request $request){
     
        
        $record = array();
        if(isset($request['rider_id'] )){
            if(isset($request->pln_date)){
                $plan_date  = $request->pln_date;
            }else{
                return response()->json(['error'=>"Please select plan date!!!"]);
            }
            
            // BEGIN :: Setting rides element to 0;
            foreach($request['rider_id'] as $key => $outer_id){
                
                // BEGIN :: checking have riders are assigned to every order.
                if(isset($outer_id) && ($outer_id == 0)){
                    return response()->json(['error'=>[0=>"Please select rider of order no# " . $key ]]);
                }
                // END :: checking have riders are assigned to every order.
                
                $riders     = DB::table('riders')
                                ->select('riders.max_pick','riders.max_drop_size','riders.max_loc','riders.name')
                                ->where('riders.id',$outer_id)
                                ->first();
                                
               $record[$outer_id]["Pickup"]             = 0;
               $record[$outer_id]["Drop Off"]           = 0;
               $record[$outer_id]["Pick & Drop"]        = 0;
               $record[$outer_id]["tot_loc"]            = 0;
               $record[$outer_id]["name"]               = $riders->name;
               $record[$outer_id]["max_loc"]            = $riders->max_loc;
               $record[$outer_id]["max_pick"]           = $riders->max_pick;
               $record[$outer_id]["max_drop_size"]      = $riders->max_drop_size;
            }
            // END :: Setting rides element to 0;
            
            // BEGIN :: counting the rides
            foreach($request['rider_id'] as $key => $outer_id){
                
                $status = $request['status_name'][$key];
                $record[$outer_id][$status]     += 1;
                $record[$outer_id]['tot_loc']   += 1;
                
                
                     if(($record[$outer_id]['tot_loc']) > ($record[$outer_id]["max_loc"])){
                        return response()->json(['error'=>[0=>($record[$outer_id]['name'])." Max location exceeded"]]);
                    }
                
                
                 $route      = DB::table('route_plans')
                                        ->select('route_plans.weight')
                                        ->where('route_plans.id',$request['order_id'][$key])
                                        ->first();

                if( ($record[$outer_id]['max_drop_size']) < ($route->weight) ){
                    return response()->json(['error'=>[0=>($record[$outer_id]['name'])." drop size is less than the order no# " . $request['odr_id'][$key]  ]]);
                }
                
               
            }
        }

        
        // Checking:: same customer must have same riders
        
        if(isset($request['rider_id'] )){
            foreach($request['customer_id'] as $key => $outer_id){
                foreach($request['customer_id'] as $k => $inner_id){
                    if( $outer_id  == $inner_id){
                        if($request['rider_id'][$key] != $request['rider_id'][$k]){
                            $customer   = DB::table('customers')
                                            ->select('customers.name')
                                            ->where('customers.id', $request['customer_id'][$k])
                                            ->first();
                            if(isset($customer)){
                                return response()->json(['error'=>[0=>'Order no: '.$request['odr_id'][$key].' & '.$request['odr_id'][$k]. ' cannot have different riders because they bolongs to same customer: '.$customer->name]]);
                            }else{
                                return response()->json(['error'=>[0=>"Something went wrong!!!!"]]);
                            }
                        }
                    }
                }  
            }
        }else{
            return response()->json(['error'=>[0=>"No orders found!!!!"]]);  
        }

        if(isset($request['order_id'])){
            $validator = Validator::make($request->all(), [
                    'order_id'                    => 'required|array',
                    'order_id.*'                  => 'required|min:1|numeric',
                    'rider_id'                    => 'required|array',
                    'rider_id.*'                  => 'required|min:1|numeric'
                ],
                [
                    'rider_id.*.min'              => 'Please select rider of yellow orders'
                ]
            );
    
        if ($validator->passes()) {
            // Update Order data 
            foreach ($request['order_id'] as $key => $id) {
                $data                               = Route_plan::leftjoin('riders', 'riders.id', '=', 'route_plans.rider_id')
                                                        ->select(
                                                                    'route_plans.id',
                                                                    'route_plans.seq',
                                                                    'route_plans.schedule',
                                                                    'route_plans.route',
                                                                    'route_plans.hanger',
                                                                    'route_plans.order_id',
                                                                    'route_plans.rider_id',
                                                                    'route_plans.status_id',
                                                                    'route_plans.created_by',
                                                                    'riders.name as rider_name',
                                                                )
                                                        ->find($id);
                if($data){
                    $rider                          = DB::table('riders')
                                                        ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                                                        ->select('vehicle_types.hanger','riders.name')
                                                        ->where('riders.id', $request['rider_id'][$key])
                                                        ->first();
                    if((($data->hanger ) == 1 ) && (($rider->hanger)== 0 )){
                        return response()->json(['error'=>[0=>$rider->name. " has hangerless vehicle. Order no# ". ($data->order_id) ." requires hanger rider"]]);
                    }

                    
                    $req['id']                      = $id;
                    $req['rider_id']                = $request['rider_id'][$key];
                    $req['route']                   = $request['route_no'][$key];
                    $req['seq']                     = $request['seq'][$key];
                    $req['travel_time']             = $request['time'][$key];
                    $req['req_dist']                = $request['dist'][$key];
                    // $req['updated_at']              =  $plan_date;
                    
                    $req['schedule']                = 1;
                    $req['created_by']              = Auth::user()->id;
                    $upd                            = $data->update($req);

                    // BEGIN:: Update order table columns "pickup_rider / delivery_rider" and "pickup_timeslot/ delivery_timeslot" 
                    if(($data->status_id ) == 1 ){ // 1: Pickup

                        $chk            = Order::where('id', $data->order_id)
                                            ->update([

                                                'pickup_rider_id'       => $data->rider_id,
                                                'pickup_rider'          => $data->rider_name,
                                                'status_id2'            => '6',  // 6: Rider Assigned
                                            ]);

                    }else{

                        $chk            = Order::where('id', $data->order_id)
                                            ->update([

                                                'delivery_rider_id'     => $data->rider_id,
                                                'delivery_rider'        => $data->rider_name,
                                                'status_id2'            => '6',  // 6: Rider Assigned
                                            ]);

                    }
                    // BEGIN::Insert order status in Order_history table//
                    $val                    = new Order_history();
                    $val->order_id          = $data->order_id;
                    $val->created_by        = Auth::user()->id;
                    $val->status_id         = 6;
                    $val->save();
                    // END::Insert order status in Order_history table//

                    // END:: Update order table columns "pickup_rider / delivery_rider" and "pickup_timeslot/ delivery_timeslot" 
                }else{
                    return response()->json(['error'=>[0=>"No record found."]]);
                }
            }
            return response()->json(['success'=>'Riders have been assigned successfully.']);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
            
        }
    }

    public function update_riders(Request $request){
        
        $validator = Validator::make($request->all(),
                [
                    'rider'                       => 'required|array',
                    'rider.*'                     => 'required|min:1|numeric',

                    'max_loc'                     => 'required|array',
                    'max_loc.*'                   => 'required|min:1|numeric',

                    'max_pick'                    => 'required|array',
                    'max_pick.*'                  => 'required|min:1|numeric',

                    'max_drop_size'               => 'required|array',
                    'max_drop_size.*'             => 'required|min:1|numeric',

                    'max_drop_weight'             => 'required|array',
                    'max_drop_weight.*'           => 'required|min:1|numeric',

                    'vehicle_type_id'             => 'required|array',
                    'vehicle_type_id.*'           => 'required|min:1|numeric',
                ],
                [
                    'max_loc.*.min'               =>'Max Loc must be greater than 0',
                    'max_loc.*.required'          =>'Max Loc is required',

                    'max_pick.*.min'              =>'Max Pickup must be greater than 0',
                    'max_pick.*.required'         =>'Max Pickup is required',

                    'max_drop_size.*.min'         =>'Max Drop Size must be greater than 0',
                    'max_drop_size.*.required'    =>'Max Drop Size is required',

                    'max_drop_weight.*.min'       =>'Max Drop Weight must be greater than 0',
                    'max_drop_weight.*.required'  =>'Max Drop Weight is required',

                    'vehicle_type_id.*.min'       =>'Please select vehicle',
                    'vehicle_type_id.*.required'  =>'Please select vehicle',
                ]
            );
        if ($validator->passes()) {
            if(isset($request->rider)){
                foreach ($request->rider as $key => $value) {
                  
                    $rider                                  = rider::find($key);
                    if($rider){
                       
                        if((isset($request->state[$key])) && ($request->state[$key] == 1)){
                         
                            $input['id']                    = $key;
                            $input['status']                = $request->state[$key];
                            $input['max_loc']               = $request->max_loc[$key];
                            $input['max_pick']              = $request->max_pick[$key];
                            $input['max_drop_size']         = $request->max_drop_size[$key];
                            $input['max_drop_weight']       = $request->max_drop_weight[$key];
                            $input['vehicle_type_id']       = $request->vehicle_type_id[$key];
                            $input['max_route']             = $request->max_route[$key];
                             $rider->update($input);
                        }else{
                            $input['id']                    = $key;
                            // $input['status']                = $request->state[$key];
                            $input['status']                = 0;
                            $input['max_loc']               = $request->max_loc[$key];
                            $input['max_pick']              = $request->max_pick[$key];
                            $input['max_drop_size']         = $request->max_drop_size[$key];
                            $input['max_drop_weight']       = $request->max_drop_weight[$key];
                            $input['vehicle_type_id']       = $request->vehicle_type_id[$key];
                            $input['max_route']             = $request->max_route[$key];
                             $rider->update($input);
                        }
                       
                    }
                }
               
                if($request->rider){
                    return response()->json(['success'=>'All selected rides are scheduled successfully.']);
                }else{
                    return response()->json(['error'=>[0=>"Something went wrong"]]);
                }
            }else{
                return response()->json(['error'=>[0=>"No record found"]]);
            }
        }
        return response()->json(['error'=>$validator->errors()->all()]);
    }

    public function get_rider_primary_zone($zone_id){
        $data       = Rider_has_zone::orderBy('riders.id')
                        ->leftjoin('riders', 'riders.id', '=', 'rider_has_zones.rider_id')
                        ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                        ->select(
                            'riders.id as id',
                            'riders.max_drop_weight',
                            'riders.max_drop_size',
                            'riders.max_loc',
                            'vehicle_types.hanger',
                            'riders.color_code',
                             DB::raw('CONCAT(riders.name,  "  -  ",
                              (CASE 
                                WHEN vehicle_types.hanger = "0" THEN "(B)" 
                                WHEN vehicle_types.hanger = "1" THEN "(CH)" 
                             END)
                             ) as name')

                            )
                        ->where('rider_has_zones.zone_id',$zone_id)
                        ->where('rider_has_zones.priority',1) // 1 means primary
                        ->where('riders.status',1)
                        
                        ->get()->toArray();
                        // ->pluck('name','id')
                        // ->all();

        if( isset($data) ){
            return $data;
        }else{
            return 0;
        }
    }

    public function get_rider_secondary_zone($zone_id){
        $data       = Rider_has_zone::orderBy('riders.id')
                        ->leftjoin('riders', 'riders.id', '=', 'rider_has_zones.rider_id')
                        ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                        ->select(
                            'rider_has_zones.rider_id as id',
                            'riders.max_drop_weight',
                            'riders.max_drop_size',
                            'vehicle_types.hanger',
                            'riders.max_loc',
                            // 'riders.name',
                            'riders.color_code',
                            DB::raw('CONCAT(riders.name,  "  -  ",
                            (CASE 
                                WHEN vehicle_types.hanger = "0" THEN "(B)" 
                                WHEN vehicle_types.hanger = "1" THEN "(CH)" 
                            END)
                            ) as name')

                            )
                        ->where('rider_has_zones.zone_id',$zone_id)
                        ->where('rider_has_zones.priority',0)  // 0 means secondary
                        ->where('riders.status',1)
                        ->get()->toArray();
    
                        // ->pluck('name','id')
                        // ->all();

        if(isset($data) ){
            return $data;
        }else{
            return 0;
        }

    }

    public function get_rider_has_hanger($hub_id){
         
        $data       = Hub_has_rider::orderBy('riders.id')
                        ->leftjoin('riders', 'riders.id', '=', 'hub_has_riders.rider_id')
                        ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                        ->select(
                                    'riders.id as id',
                                    'riders.max_drop_weight',
                                    'riders.max_drop_size',
                                    'vehicle_types.hanger',
                                    'riders.max_loc',
                                    // 'riders.name',
                                    'riders.color_code',
                                    DB::raw('CONCAT(riders.name,  "  -  ",
                                                (CASE 
                                                    WHEN vehicle_types.hanger = "0" THEN "(B)" 
                                                    WHEN vehicle_types.hanger = "1" THEN "(CH)" 
                                                END)
                                                ) as name'
                                    )

                            )
                        ->where('vehicle_types.hanger',1)
                        // ->where('hub_has_riders.rider_id',6)
                        ->where('hub_has_riders.hub_id',$hub_id)
                        ->where('riders.status',1)
                        ->get()->toArray();

        // $data       = Rider_has_zone::orderBy('riders.id')
        //                 ->leftjoin('riders', 'riders.id', '=', 'rider_has_zones.rider_id')
        //                 ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
        //                 ->select(
        //                     'rider_has_zones.rider_id as id',
        //                     'riders.max_drop_weight',
        //                     'riders.max_drop_size',
        //                     'vehicle_types.hanger',
        //                     // 'riders.name',
        //                     'riders.color_code',
        //                     DB::raw('CONCAT(riders.name,  "  -  ",
        //                     (CASE 
        //                         WHEN vehicle_types.hanger = "0" THEN "(B)" 
        //                         WHEN vehicle_types.hanger = "1" THEN "(CH)" 
        //                     END)
        //                     ) as name')

        //                     )
        //                 ->where('vehicle_types.hanger',1)
        //                 ->get()->toArray();

        if(isset($data) ){
            return $data;
        }else{
            return 0;
        }

    }

    public function store_route_no(Request $request){
        if($request->max_loc){
            foreach($request->max_loc as $key => $value){
                $rider              = rider::find($key);
                if($rider){
                    if(array_key_exists($key,$request->state)){
                        $input['status']            = $request->state[$key];
                    }else{
                        $input['status']            = 0;
                    }
                    $input['max_loc']               = $value;
                    $input['max_pick']              = $request->max_pick[$key];
                    $input['max_drop_weight']       = $request->max_drop_weight[$key];
                    $input['max_route']             = $request->max_route[$key];
                    $rider->update($input);
                }
            }
            if($request->max_loc){
                return redirect()->route('route_plans.create')
                                    ->with('success','Profile Updated');
            }
        }else{
            return redirect()->route('route_plans.create')
                                ->with('permission','No record found.');
        }
    }

    public function update_rider_profile(Request $request){
        if(isset($request->update_attendance)){
            
            foreach ($request->rider as $key => $value) {
                $rider              = rider::find($key);
                if($rider){
                    if(array_key_exists($key,$request->state)){
                        $input['status']            = $request->state[$key];
                    }else{
                        $input['status']            = 0;
                    }
                    $rider->update($input);
                }
            }
            if($request->rider){
                return redirect()->route('route_plans.index')
                                    ->with('success','Profile Updated');
            }else{
                return redirect()->route('route_plans.index')
                                    ->with('permission','No record found.');
            }

        }else{
            $this->validate($request, 
                [
                    'route_date'                => 'required',
                    'max_loc'                   => 'required|array',
                    'max_loc.*'                 => 'required|numeric|min:1',

                    'max_route'                 => 'required|array',
                    'max_route.*'               => 'required|numeric|min:1',

                    'max_pick'                  => 'required|array',
                    'max_pick.*'                => 'required|numeric|min:1',

                    'max_drop_weight'           => 'required|array',
                    'max_drop_weight.*'         => 'required|numeric|min:1',
                    
                ],
                [
                    'max_loc.required'          => 'Max: Location is required field.',
                    'max_loc.*.min'             => 'Max: Location must be greater than 0.',

                    'max_route.required'        => 'Max: Route is required field.',
                    'max_route.*.min'           => 'Max: Route must be greater than 0.',

                    'max_pick.required'         => 'Max: Pickup is required field.',
                    'max_pick.*.min'            => 'Max: Pickup must be greater than 0.',

                    'max_drop_weight.required'  => 'Max: Drop weight is required field.',
                    'max_drop_weight.*.min'     => 'Max: Drop weight must be greater than 0.',
                ]
            );
            if($request->max_loc){
                foreach($request->max_loc as $key => $value){
                    $rider              = rider::find($key);
                    if($rider){
                        if(array_key_exists($key,$request->state)){
                            $input['status']            = $request->state[$key];
                        }else{
                            $input['status']            = 0;
                        }
                        $input['max_loc']               = $value;
                        $input['max_pick']              = $request->max_pick[$key];
                        $input['max_drop_weight']       = $request->max_drop_weight[$key];
                        $input['max_route']             = $request->max_route[$key];
                        $rider->update($input);
                    }
                }
                if($request->max_loc){
                    // dd($request);
                    return $this->create_route_plan($request);
                    // return redirect()->route('route_plans.create_route_plan/{$route_date}')
                    //                     ->with('success','Profile Updated');
                }
            }else{
                return $this->create_route_plan($request);
                // return redirect()->route('route_plans.create')
                //                     ->with('permission','No record found.');
            }
        }

        // dd($request);
       
    }

    public function fn_assign_rider($customer_id,$route_id,$hanger, $weight,$p_rider,$s_rider,$h_rider){

        $orders                 = DB::table('route_plans')
                                        ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                                        ->select(
                                                    'route_plans.id',
                                                )
                                        ->where('orders.customer_id', $customer_id) 
                                        ->where('route_plans.complete', 0) 
                                        ->where('route_plans.schedule', 0) 
                                        ->where('route_plans.hanger', 1) 
                                        ->get();
        $rider      = NULL;
        if( $orders->isEmpty() ){
            if($rider == NULL){
                foreach ($p_rider as $key => $value) {
                    // echo "<br>hh: ". $hanger;
                    // echo "<br>val: ". $value['name'];
                    // echo "<br>h-v: ". $value['hanger'];
                    if((($hanger) == 1) && ($value['hanger'] == 1) && ($value['max_drop_size'] >($weight) )){
                        $rider  = $value['id'];
                        break;
                    }
                    elseif((($hanger) == 0) && ($value['max_drop_size'] > ($weight))){
                        $rider  = $value['id'];
                        break;
                      
                    }
                }
            }    
       
            if($rider == NULL){
                foreach ($s_rider as $key => $value) {
                  
                    if((($hanger) == 1) && ($value['hanger'] == 1) && ($value['max_drop_size'] >($weight) )){
                        $rider  = $value['id'];
                        break;
                   
                    }
                    elseif((($hanger) == 0) && ($value['max_drop_size'] > ($weight))){
                        $rider  = $value['id'];
                        break;
                      
                    }
                }
            }
        }

        if($rider == NULL){
            foreach ($h_rider as $key => $value) {
                if( $value['max_drop_size'] >($weight) ){
                    $rider  = $value['id'];
                    break;
                }
            }
        }
        // echo "<br> hanger: $hanger ";
        // echo "<br> Rider: $rider ";
        
        $data   = DB::table('route_plans')
                    ->where('route_plans.id',$route_id)
                    ->where('route_plans.rider_id',null)
                    ->update(['rider_id'=> $rider]);

        $rec    = DB::table('route_plans')
                    ->leftjoin('riders', 'riders.id', '=', 'route_plans.rider_id')
                    ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                    ->select(
                                'riders.max_loc',
                                'riders.max_pick',
                                'riders.color_code',
                                'vehicle_types.hanger',
                                'route_plans.rider_id',
                                'riders.max_drop_size',
                                'riders.max_drop_weight'
                            )
                    ->where('route_plans.id',$route_id)
                    ->first();

        return $rec;
    }

    public function fn_sort($rec){
        $unsorted_by_rider  = collect($rec)
                                ->sortBy('rider_id')
                                // ->sortBy('area_id')
                                ->sortBy('zone_id')
                                ->groupBy('rider_id')
                                ->toArray();

        $sorted_by_rider    = $unsorted_by_rider;
        // $len                = count($sorted_by_rider);
        $inputs             = array();

        foreach ($sorted_by_rider as $key => $value) {
            $inputs2             = array();
            $sorted_by_tslot    = collect($value)
                                    // ->sortBy('latitude')
                                    // ->sortBy('longitude')
                                    ->sortBy('timeslot_id')
                                    // ->sortBy('zone_id')
                                    ->groupBy('timeslot_id')
                                    ->toArray();
                                    
            foreach ($sorted_by_tslot as $k => $v) {
                $sorted_by_tslot2    = collect($v)
                                        // ->sortBy('latitude')
                                        // ->sortBy('longitude')
                                        // ->sortBy('timeslot_id')
                                        ->sortBy('zone_id')
                                        ->groupBy('zone_id')
                                        ->toArray();
                $inputs2[]          =  $sorted_by_tslot2;
            }
            $inputs[]           =  $inputs2;
            // $inputs[]           =  $sorted_by_tslot;

        }

        // $len1 = count($inputs);
        $recd = array();
        foreach ($inputs as $key1 => $record1) {
            foreach ($record1 as $key2 => $record2) {
                foreach ($record2 as $key3 => $record3) {
                    foreach ($record3 as $key4 => $record4) {
                        $recd[] =  $record4;
                    }
                    // $recd[] =  $record3;
                }
            }
        }

        // dd($recd);
        return $recd;
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }
 
    public function edit($id)
    {
       

    }

    public function update(Request $request, $id)
    {
    }

    public function destroy(Request $request)
    { 
    }

}
