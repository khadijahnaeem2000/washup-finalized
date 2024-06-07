<?php

namespace App\Http\Controllers;
use DB;
use Auth;
use Validator;
use DataTables;
use Carbon\Carbon;
use App\Models\Rider;
use App\Models\Route_plan;
use App\Models\Rider_has_zone;
use App\Models\Distribution_hub;
use App\Models\Rider_history;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;


class Fuel_ReportController extends Controller
{
     public $today;
    function __construct()
    {
         $this->middleware('permission:report-list', ['only' => ['index','show']]);
         $this->middleware('permission:report-create', ['only' => ['create','store']]);
         $this->middleware('permission:report-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:report-delete', ['only' => ['destroy']]);
         $this->today =  date('Y-m-d');
    }


    public function fetch_riders(Request $request)
    {
        if($request->ajax()){
           
            $riders     = DB::table('hub_has_riders')
                            ->leftjoin('riders', 'riders.id', '=', 'hub_has_riders.rider_id')
                            ->select('riders.id','riders.name')
                            ->where('hub_has_riders.hub_id',$request->hub_id)
                            ->pluck("riders.name","riders.id")
                            ->all();
            if ($riders){
                $data   = view('fuel_report.ajax-riders',compact('riders'))->render();
                return response()->json(['data'=>$data]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
        }

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
        return view('fuel_report.index',
                        compact('hubs')
                    );
    }



        public function list(Request $request)
         {
        $validator = Validator::make($request->all(),
            [
                'hub_id'                => 'required|min:1',
                'from_date'             => 'required|date',
                // 'to_date'               => 'required|date|after:from_date',
                'to_date'               => 'required|date',
                'rider_id'              => 'required|min:1',
            ],
            [
                'hub_id.required'       =>'Please select at least one hub',
                'rider_id.required'     =>'Please select rider',
                // 'to_date.after'         =>'Please select "To date" greater than "From date" ',
            ]
        );

        if ($validator->passes()) {
            $to             = $request['to_date'];
            $from           = $request['from_date'];
            $hub_id         = $request['hub_id'];
            $rider_id       = $request['rider_id'];
            if($to < $from){
                return response()->json(['error'=>[0=>'Please select "To date" greater than "From date" ']]);
            }
            $orders         = DB::table('route_plans')
                                // ->orderBy('route_plans.created_at','ASC')
                                ->leftjoin('riders', 'riders.id', '=', 'route_plans.rider_id')
                                ->select(
                                            // 'route_plans.updated_at',
                                            'riders.name as rider_name',
                                            DB::raw('DATE_FORMAT(route_plans.updated_at, "%Y-%m-%d") as dt')
                                        )
                                ->where('route_plans.hub_id', $hub_id)
                                ->where('route_plans.rider_id', $rider_id)
                                ->whereDate('route_plans.updated_at','>=', $from)  
                                ->whereDate('route_plans.updated_at','<=', $to)  
                                // ->whereBetween('route_plans.updated_at', [$from, $to])  
                                
                                ->whereNull('route_plans.is_canceled')
                                ->groupBy('riders.name','dt')
                                ->get();
             $rider = DB::table('riders')
                    ->where('id', $rider_id)
                    ->value('rider_incentives');
                    $rider_name = DB::table('riders')
                    ->where('id', $rider_id)
                    ->value('name');
                $pickup_rate = DB::table('rider_incentives')
                    ->where('id', $rider)
                    ->value('pickup_rate');
                $rider_incentive_name = DB::table('rider_incentives')
                    ->where('id', $rider)
                    ->value('name');
                
                $dropoff_rate = DB::table('rider_incentives')
                    ->where('id', $rider)
                    ->value('drop_rate');
                $pickdrop_rate = DB::table('rider_incentives')
                    ->where('id', $rider)
                    ->value('pickdrop_rate');    
        if(!($orders->isEmpty())){
                $ord            = [];
                foreach ($orders as $key => $value) {
                    $value->rider_id            = $rider_id;    
                    $value->date                = $value->dt; // $value->created_at; 
                    $dt                         = $value->dt; //date('Y-m-d', strtotime($value->created_at));
                    $value->dt                  = $value->dt; // date('Y-m-d', strtotime($value->created_at));
                    $value->plan_date           = $value->dt; // date('d/m/Y', strtotime($value->created_at));
                    $start_reading = DB::table('rider_histories')
                    ->where('rider_id', $rider_id)
                    ->whereDate('created_at', $dt)
                    ->value('start_reading');
                    $end_reading = DB::table('rider_histories')
                    ->where('rider_id', $rider_id)
                    ->whereDate('created_at', $dt)
                    ->value('end_reading');
                    $meter_id = DB::table('rider_histories')
                    ->where('rider_id', $rider_id)
                    ->whereDate('created_at', $dt)
                    ->value('id');
                    $lock = DB::table('rider_histories')
                    ->where('rider_id', $rider_id)
                    ->whereDate('created_at', $dt)
                    ->value('lock');
                    $value->rider_incentive_name= $rider_incentive_name;
                    $value->start_reading       = $start_reading;
                    $value->pickup_rate         = $pickup_rate;
                    $value->dropoff_rate        = $dropoff_rate;
                    $value->pickdrop_rate       = $pickdrop_rate;
                    $value->end_reading         = $end_reading;
                    $value->meter_id            = $meter_id;
                    $value->a_dist              = $this->fn_calc_covered_kms($rider_id, $dt); //covered distance
                    
                    $uniqueLocations            = $this->fn_pickup_count($hub_id, $rider_id, $dt);

                    $value->pick_up             = $uniqueLocations['pickup'];
                    $value->drop_off            = $uniqueLocations['dropoff'];
                    $value->pick_drop           = $uniqueLocations['pickdrop'];
                    // $status_id                  = 1;
                    // $value->pick_up             = $this->fn_pickup_count($status_id, $hub_id, $rider_id, $dt); // required distance
                    // $status_id                  = 2;
                    // $value->drop_off            = $this->fn_pickup_count($status_id, $hub_id, $rider_id, $dt);
                    // $status_id                  = 3;
                    // $value->pick_drop           = $this->fn_pickup_count($status_id, $hub_id, $rider_id, $dt);
                    $value->a_loc               = $value->pick_up + $value->drop_off + $value->pick_drop;
                    $value->lockStatus          = $lock;
                    $ord[$key]              = $value;
                }
                // dd($orders);
                $rec = collect($ord);
                // dd($rec);
                if(!($orders->isEmpty())){
                    $details    = view('fuel_report.report_table',
                                      compact('rec'))
                                    ->render();
                    return response()->json(['data'=>$orders,'details'=>$details,'rider_incentive_name'=>$rider_incentive_name,'rider_name'=>$rider_name,'from'=>$from,'to'=>$to]);
                }else{
                    return response()->json(['error'=>[0=>"Data not found"  ]]);
                }
            }else{
                return response()->json(['error'=>[0=>"Data not found"  ]]);
            }
            
        }else{
            return response()->json(['error'=>$validator->errors()->all()]);
        }
        
    }





                    public function fn_pickup_count($hub_id, $rider_id, $dt)
{
    // Fetch all route plans for the given date, hub, and rider
    $routePlans = DB::table('route_plans')
                    ->where('schedule', 1)
                    ->whereDate('updated_at', $dt)
                    ->where('rider_id', $rider_id)
                    ->where('hub_id', $hub_id)
                    ->where('is_canceled', null)
                    ->get();

    $customerActions = [];

    // Loop through each route plan to categorize actions
    foreach ($routePlans as $route) {
        $orderId = $route->order_id;
        $statusId = $route->status_id;

        $customerId = DB::table('orders')
                        ->where('id', $orderId)
                        ->value('customer_id');

        // Skip if customer ID is null
        if (is_null($customerId)) {
            continue;
        }

        // Initialize customer's actions if not already present
        if (!isset($customerActions[$customerId])) {
            $customerActions[$customerId] = [
                'pickup' => false,
                'dropoff' => false,
                'pickdrop' => false
            ];
        }

        // Update customer actions based on status
        if ($statusId == 1) {
            $customerActions[$customerId]['pickup'] = true;
        } elseif ($statusId == 2) {
            $customerActions[$customerId]['dropoff'] = true;
        } elseif ($statusId == 3) {
            $customerActions[$customerId]['pickdrop'] = true;
        }
    }

    $pickdropCount = 0;
    $pickupCount = 0;
    $dropoffCount = 0;

    // Determine the final counts based on customer actions
    foreach ($customerActions as $actions) {
        if ($actions['pickdrop'] || ($actions['pickup'] && $actions['dropoff'])) {
            $pickdropCount++;
        } else {
            if ($actions['pickup']) {
                $pickupCount++;
            }
            if ($actions['dropoff']) {
                $dropoffCount++;
            }
        }
    }

    return [
        'pickdrop' => $pickdropCount,
        'pickup' => $pickupCount,
        'dropoff' => $dropoffCount
    ];
}    

    public function fn_calc_covered_kms($rider_id, $dt)
    {
        $tmp = 0;
        if( (isset($rider_id)) && (isset($dt))){
            $record         = DB::table('rider_histories')
                                ->select(
                                            'rider_histories.start_reading',
                                            'rider_histories.end_reading',
                                        )
                                ->whereDate('rider_histories.plan_date',$dt)
                                ->where('rider_histories.rider_id',$rider_id)
                                ->first();  
            if( (isset($record->start_reading)) && (isset($record->end_reading)) ){
                $tmp = ($record->end_reading) - ($record->start_reading);
            }
        }
        return $tmp;
    }

    public function fn_count_polybags($hub_id, $rider_id, $dt,$state)
    {
        if((isset($hub_id)) && (isset($rider_id)) && (isset($dt))){
            $orders         = DB::table('route_plans')
                                ->select(
                                            'route_plans.id',
                                            'route_plans.order_id',
                                        )
                                // ->where('route_plans.status_id',$status_id)
                                ->distinct('route_plans.order_id')
                                // ->where('route_plans.schedule',0)
                                // ->whereDate('route_plans.created_at',$dt)
                                ->whereDate('route_plans.updated_at',$dt)
                                ->where('route_plans.rider_id',$rider_id)
                                ->where('route_plans.hub_id',$hub_id)
                                ->get();  
                             

            $count = 0;
            if($state == 'all'){
                 // getting all polybags only.
                foreach ($orders as $key => $value) {
                    $polybags           = DB::table('order_has_bags')
                                            ->where('order_has_bags.order_id', $value->order_id)
                                            ->count();
                    $count              += $polybags;
                }
            
            }else{
                // getting scanned polybags only.
                foreach ($orders as $key => $value) {
                    $polybags           = DB::table('order_has_bags')
                                            ->where('order_has_bags.order_id', $value->order_id)
                                            ->where('order_has_bags.tag_scanned', 1)
                                            ->count();
                    $count              += $polybags;
                }

            }

            

            if(!(isset($count))){
                $count       = 0;
            }
            return $count;
        }else{
            return 0;
        }
    }

    public function fn_get_service_amount($order_id){
        $data                   = "";
        $tot                    = 0;
        $all_services           =  DB::table('orders')
                                ->leftjoin('order_has_services', 'order_has_services.order_id', '=', 'orders.id')
                                // ->leftjoin('customer_has_services', 'customer_has_services.customer_id', '=', 'orders.customer_id')
                                ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                ->where('order_has_services.order_id',$order_id)
                                // ->where('services.unit_id',2)
                                ->select(
                                            'orders.id',
                                            'services.unit_id',
                                            'orders.customer_id',
                                            'order_has_services.qty',
                                            'order_has_services.weight',
                                            'order_has_services.service_id',
                                        )
                                ->get();

                                // dd($all_services);
        foreach ($all_services as $key => $value) {
            if($value->unit_id == 1){
                 // If unit is KG:1 then rate will be based on service weight
                $data           =  DB::table('customer_has_services')
                                    ->where('customer_has_services.customer_id',$value->customer_id)
                                    ->where('customer_has_services.service_id',$value->service_id)
                                    ->select(
                                                'customer_has_services.service_id',
                                                'customer_has_services.service_rate',
                                            )
                                    ->first();
                $tot            += (($value->weight) * ( $data->service_rate));

                //                 echo "Qty: ". $value->weight. "<br>";
                // echo "<pre>";
                // print_r($data);
                // echo "</pre>";
            }else if($value->unit_id == 2){
                // If unit is item:2 then rate will be based on item rate which will be different for all items

                $items      =  DB::table('order_has_items')
                                    // ->leftjoin('order_has_items', 'order_has_items.service_id', '=', 'order_has_services.service_id')
                                    // ->leftjoin('customer_has_items', 'customer_has_items.item_id', '=', 'order_has_items.item_id')
                                    ->where('order_has_items.order_id',$value->id)
                                    ->where('order_has_items.service_id',$value->service_id)
                                    
                                    // ->where('customer_has_items.customer_id',$value->customer_id)
                                    
                                    ->select(
                                                'order_has_items.order_id',
                                                'order_has_items.item_id',
                                                'order_has_items.pickup_qty',
                                                'order_has_items.service_id as service_id'
                                            )
                                    ->get();
                                    // dd($items);
                foreach ($items as $item_key => $item_value) {

                    $rec      =  DB::table('customer_has_items')
                                    // ->where('customer_has_items.order_id',$value->id)
                                    ->where('customer_has_items.service_id',$item_value->service_id)
                                    ->where('customer_has_items.item_id',$item_value->item_id)
                                    ->where('customer_has_items.customer_id',$value->customer_id)
                                    ->select(
                                                // 'customer_has_items.order_id',
                                                'customer_has_items.item_id',
                                                'customer_has_items.item_rate',
                                            )
                                    ->first();

                                    // dd($rec);
                    // echo $item_key."pick_up:  ". ($item_value->pickup_qty) . "<br>";
                    $tot   +=   (($item_value->pickup_qty) * ($rec->item_rate));
                }
                // dd($tot);
            }else if($value->unit_id == 3){
                // If unit is piece:3 then rate will be based on rate which will be same for all items
                $data           =  DB::table('customer_has_services')
                                    ->where('customer_has_services.customer_id',$value->customer_id)
                                    ->where('customer_has_services.service_id',$value->service_id)
                                    ->select(
                                                'customer_has_services.service_id',
                                                'customer_has_services.service_rate',
                                            )
                                    ->first();
                $tot            += (($value->qty) * ( $data->service_rate));
            }
           
        }

        return $tot;
                           

    }
  
    public function fn_count_polybags_orderly($order_id, $state){
        if( (isset($order_id)) && (isset($state)) ){
            if($state == 'scan'){
                $polybags           = DB::table('order_has_bags')
                                        ->where('order_has_bags.order_id', $order_id)
                                        ->where('order_has_bags.tag_scanned', 1)
                                        ->count();
               
            }else{
                $polybags           = DB::table('order_has_bags')
                                        ->where('order_has_bags.order_id', $order_id)
                                        ->count();
            }
            if(!(isset($polybags))){
                return 0;
            }else{
                return $polybags;
            } 
        }else{
            return 0;
        }
    }

    public function fn_order_count($hub_id, $rider_id, $dt){

        if((isset($hub_id)) && (isset($rider_id)) && (isset($dt))){
            $orders         = DB::table('route_plans')
                                ->select(
                                            DB::raw('count(*) as loc')
                                        )
                                // ->where('route_plans.status_id',$status_id)
                                ->whereNull('route_plans.is_canceled')
                                ->where('route_plans.schedule',1)
                                ->whereDate('route_plans.updated_at',$dt)
                                ->where('route_plans.rider_id',$rider_id)
                                ->where('route_plans.hub_id',$hub_id)
                                ->first();  

            return ($orders->loc);
        }else{
            return 0;
        }
    }

    public function fn_calc_receiving_orderly($order_id, $rider_id, $dt){

        if((isset($order_id)) && (isset($rider_id)) && (isset($dt))){
            $rec            = DB::table('customer_has_wallets')
                                ->select(
                                            'customer_has_wallets.in_amount'
                                        )
                                ->where('customer_has_wallets.order_id',$order_id)
                                ->whereDate('customer_has_wallets.created_at',$dt)
                                ->where('customer_has_wallets.rider_id',$rider_id)
                                ->first(); 

            if((isset($rec)) && ($rec->in_amount != null)){
                return ($rec->in_amount);
            }else{
                return 0;
            } 
        }else{
            return 0;
        }
    }

    public function fn_calc_receiving_ridely($ride_id, $rider_id, $dt){
        if((isset($ride_id)) && (isset($rider_id)) && (isset($dt))){
            $rec            = DB::table('customer_has_wallets')
                                ->select( 'customer_has_wallets.rider_id',
                                            'customer_has_wallets.in_amount'
                                        )
                                ->where('customer_has_wallets.ride_id',$ride_id)
                                ->whereDate('customer_has_wallets.created_at',$dt)
                                ->where('customer_has_wallets.rider_id',$rider_id)
                                ->first(); 

            if((isset($rec)) && ($rec->in_amount != null)){
                return ($rec->in_amount);
            }else{
                return 0;
            } 
        }else{
            return 0;
        }
    }

    public function fn_calc_kms($hub_id, $rider_id, $dt,$col){

        if((isset($hub_id)) && (isset($rider_id)) && (isset($dt))){
            if($col == 'req_dist'){
                $orders         = DB::table('route_plans')
                                    ->select(
                                                DB::raw('sum(route_plans.req_dist) as dist')
                                            )
                                    // ->where('route_plans.schedule',0)
                                    // ->whereDate('route_plans.created_at',$dt)
                                    ->whereDate('route_plans.updated_at',$dt)
                                    ->where('route_plans.rider_id',$rider_id)
                                    // ->where('route_plans.hub_id',$hub_id)
                                    ->first(); 
            }else{
                $orders         = DB::table('route_plans')
                                    ->select(
                                                DB::raw('sum(route_plans.cov_dist) as dist')
                                            )
                                    // ->where('route_plans.schedule',0)
                                    // ->whereDate('route_plans.created_at',$dt)
                                    ->whereDate('route_plans.updated_at',$dt)
                                    ->where('route_plans.rider_id',$rider_id)
                                    // ->where('route_plans.hub_id',$hub_id)
                                    ->first(); 
            }
             
            if((isset($orders)) && ($orders->dist != null)){
                return ($orders->dist);
            }else{
                return 0;
            }
            
        }else{
            return 0;
        }

       
    }

    public function fn_calc_receiving($rider_id, $dt){

        if( (isset($rider_id)) && (isset($dt)) ){
            $orders         = DB::table('customer_has_wallets')
                                ->select(
                                            DB::raw('sum(customer_has_wallets.in_amount) as in_amount')
                                        )
                                ->whereDate('customer_has_wallets.created_at',$dt)
                                ->where('customer_has_wallets.rider_id',$rider_id)
                                ->first();  
            if((isset($orders)) && ($orders->in_amount != null)){
                return ($orders->in_amount);
            }else{
                return 0;
            }
            
        }else{
            return 0;
        }

       
    }

    public function fn_add_tax($amount){
        $d_amount = 0;  $vat_amount =0; 
        $vat            = DB::table('vats')
                            ->select(
                                        'vats.vat'
                                    )
                            ->first();

        $d_charges      = DB::table('delivery_charges')
                            ->select(
                                        'delivery_charges.order_amount',
                                        'delivery_charges.delivery_charges'
                                    )
                            ->first();
        if($d_charges){
            $order_amount  = $d_charges->order_amount; 
            if($amount > $order_amount){
                $d_amount =  $d_charges->delivery_charges;
            }
        }

        if($vat){
            $val        = $vat->vat;
            $vat_amount = ($amount * $val / 100);
        }

        $amount += ($d_amount + $vat_amount);
       return $amount;

    }

    public function fn_get_addon_amount($order_id){
        $amount = 0;
        $all_addons             =  DB::table('order_has_addons')
                                    ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                    ->leftjoin('order_has_items', 'order_has_items.id', '=', 'order_has_addons.ord_itm_id')
                                    ->where('order_has_addons.order_id',$order_id)
                                    ->select(
                                                'addons.rate',
                                                'order_has_addons.id',
                                                'order_has_items.pickup_qty',
                                                'order_has_addons.order_id',
                                            )
                                    ->get();

        foreach ($all_addons as $key => $value) {
            for ($i=0; $i < $value->pickup_qty; $i++) { 
                $amount += $value->rate;
            }
        }
        return $amount;
                           

    }
    
   public function edit($id){
      
       
            
       
         $user_id = auth()->id();
         
        $rec                    = explode("-",$id);
        $date                   = date('Y-m-d', $rec[0]);

       
        $rider_id               = $rec[1];

        $tot_order_bill_amnt    = 0;
        $tot_order_recd_amnt    = 0;

        $tot_ride_bill_amnt     = 0;
        $tot_ride_recd_amnt     = 0;
           

        if( (isset($date)) && (isset($rider_id))){
         
            $record     = DB::table('rider_histories')
                            ->leftjoin('riders', 'riders.id', '=', 'rider_histories.rider_id')
                            ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                            ->leftjoin('users', 'users.id', '=', 'rider_histories.updated_by')
                            ->select(
                                        'rider_histories.*',
                                        'riders.name as rider_name',
                                        'vehicle_types.name as vehicle_name',
                                        'users.name as UpdateName'
                                    )
                            ->where('rider_histories.rider_id', $rider_id)
                            ->whereDate('rider_histories.plan_date',$date)
                            ->first();
            $pickdrop_orders         = DB::table('route_plans')
                            ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                            ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                            // ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                            // ->leftjoin('customer_has_wallets', 'customer_has_wallets.order_id', '=', 'route_plans.order_id')
                            ->orderBy('orders.customer_id')
                            ->select(
                                        'route_plans.*',
                                        'orders.pickup_address',
                                        'orders.customer_id as customerID',
                                        'orders.delivery_address',
                                        'statuses.name as status_name',
                                        'customers.name as customer_name',
                                        'customers.contact_no as customer_contact_no',
                                        // 'customer_has_addresses.address as customer_address',
                                        'time_slots.start_time',
                                        'time_slots.end_time',
                                            DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                    )
                            // ->where('route_plans.schedule', 0) 
                            
                            ->whereDate('route_plans.updated_at',$date)
                            ->whereNull('route_plans.is_canceled')
                            ->where('route_plans.rider_id', $rider_id)
                            ->where('route_plans.status_id',3)
                            ->get();
                            $uniqueCustomerPickdrop = $pickdrop_orders->pluck('customerID')->unique()->toArray();    
                              // dd($uniqueCustomerPickdrop);

                $OrderPickdrop = [];


                foreach ($uniqueCustomerPickdrop as $customerId) {
                    $firstOrder = DB::table('route_plans')
                        ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                        ->select('orders.id as order_id')
                        ->where('route_plans.rider_id', $rider_id)
                        ->where('orders.customer_id', $customerId)
                        ->where('orders.customer_id', $customerId)
                        ->where('route_plans.status_id', 3)
                        ->orderBy('route_plans.updated_at')
                        ->first();

                    if ($firstOrder) {
                        $OrderPickdrop[$customerId] = $firstOrder->order_id;
                    }
                }
                //dd($OrderPickdrop);
                

                $pick_orders         = DB::table('route_plans')
                            ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                            ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                            // ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                            // ->leftjoin('customer_has_wallets', 'customer_has_wallets.order_id', '=', 'route_plans.order_id')
                            ->orderBy('orders.customer_id')
                            ->select(
                                        'route_plans.*',
                                        'orders.pickup_address',
                                        'orders.customer_id as customerID',
                                        'orders.delivery_address',
                                        'statuses.name as status_name',
                                        'customers.name as customer_name',
                                        'customers.contact_no as customer_contact_no',
                                        // 'customer_has_addresses.address as customer_address',
                                        'time_slots.start_time',
                                        'time_slots.end_time',
                                            DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                    )
                            // ->where('route_plans.schedule', 0) 
                            
                            ->whereDate('route_plans.updated_at',$date)
                            ->whereNull('route_plans.is_canceled')
                            ->where('route_plans.rider_id', $rider_id)
                            ->where('route_plans.status_id',1)
                            ->get();
                             $uniqueCustomerPick = $pick_orders->pluck('customerID')->unique()->toArray();
                             
                             //dd($uniqueCustomerPick);
                $OrderPick = [];


                foreach ($uniqueCustomerPick as $customerId) {
                    $firstOrder = DB::table('route_plans')
                        ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                        ->select('orders.id as order_id')
                        ->where('route_plans.rider_id', $rider_id)
                        ->where('orders.customer_id', $customerId)
                        ->where('orders.customer_id', $customerId)
                        ->where('route_plans.status_id', 1)
                        ->orderBy('route_plans.updated_at')
                        ->first();

                    if ($firstOrder) {
                        $OrderPick[$customerId] = $firstOrder->order_id;
                    }
                }
                ///dd($OrderPick);


                $drop_orders         = DB::table('route_plans')
                            ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                            ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                            // ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                            // ->leftjoin('customer_has_wallets', 'customer_has_wallets.order_id', '=', 'route_plans.order_id')
                            ->orderBy('orders.customer_id')
                            ->select(
                                        'route_plans.*',
                                        'orders.pickup_address',
                                        'orders.customer_id as customerID',
                                        'orders.delivery_address',
                                        'statuses.name as status_name',
                                        'customers.name as customer_name',
                                        'customers.contact_no as customer_contact_no',
                                        // 'customer_has_addresses.address as customer_address',
                                        'time_slots.start_time',
                                        'time_slots.end_time',
                                            DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                    )
                            // ->where('route_plans.schedule', 0) 
                            
                            ->whereDate('route_plans.updated_at',$date)
                            ->whereNull('route_plans.is_canceled')
                            ->where('route_plans.rider_id', $rider_id)
                            ->where('route_plans.status_id',2)
                            ->get();
                             $uniqueCustomerDrop = $drop_orders->pluck('customerID')->unique()->toArray();
                             
                             //dd($uniqueCustomerDrop);
                $OrderDrop = [];
                foreach ($uniqueCustomerDrop as $customerId) {
                    $firstOrder = DB::table('route_plans')
                        ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                        ->select('orders.id as order_id')
                        ->where('route_plans.rider_id', $rider_id)
                        ->where('orders.customer_id', $customerId)
                        ->where('orders.customer_id', $customerId)
                        ->where('route_plans.status_id', 2)
                        ->orderBy('route_plans.updated_at')
                        ->first();

                    if ($firstOrder) {
                        $OrderDrop[$customerId] = $firstOrder->order_id;
                    }
                }
                //dd($OrderDrop);

                $unique_orders = [];

                // Populate the dictionary with Pickdrop data
                foreach ($uniqueCustomerPickdrop as $customer_id) {
                    $order_id = $OrderPickdrop[$customer_id] ?? null;
                    if ($order_id !== null) {
                        $unique_orders[$customer_id] = $order_id;
                    }
                }

                // Add Pickup data to the dictionary only if the customer ID is not already present and is not null
                foreach ($uniqueCustomerPick as $customer_id) {
                    $order_id = $OrderPick[$customer_id] ?? null;
                    if ($customer_id !== null && !array_key_exists($customer_id, $unique_orders) && $order_id !== null) {
                        $unique_orders[$customer_id] = $order_id;
                    }
                }

                // Extract the customer IDs and order IDs from the dictionary
                $result_customer_ids = array_keys($unique_orders);
                $result_order_ids = array_values($unique_orders);
                foreach ($uniqueCustomerDrop as $customer_id) {
                        $order_id = $OrderDrop[$customer_id] ?? null;
                        if ($customer_id !== null && !array_key_exists($customer_id, $unique_orders) && $order_id !== null) {
                            $unique_orders[$customer_id] = $order_id;
                        }
                    }

                    // Final extraction of customer IDs and order IDs
                    $resultdrop_customer_ids = array_keys($unique_orders);
                    $resultdrop_order_ids = array_values($unique_orders);
                    //dd($resultdrop_order_ids);
            // setting up the orders from route plans table
            $orders     = DB::table('route_plans')
                            ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                            ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                            // ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                            // ->leftjoin('customer_has_wallets', 'customer_has_wallets.order_id', '=', 'route_plans.order_id')
                            ->orderBy('orders.customer_id')
                            ->select(
                                        'route_plans.*',
                                        'orders.pickup_address',
                                        'orders.delivery_address',
                                        'statuses.name as status_name',
                                        'customers.name as customer_name',
                                        'customers.contact_no as customer_contact_no',
                                        // 'customer_has_addresses.address as customer_address',
                                        'time_slots.start_time',
                                        'time_slots.end_time',
                                            DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                    )
                            // ->where('route_plans.schedule', 0) 
                            
                            ->whereDate('route_plans.updated_at',$date)
                            ->whereNull('route_plans.is_canceled')
                            ->where('route_plans.rider_id', $rider_id)
                            ->whereIn('route_plans.order_id', $resultdrop_order_ids)
                            ->get()
                            ->all();
               

            // calculating other columns for orders

            

            foreach ($orders as $key => $value) {
                // $orders[$key]->bill_amount          = ($this->fn_get_service_amount($value->order_id)); 

                // $service_tot                        = $this->fn_get_service_amount($value->order_id);
                // $addon_tot                          = $this->fn_get_addon_amount($value->order_id);
                // $tot                                = ( $service_tot + $addon_tot);
                // $orders[$key]->bill                 = ($this->fn_add_tax($tot)); 
                if(($value->status_id) !=  1){
                    $chw                                = DB::table('customer_has_wallets')
                                                            ->select('customer_has_wallets.out_amount')
                                                            ->where('customer_has_wallets.order_id',  $value->order_id)
                                                            // ->where('customer_has_wallets.rider_id', $rider_id)
                                                            ->first();

                }


                if(isset($chw->out_amount)){
                    $bill = $chw->out_amount; 
                }else{
                    $bill = 0;
                }
                $orders[$key]->bill                 = $bill; 

                // $orders[$key]->bill                 = ($this->fn_add_tax($orders[$key]->bill_amount)); 
                $orders[$key]->scanned_bags         = ($this->fn_count_polybags_orderly($value->order_id,'scan')); 
                $orders[$key]->allotted_bags        = ($this->fn_count_polybags_orderly($value->order_id,'all')); 
                $orders[$key]->received_amount      = ($this->fn_calc_receiving_orderly($value->order_id, $value->rider_id, $date )); 
                
                $tot_order_bill_amnt               += $orders[$key]->bill;
                if(($orders[$key]->received_amount) < 0){
                    $tot_order_recd_amnt               = ($tot_order_recd_amnt + ((-1) * ($orders[$key]->received_amount)));
                }else{
                    $tot_order_recd_amnt               += $orders[$key]->received_amount;
                }
                
        
               
            }

            // setting up the paymend rides only from payment_rides table.
            $rides      = DB::table('payment_rides')
                            ->leftjoin('customers', 'customers.id', '=', 'payment_rides.customer_id')
                            ->leftjoin('statuses', 'statuses.id', '=', 'payment_rides.status_id')
                            ->leftjoin('time_slots', 'time_slots.id', '=', 'payment_rides.timeslot_id')
                            ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'payment_rides.address_id')
                            ->orderBy('payment_rides.customer_id')
                            ->select(
                                        'payment_rides.*',
                                        'statuses.name as status_name',
                                        'customers.name as customer_name',
                                        'customers.contact_no as customer_contact_no',
                                        'customer_has_addresses.address as customer_address',
                                        'time_slots.start_time',
                                        'time_slots.end_time',
                                            DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                    )
                            // ->where('route_plans.schedule', 0) 
                            ->where('payment_rides.rider_id', $rider_id)
                            ->whereDate('payment_rides.ride_date',$date)
                            ->get()
                            ->all();

            // calculating other columns for payment only rides

            foreach ($rides as $key => $value) {
                $rides[$key]->received_amount      = ($this->fn_calc_receiving_ridely($value->id,$value->rider_id,$date )); 
                
                // $tot_ride_bill_amnt               += $rides[$key]->bill;
                 if(($rides[$key]->bill) < 0){
                    $tot_ride_bill_amnt               = ($tot_ride_bill_amnt + ((-1) * ($rides[$key]->bill)));
                }else{
                    $tot_ride_bill_amnt               += $rides[$key]->bill;
                }
                
                $tot_ride_recd_amnt               += $rides[$key]->received_amount;
            }


           
            return view('fuel_report.edit',compact(
                                                'record',
                                                'orders',
                                                'rides', 
                                                'tot_order_bill_amnt',
                                                'tot_order_recd_amnt',
                                                'tot_ride_bill_amnt',
                                                'tot_ride_recd_amnt',
                                                'user_id'
                                            ));
            // if( (isset($orders)) && (isset($rides)) ){
            //     return view('reports.edit',compact('record','orders','rides'));
            // }else{
            //     return view('reports.edit',compact('record','orders'));
            // }
            
        }
        
        
       
    }
public function checkLockStatus(Request $request)
{
    $id = $request->input('id');
    $rider = $request->input('rider');
    $date = $request->input('date');

    $lockStatus = DB::table('rider_histories')
                    ->where('rider_id', $rider)
                    ->whereDate('plan_date', $date)
                    ->value('lock');
                    $StartReading = DB::table('rider_histories')
                    ->where('rider_id', $rider)
                    ->whereDate('plan_date', $date)
                    ->value('start_reading');
 $EndReading = DB::table('rider_histories')
                    ->where('rider_id', $rider)
                    ->whereDate('plan_date', $date)
                    ->value('end_reading');
                    if($StartReading === null && $EndReading === null){
                         return response()->json(['Nodata' => true]);
                    }
    if ($lockStatus === 1) {
        return response()->json(['locked' => true]);
    } else {
        return response()->json(['locked' => false]);
    }
}

    public function create(){
    }

    public function store(Request $request){
    }

   
 
 
    public function show($id){
         $user_id = auth()->id();
    
         
        $rec                    = explode("-",$id);
        $date                   = date('Y-m-d', $rec[0]);

       
        $rider_id               = $rec[1];

        $tot_order_bill_amnt    = 0;
        $tot_order_recd_amnt    = 0;

        $tot_ride_bill_amnt     = 0;
        $tot_ride_recd_amnt     = 0;

        if( (isset($date)) && (isset($rider_id))){
            $record     = DB::table('rider_histories')
                            ->leftjoin('riders', 'riders.id', '=', 'rider_histories.rider_id')
                            ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                            ->leftjoin('users', 'users.id', '=', 'rider_histories.updated_by')
                            ->select(
                                        'rider_histories.*',
                                        'riders.name as rider_name',
                                        'vehicle_types.name as vehicle_name',
                                        'users.name as UpdateName'
                                    )
                            ->where('rider_histories.rider_id', $rider_id)
                            ->whereDate('rider_histories.plan_date',$date)
                            ->first();
            $pickdrop_orders         = DB::table('route_plans')
                            ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                            ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                            // ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                            // ->leftjoin('customer_has_wallets', 'customer_has_wallets.order_id', '=', 'route_plans.order_id')
                            ->orderBy('orders.customer_id')
                            ->select(
                                        'route_plans.*',
                                        'orders.pickup_address',
                                        'orders.customer_id as customerID',
                                        'orders.delivery_address',
                                        'statuses.name as status_name',
                                        'customers.name as customer_name',
                                        'customers.contact_no as customer_contact_no',
                                        // 'customer_has_addresses.address as customer_address',
                                        'time_slots.start_time',
                                        'time_slots.end_time',
                                            DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                    )
                            // ->where('route_plans.schedule', 0) 
                            
                            ->whereDate('route_plans.updated_at',$date)
                            ->whereNull('route_plans.is_canceled')
                            ->where('route_plans.rider_id', $rider_id)
                            ->where('route_plans.status_id',3)
                            ->get();
                            $uniqueCustomerPickdrop = $pickdrop_orders->pluck('customerID')->unique()->toArray();    
                              // dd($uniqueCustomerPickdrop);

                $OrderPickdrop = [];


                foreach ($uniqueCustomerPickdrop as $customerId) {
                    $firstOrder = DB::table('route_plans')
                        ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                        ->select('orders.id as order_id')
                        ->where('route_plans.rider_id', $rider_id)
                        ->where('orders.customer_id', $customerId)
                        ->where('orders.customer_id', $customerId)
                        ->where('route_plans.status_id', 3)
                        ->orderBy('route_plans.updated_at')
                        ->first();

                    if ($firstOrder) {
                        $OrderPickdrop[$customerId] = $firstOrder->order_id;
                    }
                }
                //dd($OrderPickdrop);
                

                $pick_orders         = DB::table('route_plans')
                            ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                            ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                            // ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                            // ->leftjoin('customer_has_wallets', 'customer_has_wallets.order_id', '=', 'route_plans.order_id')
                            ->orderBy('orders.customer_id')
                            ->select(
                                        'route_plans.*',
                                        'orders.pickup_address',
                                        'orders.customer_id as customerID',
                                        'orders.delivery_address',
                                        'statuses.name as status_name',
                                        'customers.name as customer_name',
                                        'customers.contact_no as customer_contact_no',
                                        // 'customer_has_addresses.address as customer_address',
                                        'time_slots.start_time',
                                        'time_slots.end_time',
                                            DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                    )
                            // ->where('route_plans.schedule', 0) 
                            
                            ->whereDate('route_plans.updated_at',$date)
                            ->whereNull('route_plans.is_canceled')
                            ->where('route_plans.rider_id', $rider_id)
                            ->where('route_plans.status_id',1)
                            ->get();
                             $uniqueCustomerPick = $pick_orders->pluck('customerID')->unique()->toArray();
                             
                             //dd($uniqueCustomerPick);
                $OrderPick = [];


                foreach ($uniqueCustomerPick as $customerId) {
                    $firstOrder = DB::table('route_plans')
                        ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                        ->select('orders.id as order_id')
                        ->where('route_plans.rider_id', $rider_id)
                        ->where('orders.customer_id', $customerId)
                        ->where('orders.customer_id', $customerId)
                        ->where('route_plans.status_id', 1)
                        ->orderBy('route_plans.updated_at')
                        ->first();

                    if ($firstOrder) {
                        $OrderPick[$customerId] = $firstOrder->order_id;
                    }
                }
                ///dd($OrderPick);


                $drop_orders         = DB::table('route_plans')
                            ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                            ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                            // ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                            // ->leftjoin('customer_has_wallets', 'customer_has_wallets.order_id', '=', 'route_plans.order_id')
                            ->orderBy('orders.customer_id')
                            ->select(
                                        'route_plans.*',
                                        'orders.pickup_address',
                                        'orders.customer_id as customerID',
                                        'orders.delivery_address',
                                        'statuses.name as status_name',
                                        'customers.name as customer_name',
                                        'customers.contact_no as customer_contact_no',
                                        // 'customer_has_addresses.address as customer_address',
                                        'time_slots.start_time',
                                        'time_slots.end_time',
                                            DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                    )
                            // ->where('route_plans.schedule', 0) 
                            
                            ->whereDate('route_plans.updated_at',$date)
                            ->whereNull('route_plans.is_canceled')
                            ->where('route_plans.rider_id', $rider_id)
                            ->where('route_plans.status_id',2)
                            ->get();
                             $uniqueCustomerDrop = $drop_orders->pluck('customerID')->unique()->toArray();
                             
                             //dd($uniqueCustomerDrop);
                $OrderDrop = [];
                foreach ($uniqueCustomerDrop as $customerId) {
                    $firstOrder = DB::table('route_plans')
                        ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                        ->select('orders.id as order_id')
                        ->where('route_plans.rider_id', $rider_id)
                        ->where('orders.customer_id', $customerId)
                        ->where('orders.customer_id', $customerId)
                        ->where('route_plans.status_id', 2)
                        ->orderBy('route_plans.updated_at')
                        ->first();

                    if ($firstOrder) {
                        $OrderDrop[$customerId] = $firstOrder->order_id;
                    }
                }
                //dd($OrderDrop);

                $unique_orders = [];

                // Populate the dictionary with Pickdrop data
                foreach ($uniqueCustomerPickdrop as $customer_id) {
                    $order_id = $OrderPickdrop[$customer_id] ?? null;
                    if ($order_id !== null) {
                        $unique_orders[$customer_id] = $order_id;
                    }
                }

                // Add Pickup data to the dictionary only if the customer ID is not already present and is not null
                foreach ($uniqueCustomerPick as $customer_id) {
                    $order_id = $OrderPick[$customer_id] ?? null;
                    if ($customer_id !== null && !array_key_exists($customer_id, $unique_orders) && $order_id !== null) {
                        $unique_orders[$customer_id] = $order_id;
                    }
                }

                // Extract the customer IDs and order IDs from the dictionary
                $result_customer_ids = array_keys($unique_orders);
                $result_order_ids = array_values($unique_orders);
                foreach ($uniqueCustomerDrop as $customer_id) {
                        $order_id = $OrderDrop[$customer_id] ?? null;
                        if ($customer_id !== null && !array_key_exists($customer_id, $unique_orders) && $order_id !== null) {
                            $unique_orders[$customer_id] = $order_id;
                        }
                    }

                    // Final extraction of customer IDs and order IDs
                    $resultdrop_customer_ids = array_keys($unique_orders);
                    $resultdrop_order_ids = array_values($unique_orders);                   

            // setting up the orders from route plans table
            $orders     = DB::table('route_plans')
                            ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                            ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                            // ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                            // ->leftjoin('customer_has_wallets', 'customer_has_wallets.order_id', '=', 'route_plans.order_id')
                            ->orderBy('orders.customer_id')
                            ->select(
                                        'route_plans.*',
                                        'orders.pickup_address',
                                        'orders.delivery_address',
                                        'statuses.name as status_name',
                                        'customers.name as customer_name',
                                        'customers.contact_no as customer_contact_no',
                                        // 'customer_has_addresses.address as customer_address',
                                        'time_slots.start_time',
                                        'time_slots.end_time',
                                            DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                    )
                            // ->where('route_plans.schedule', 0) 
                            
                            ->whereDate('route_plans.updated_at',$date)
                            ->whereNull('route_plans.is_canceled')
                            ->whereIn('route_plans.order_id', $resultdrop_order_ids)
                            ->where('route_plans.rider_id', $rider_id)
                            ->get()
                            ->all();
               

            // calculating other columns for orders

            

            foreach ($orders as $key => $value) {
                // $orders[$key]->bill_amount          = ($this->fn_get_service_amount($value->order_id)); 

                // $service_tot                        = $this->fn_get_service_amount($value->order_id);
                // $addon_tot                          = $this->fn_get_addon_amount($value->order_id);
                // $tot                                = ( $service_tot + $addon_tot);
                // $orders[$key]->bill                 = ($this->fn_add_tax($tot)); 
                if(($value->status_id) !=  1){
                    $chw                                = DB::table('customer_has_wallets')
                                                            ->select('customer_has_wallets.out_amount')
                                                            ->where('customer_has_wallets.order_id',  $value->order_id)
                                                            // ->where('customer_has_wallets.rider_id', $rider_id)
                                                            ->first();

                }


                if(isset($chw->out_amount)){
                    $bill = $chw->out_amount; 
                }else{
                    $bill = 0;
                }
                $orders[$key]->bill                 = $bill; 

                // $orders[$key]->bill                 = ($this->fn_add_tax($orders[$key]->bill_amount)); 
                $orders[$key]->scanned_bags         = ($this->fn_count_polybags_orderly($value->order_id,'scan')); 
                $orders[$key]->allotted_bags        = ($this->fn_count_polybags_orderly($value->order_id,'all')); 
                $orders[$key]->received_amount      = ($this->fn_calc_receiving_orderly($value->order_id, $value->rider_id, $date )); 
                
                $tot_order_bill_amnt               += $orders[$key]->bill;
                if(($orders[$key]->received_amount) < 0){
                    $tot_order_recd_amnt               = ($tot_order_recd_amnt + ((-1) * ($orders[$key]->received_amount)));
                }else{
                    $tot_order_recd_amnt               += $orders[$key]->received_amount;
                }
                
        
               
            }

            // setting up the paymend rides only from payment_rides table.
            $rides      = DB::table('payment_rides')
                            ->leftjoin('customers', 'customers.id', '=', 'payment_rides.customer_id')
                            ->leftjoin('statuses', 'statuses.id', '=', 'payment_rides.status_id')
                            ->leftjoin('time_slots', 'time_slots.id', '=', 'payment_rides.timeslot_id')
                            ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'payment_rides.address_id')
                            ->orderBy('payment_rides.customer_id')
                            ->select(
                                        'payment_rides.*',
                                        'statuses.name as status_name',
                                        'customers.name as customer_name',
                                        'customers.contact_no as customer_contact_no',
                                        'customer_has_addresses.address as customer_address',
                                        'time_slots.start_time',
                                        'time_slots.end_time',
                                            DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                    )
                            // ->where('route_plans.schedule', 0) 
                            ->where('payment_rides.rider_id', $rider_id)
                            ->whereDate('payment_rides.ride_date',$date)
                            ->get()
                            ->all();

            // calculating other columns for payment only rides

            foreach ($rides as $key => $value) {
                $rides[$key]->received_amount      = ($this->fn_calc_receiving_ridely($value->id,$value->rider_id,$date )); 
                
                // $tot_ride_bill_amnt               += $rides[$key]->bill;
                 if(($rides[$key]->bill) < 0){
                    $tot_ride_bill_amnt               = ($tot_ride_bill_amnt + ((-1) * ($rides[$key]->bill)));
                }else{
                    $tot_ride_bill_amnt               += $rides[$key]->bill;
                }
                
                $tot_ride_recd_amnt               += $rides[$key]->received_amount;
            }


           
            return view('fuel_report.show',compact(
                                                'record',
                                                'orders',
                                                'rides', 
                                                'tot_order_bill_amnt',
                                                'tot_order_recd_amnt',
                                                'tot_ride_bill_amnt',
                                                'tot_ride_recd_amnt',
                                                'user_id'
                                            ));
            // if( (isset($orders)) && (isset($rides)) ){
            //     return view('reports.edit',compact('record','orders','rides'));
            // }else{
            //     return view('reports.edit',compact('record','orders'));
            // }
            
        }else{
            return redirect()
                    ->route('fuel_report.index')
                    ->with('permission','No record found. Rider didnt store meter reading');
        }

        
    }
  public function toggleLock(Request $request)
{
    $id = $request->input('id');
    $user = Auth::user();

    // Fetch the record from the database
    $record = DB::table('rider_histories')->where('id', $id)->first();

    if (!$record) {
        return response()->json(['error' => 'Record not found'], 404);
    }

    if ($record->lock == 1) {
        if ($user->name !== 'Admin') {
            return response()->json(['error' => 'Only admins can unlock'], 403);
        }
        // Unlock
        DB::table('rider_histories')->where('id', $id)->update(['lock' => 0]);
        return response()->json(['success' => 'Unlocked successfully', 'status' => 0]);
    } else {
        // Lock
        DB::table('rider_histories')->where('id', $id)->update(['lock' => 1]);
        return response()->json(['success' => 'Locked successfully', 'status' => 1]);
    }
}

 
    public function update(Request $request, $id){
    }

    public function destroy(Request $request){ 
    }

 public function updateStartReading(Request $request)
{
    $oldValue = $request->input('oldValue');
    $newValue = $request->input('newValue');
    $riderId = $request->input('riderId');
    $userId = $request->input('userId'); // Fetching user ID

    // Find the existing rider history record
    $riderHistory = Rider_history::where('id', $riderId)->first();

    if ($riderHistory) {
        // Update the existing record with the new values
        $riderHistory->old_start_reading = $oldValue;
        $riderHistory->start_reading = $newValue;
        $riderHistory->updated_by = $userId; // Save user ID
        $riderHistory->save();
        $oldend = $riderHistory->old_end_reading ;
        $newend = $riderHistory->end_reading;
         $UpdateName = $riderHistory->updated_by;
          $record     = User::where('id', $UpdateName)->first();
        $name = $record->name;
        return response()->json(['success' => true,'oldEnd' =>$oldend,'newEnd'=>$newend,'update'=>$name]);
    } else {
        return response()->json(['success' => false, 'message' => 'Rider history record not found.']);
    }
}

 public function updateEndReading(Request $request)
{
    $oldValue = $request->input('oldValue');
    $newValue = $request->input('newValue');
    $riderId = $request->input('riderId');
    $userId = $request->input('userId'); // Fetching user ID

    // Find the existing rider history record
    $riderHistory = Rider_history::where('id', $riderId)->first();

    if ($riderHistory) {
        // Update the existing record with the new values
        $riderHistory->old_end_reading = $oldValue;
        $riderHistory->end_reading = $newValue;
        $riderHistory->updated_by = $userId; // Save user ID
        $riderHistory->save();
        $oldstart = $riderHistory->old_start_reading;
        $newstart = $riderHistory->start_reading;
          $UpdateName = $riderHistory->updated_by;
            $record     = User::where('id', $UpdateName)->first();
            $name = $record->name;
        return response()->json(['success' => true,'oldStart'=>$oldstart,'newStart'=>$newstart,'update'=>$name]);
    } else {
        return response()->json(['success' => false, 'message' => 'Rider history record not found.']);
    }
}
}
