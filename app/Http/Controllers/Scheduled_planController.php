<?php
namespace App\Http\Controllers;
use DB;
use Auth;
use Validator;
use DataTables;
use App\Models\Rider;
use App\Models\Order;
use App\Models\Order_history;
use App\Models\Route_plan;
use App\Models\Rider_has_zone;
use App\Models\Distribution_hub;

use App\Models\Payment_ride_history;
use App\Models\Payment_ride;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;

class Scheduled_planController extends Controller
{
    public $today;
    function __construct()
    {
         $this->middleware('permission:scheduled_route_plan-list', ['only' => ['index','show']]);
         $this->middleware('permission:scheduled_route_plan-create', ['only' => ['create','store']]);
         $this->middleware('permission:scheduled_route_plan-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:scheduled_route_plan-delete', ['only' => ['destroy']]);
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
        return view('scheduled_plans.index',
                        compact('hubs')
                    );
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
                                'route_plans.rider_id',
                                'riders.color_code',
                                'riders.max_drop_size',
                                'riders.max_drop_weight',
                                'riders.max_pick',
                                'vehicle_types.hanger'
                            )
                    ->where('route_plans.id',$route_id)
                    ->first();

        return $rec;
    }
    
    public function get_rider_primary_zone($zone_id){
        $data       = Rider_has_zone::orderBy('riders.id')
                        ->leftjoin('riders', 'riders.id', '=', 'rider_has_zones.rider_id')
                        ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                        ->select(
                            'riders.id as id',
                            'riders.max_drop_weight',
                            'riders.max_drop_size',
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

    public function get_rider_has_hanger(){
        $data       = Rider_has_zone::orderBy('riders.id')
                        ->leftjoin('riders', 'riders.id', '=', 'rider_has_zones.rider_id')
                        ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                        ->select(
                            'rider_has_zones.rider_id as id',
                            'riders.max_drop_weight',
                            'riders.max_drop_size',
                            'vehicle_types.hanger',
                            // 'riders.name',
                            'riders.color_code',
                            DB::raw('CONCAT(riders.name,  "  -  ",
                            (CASE 
                                WHEN vehicle_types.hanger = "0" THEN "(B)" 
                                WHEN vehicle_types.hanger = "1" THEN "(CH)" 
                            END)
                            ) as name')

                            )
                        ->where('vehicle_types.hanger',1)
                        ->get()->toArray();

        if(isset($data) ){
            return $data;
        }else{
            return 0;
        }

    }

    public function fn_sort($rec){
        $unsorted_by_rider  = collect($rec)
                                ->sortBy('rider_id')
                                ->sortBy('area_id')
                                ->sortBy('timeslot_id')
                                ->groupBy('rider_id')
                                ->toArray();

        $sorted_by_rider    = $unsorted_by_rider;
        $len                = count($sorted_by_rider);
        $inputs             = array();

        foreach ($sorted_by_rider as $key => $value) {

        $sorted_by_tslot    = collect($value)
                                ->sortBy('zone_id')
                                ->sortBy('latitude')
                                ->sortBy('longitude')
                                ->sortBy('timeslot_id')
                                ->groupBy('timeslot_id')
                                ->toArray();
        $inputs[]           =  $sorted_by_tslot;
        }

        $len1 = count($inputs);
        $recd = array();
        foreach ($inputs as $key1 => $record1) {
            foreach ($record1 as $key2 => $record2) {
                foreach ($record2 as $key3 => $record3) {
                    $recd[] =  $record3;
                }
            }
        }

        return $recd;
    }
public function fn_order_count($hub_id, $rider_id, $dt)
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



    public function schedule_payment_rides(Request $request)
    {
        if((isset($request['ride_id'] )) && (count($request['ride_id'])>0)){
            
            $validator = Validator::make($request->all(), 
                [
                    'rider_id'                    => 'required|array',
                    'rider_id.*'                  => 'required|numeric|min:1', 

                    'ride_id'                     => 'required|array',
                    'ride_id.*'                   => 'required'
                ],
                [
                    'ride_id.*.required'          => 'Please select rider(s)!',
                    'rider_id.*.required'          => 'Please select rides/ orders (s)!',

                ]
            );
            if ($validator->passes()) {
                foreach (($request['ride_id']) as $key => $value) {
                    $data   = DB::table('payment_rides')
                                    ->where('payment_rides.id', $key)
                                    ->whereNotIn('payment_rides.status_id',[15,16])
                                    // ->where('route_plans.rider_id',null)
                                    ->update([
                                                'rider_id'  => $request['rider_id'][$key],
                                                'status_id' => 6, //rider assigned
                                            ]);

                }
                return response()->json(['success'=>'Riders have been assigned successfully.']);
            }else{
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }else{
            return response()->json(['error'=>[0=>"Please select rides/ orders (s)"]]);
        }

    }

    public function cancel_payment_rides(Request $request)
    {
        if((isset($request['ride_id'] )) && (count($request['ride_id'])>0)){
            
            $validator = Validator::make($request->all(), 
                [
                    // 'rider_id'                    => 'required|array',
                    // 'rider_id.*'                  => 'required|numeric|min:1', 

                    'ride_id'                     => 'required|array',
                    'ride_id.*'                   => 'required'
                ],
                [
                    'ride_id.*.required'          => 'Please select rider(s)!',
                    // 'rider_id.*.required'         => 'Please select rides/ orders (s)!',

                ]
            );
            if ($validator->passes()) {
                foreach (($request['ride_id']) as $key => $value) {
                    // cancel rides
                    $data   = DB::table('payment_rides')
                                    ->where('payment_rides.id', $key)
                                    ->whereNotIn('payment_rides.status_id',[15,16])
                                    ->update([
                                                // 'rider_id'  => $request['rider_id'][$key],
                                                'status_id' => 16, //16: cancel
                                            ]);

                    if($data){
                        
                        // BEGIN:: Store cancel payment_rides history
                            $val                     = new Payment_ride_history();
                            $val->payment_ride_id    = $key;
                            $val->status_id          = 16;
                            $val->created_by         = Auth::user()->id;
                            $val->save();
                        // END::  Store cancel payment_rides history
                    }

                }
                return response()->json(['success'=>'Ride(s) have been canceled successfully.']);
            }else{
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }else{
            return response()->json(['error'=>[0=>"Please select rides/ orders (s)"]]);
        }

    }

    public function schedule_reg_orders(Request $request)
    {

    
        if((isset($request['route_id'] )) && (count($request['route_id'])>0)){
            
            $validator = Validator::make($request->all(), 
                [
                    'route_id'                      => 'required|array',
                    'route_id.*'                    => 'required',

                    'rider_id'                      => 'required|array',
                    'rider_id.*'                    => 'required|numeric|min:1', 
                ],
                [
                    'route_id.*.required'           => 'Please select rides/ orders (s)!',
                    'rider_id.*.required'           => 'Please select rider/ orders (s)!',
                ]
            );
            if ($validator->passes()) {
                foreach (($request['route_id']) as $key => $value) {

                    // BEGIN:: finding the last route and seq and rider name of the particular rider id 
                    $rec            = DB::table('route_plans')
                                        ->select(
                                                    'route_plans.id',
                                                    'route_plans.route',
                                                    'route_plans.seq',
                                                    'route_plans.order_id'
                                                )
                                        // ->orderBy('route_plans.id', 'DESC')
                                        ->whereNotNull('route_plans.route')
                                        ->whereNotNull('route_plans.seq')
                                        ->whereDate('route_plans.updated_at','=', $request->plan_date)
                                        ->where('route_plans.rider_id', $request['rider_id'][$key])
                                        ->first();
                    // END:: finding the last route and seq and rider name of the particular rider id 
              
                    // BEGIN:: Getting route, seq, and rider name
                    if(isset($rec->route)){
                        $seq        = (($rec->seq) + 1);
                        $route      = $rec->route;
                    }else{
                        $seq        = 1;
                        $route      = 1;
                        
                    }
                    // END:: Getting route, seq, and rider name

                   // BEGIN:: Getting rider name
                    $rider      = DB::table('riders')
                                        ->select('riders.name')
                                        ->find($request['rider_id'][$key]);

                    if(isset($rider->name)){
                        $rider_name = $rider->name;
                    }else{
                        $rider_name = null;
                    }
                    // END:: Getting rider name
                    
                    $uid            = (Auth::user()->id);
                    // BEGIN:: Update route_plans table 'rider_id', 'route', 'seq'
                    $data           = DB::table('route_plans')
                                        ->where('route_plans.id', $key)
                                        ->update([
                                                    'seq'       => $seq,
                                                    'route'     => $route,
                                                    'rider_id'  => $request['rider_id'][$key],
                                                    'schedule'  => 1, 
                                                    'created_by' =>$uid
                                                ]);
                    // END:: Update route_plans table 'rider_id', 'route', 'seq'

               


                    // BEGIN:: fetch order_id from route_plans table
                    $order          = DB::table('route_plans')
                                        ->select('route_plans.order_id','route_plans.status_id')
                                        ->find($key);

                    $order_id       = $order->order_id;
                    // END:: fetch order_id from route_plans table


                    // BEGIN::Insert order status in Order_history table//
                        $val                    = new Order_history();
                        $val->order_id          = $order_id;
                        $val->created_by        = (Auth::user()->id);
                        $val->status_id         = 6;
                        $val->save();
                    // END::Insert order status in Order_history table//


                
                    // BEGIN:: Update orders table 'pickup_rider' or 'delivery_rider' and their names
                    if( (isset($order->order_id)) && (isset($order->status_id)) ){
                        if(($order->status_id) == 1){ // 1: Pickup
                            $chk            = Order::where('id', $order->order_id)
                                                ->update([

                                                    'pickup_rider_id'       => $request['rider_id'][$key],
                                                    'pickup_rider'          => $rider_name,
                                                    'status_id2'            => '6',  // 6: Rider Assigned
                                                ]);
                        }else{
                            $chk            = Order::where('id', $order->order_id)
                                                ->update([
                                                    'delivery_rider_id'     => $request['rider_id'][$key],
                                                    'delivery_rider'        => $rider_name,
                                                    'status_id2'            => '6',  // 6: Rider Assigned
                                                ]);
                        }
                    }
                    // BEGIN:: Update orders table 'pickup_rider' or 'delivery_rider' and their names

                }
                return response()->json(['success'=>'Riders have been assigned successfully.']);
            }else{
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }else{
            return response()->json(['error'=>[0=>"Please select orders (s)"]]);
        }

        

    }

    public function fetch_payment_order(Request $request)
    {
        if($request->ajax()){

            $dt             = $request->dt;
            $hub_id         = $request->hub_id;
        
            $orders         = DB::table('payment_rides')
                                    ->orderBy('payment_rides.rider_id')
                                    ->leftjoin('customers', 'customers.id', '=', 'payment_rides.customer_id')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'payment_rides.status_id')
                                    ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'payment_rides.address_id')
                                    ->leftjoin('riders', 'riders.id', '=', 'payment_rides.rider_id')
                                    ->leftjoin('time_slots', 'time_slots.id', '=', 'payment_rides.timeslot_id')
                                    ->select(

                                                'payment_rides.*',
                                                'riders.name as rider_name',
                                                'customers.name as customer_name',
                                                'customers.contact_no as contact_no',
                                                'customer_has_addresses.address',
                                                'statuses.name as status_name',
                                                DB::raw('CONCAT(time_slots.start_time," - ", time_slots.end_time ) as time_slot_name')
                                            )
                                    // ->where('route_plans.schedule',0)
                                    ->whereDate('payment_rides.created_at',$dt)
                                    // ->where('route_plans.hub_id',$hub_id)
                                    ->get();
                                    // dd($orders);
            if($orders){
                $riders              = DB::table('hub_has_riders')
                                        ->leftjoin('riders', 'riders.id', '=', 'hub_has_riders.rider_id')
                                        // ->where('hub_has_riders.hub_id',$hub_id)
                                        ->where('riders.status',1)
                                        ->pluck('riders.name','riders.id')
                                        ->all();

                $details            = view('scheduled_plans.payment_order_table',
                                        compact( 
                                                'orders',
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

    public function fetch_schedule_orders(Request $request)
    {
        if($request->ajax()){
            $hub_id             = $request->hub_id;
            $dt                 = $this->today;

            $orders             = DB::table('route_plans')
                                        ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                                        ->leftjoin('areas', 'areas.id', '=', 'route_plans.area_id')
                                        ->leftjoin('zones', 'zones.id', '=', 'route_plans.zone_id')
                                        // ->leftjoin('riders', 'riders.id', '=', 'route_plans.rider_id')
                                        ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                                        ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                        ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                                        ->orderBy('orders.customer_id')
                                        ->select(
                                                    'route_plans.*',
                                                    // 'riders.color_code',
                                                    'customers.name as customer_name',
                                                    'areas.name as area_name',
                                                    'zones.name as zone_name',
                                                    'orders.ref_order_id',
                                                    'customer_has_addresses.customer_id as customer_id',
                                                    'statuses.name as status_name',
                                                    'customer_has_addresses.latitude',
                                                    'customer_has_addresses.longitude',
                                                    'customer_has_addresses.address as cus_address',
                                                     DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                                )
                                        ->where('route_plans.schedule', 0) 
                                         ->Where(function($query) use ($dt){
                                                $query->whereDate('orders.pickup_date',$dt)
                                                    ->OrWhereDate('orders.delivery_date',$dt);
                                            })
                                        ->where('orders.hub_id',$hub_id)
                                        ->get()
                                        ->all();

            if($orders){
                foreach ($orders as $key => $value) {


                    if(($value->status_id) == 1){
                        $iid =  ($value->order_id);
                        // BEGIN :: Delete details if it is pickup   
                        DB::table("order_has_addons")->where('order_id', '=', $iid)->delete();
                        DB::table("order_has_items")->where('order_id', '=', $iid)->delete();
                        DB::table("order_has_services")->where('order_id', '=', $iid)->delete();
                        // END :: Delete details if it is pickup 
                    }
                    // echo "Id: $value->zone_id";
                    // echo "<br>id: $value->id";
                    // echo ", Assigning: $asgn_rider";
                    $p_rider                        = ($this->get_rider_primary_zone($value->zone_id)); 
                    $s_rider                        = ($this->get_rider_secondary_zone($value->zone_id));
                    $h_rider                        = ($this->get_rider_has_hanger());

                    // $asgn_rider                     = ($this->fn_assign_rider(($value->id),($value->hanger),($value->weight),$p_rider,$s_rider, $h_rider));        
                    $asgn_rider                         = ($this->fn_assign_rider(($value->customer_id),($value->id),($value->hanger),($value->weight),$p_rider,$s_rider, $h_rider));        
                    if(isset( $asgn_rider->rider_id)){
                        $orders[$key]->assign_rider     = $asgn_rider->rider_id;
                        $orders[$key]->rider_id         = $asgn_rider->rider_id;
                    }else{
                        $orders[$key]->assign_rider     = null;
                    }

                    if(isset( $asgn_rider->hanger)){
                        $orders[$key]->rider_hanger     = $asgn_rider->hanger;
                    }else{
                        $orders[$key]->rider_hanger     = null;
                    }

                    if(isset( $asgn_rider->color_code)){
                        $orders[$key]->color_code     = $asgn_rider->color_code;
                    }else{
                        $orders[$key]->color_code     = null;
                    }
                    
                    if(isset( $asgn_rider->max_drop_weight)){
                        $orders[$key]->max_drop_weight = $asgn_rider->max_drop_weight;
                    }else{
                        $orders[$key]->max_drop_weight = 0;
                    }
                    if(isset( $asgn_rider->max_drop_size)){
                        $orders[$key]->max_drop_size = $asgn_rider->max_drop_size;
                    }else{
                        $orders[$key]->max_drop_size = 0;
                    }

                    if(isset( $asgn_rider->max_pick)){
                        $orders[$key]->max_pick = $asgn_rider->max_pick;
                    }else{
                        $orders[$key]->max_pick = 0;
                    }
                    $orders[$key]->primary_rider       = $p_rider;
                    $orders[$key]->secondary_rider     = $s_rider;
                    $orders[$key]->hanger_rider        = $h_rider;
                    
                }

                $orders         = ($this->fn_sort($orders));

                $details        = view('scheduled_plans.order_table',
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

   public function fetch_scheduled_plan(Request $request)
{
    if ($request->ajax()) {
        $dt = $request->dt;
        $hub_id = $request->hub_id;

        $orders = DB::table('route_plans')
                    ->leftjoin('riders', 'riders.id', '=', 'route_plans.rider_id')
                    ->select('route_plans.rider_id', 'riders.name')
                    ->groupBy('route_plans.rider_id', 'riders.name')
                    ->whereDate('route_plans.updated_at', $dt)
                    ->where('route_plans.schedule', 1)
                    ->where('route_plans.hub_id', $hub_id)
                    ->get();

        if ($orders) {
            foreach ($orders as $key => $value) {
                $rider_id = $value->rider_id;

                // Get the unique counts for each category
                $uniqueLocations = $this->fn_order_count($hub_id, $rider_id, $dt);

                $orders[$key]->date = $dt;
                $orders[$key]->pick_up = $uniqueLocations['pickup'];
                $orders[$key]->drop_off = $uniqueLocations['dropoff'];
                $orders[$key]->pick_drop = $uniqueLocations['pickdrop'];
            }

            $details = view('scheduled_plans.plan_table', compact('orders'))->render();
            return response()->json(['data' => $orders, 'details' => $details]);
        } else {
            return response()->json(['error' => "Data not found"]);
        }
    }
}



    public function update_order_seq(Request $request){
        if($request->ajax()){

            $seq            = $request->seq;
            if(isset($seq)){
               foreach($seq as $key => $value) {
                $record     = DB::table('route_plans')
                                ->where('route_plans.id',$key)
                                ->update(['route_plans.seq'=> $value]); 
                }  
                return response()->json(['success'=>'Sequence modified successfully.']);
            }else{
                return response()->json(['error'=>[0=>'Something went wrong.']]);             
            }
           
        }
    }


        public function fetch_rider_plan(Request $request)
            {
                if($request->ajax()){

                            $rider_id         = $request->rider_id;  
                            $dt               = $request->dt;
                        $pickdrop_order = DB::table('route_plans')
                    ->orderBy('route_plans.seq')
                    ->leftJoin('distribution_hubs', 'distribution_hubs.id', '=', 'route_plans.hub_id')
                    ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                    ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                    ->leftJoin('areas', 'areas.id', '=', 'route_plans.area_id')
                    ->leftJoin('zones', 'zones.id', '=', 'route_plans.zone_id')
                    ->leftJoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                    ->leftJoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                    ->leftJoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                    ->select(
                        'route_plans.id as id',
                        'route_plans.*',
                        'customers.name as cus_name',
                        'customers.contact_no as cus_contact',
                        'orders.ref_order_id',
                        'orders.customer_id as customerID',
                        'areas.name as area_name',
                        'zones.name as zone_name',
                        'statuses.name as status_name',
                        'customer_has_addresses.address as cus_address',
                        'customer_has_addresses.customer_id as customer_id',
                        DB::raw('CONCAT(time_slots.start_time, " - ", time_slots.end_time) as timeslot_name')
                    )
                    ->where('route_plans.rider_id', $rider_id)
                    ->where('route_plans.schedule', 1)
                    ->where('route_plans.status_id', 3)
                    ->whereDate('route_plans.updated_at', $dt)
                    ->orderBy('orders.customer_id')
                    ->get();

                $uniqueCustomerPickdrop = $pickdrop_order->pluck('customerID')->unique()->toArray();
                $firstOrderPickdrop = [];
                //dd($uniqueCustomerPickdrop);

                foreach ($uniqueCustomerPickdrop as $customerId) {
                    $firstOrder = DB::table('route_plans')
                        ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                        ->select('orders.id as order_id')
                        ->where('route_plans.rider_id', $rider_id)
                        ->where('orders.customer_id', $customerId)
                        ->whereDate('route_plans.updated_at', $dt)
                        ->orderBy('route_plans.updated_at')
                        ->first();

                    if ($firstOrder) {
                        $firstOrderPickdrop[$customerId] = $firstOrder->order_id;
                    }
                }

                // Fetch Pickup orders
                $pick_order = DB::table('route_plans')
                    ->orderBy('route_plans.seq')
                    ->leftJoin('distribution_hubs', 'distribution_hubs.id', '=', 'route_plans.hub_id')
                    ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                    ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                    ->leftJoin('areas', 'areas.id', '=', 'route_plans.area_id')
                    ->leftJoin('zones', 'zones.id', '=', 'route_plans.zone_id')
                    ->leftJoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                    ->leftJoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                    ->leftJoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                    ->select(
                        'route_plans.id as id',
                        'route_plans.*',
                        'customers.name as cus_name',
                        'customers.contact_no as cus_contact',
                        'orders.ref_order_id',
                        'orders.customer_id as customerID',
                        'areas.name as area_name',
                        'zones.name as zone_name',
                        'statuses.name as status_name',
                        'customer_has_addresses.address as cus_address',
                        'customer_has_addresses.customer_id as customer_id',
                        DB::raw('CONCAT(time_slots.start_time, " - ", time_slots.end_time) as timeslot_name')
                    )
                    ->where('route_plans.rider_id', $rider_id)
                    ->where('route_plans.schedule', 1)
                    ->where('route_plans.status_id', 1)
                    ->whereDate('route_plans.updated_at', $dt)
                    ->orderBy('orders.customer_id')
                    ->get();

                $uniqueCustomerPick = $pick_order->pluck('customerID')->unique()->toArray();

                $firstOrderPick = [];

                foreach ($uniqueCustomerPick as $customerId) {
                    $firstOrder = DB::table('route_plans')
                        ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                        ->select('orders.id as order_id')
                        ->where('route_plans.rider_id', $rider_id)
                        ->where('orders.customer_id', $customerId)
                        ->whereDate('route_plans.updated_at', $dt)
                        ->orderBy('route_plans.updated_at')
                        ->first();

                    if ($firstOrder) {
                        $firstOrderPick[$customerId] = $firstOrder->order_id;
                    }
                }

                // Dictionary to hold the unique customer IDs and their order IDs
                $unique_orders = [];

                // Populate the dictionary with Pickdrop data
                foreach ($uniqueCustomerPickdrop as $customer_id) {
                    $order_id = $firstOrderPickdrop[$customer_id] ?? null;
                    if ($order_id !== null) {
                        $unique_orders[$customer_id] = $order_id;
                    }
                }

                // Add Pickup data to the dictionary only if the customer ID is not already present and is not null
                foreach ($uniqueCustomerPick as $customer_id) {
                    $order_id = $firstOrderPick[$customer_id] ?? null;
                    if ($customer_id !== null && !array_key_exists($customer_id, $unique_orders) && $order_id !== null) {
                        $unique_orders[$customer_id] = $order_id;
                    }
                }

                // Extract the customer IDs and order IDs from the dictionary
                $result_customer_ids = array_keys($unique_orders);
                $result_order_ids = array_values($unique_orders);

                $drop_order = DB::table('route_plans')
                    ->orderBy('route_plans.seq')
                    ->leftJoin('distribution_hubs', 'distribution_hubs.id', '=', 'route_plans.hub_id')
                    ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                    ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                    ->leftJoin('areas', 'areas.id', '=', 'route_plans.area_id')
                    ->leftJoin('zones', 'zones.id', '=', 'route_plans.zone_id')
                    ->leftJoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                    ->leftJoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                    ->leftJoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')
                    ->select(
                        'route_plans.id as id',
                        'route_plans.*',
                        'customers.name as cus_name',
                        'customers.contact_no as cus_contact',
                        'orders.ref_order_id',
                        'orders.customer_id as customerID',
                        'areas.name as area_name',
                        'zones.name as zone_name',
                        'statuses.name as status_name',
                        'customer_has_addresses.address as cus_address',
                        'customer_has_addresses.customer_id as customer_id',
                        DB::raw('CONCAT(time_slots.start_time, " - ", time_slots.end_time) as timeslot_name')
                    )
                    ->where('route_plans.rider_id', $rider_id)
                    ->where('route_plans.schedule', 1)
                    ->where('route_plans.status_id', 2)
                    ->whereDate('route_plans.updated_at', $dt)
                    ->orderBy('orders.customer_id')
                    ->get();

                $uniqueCustomerDrop = $drop_order->pluck('customerID')->unique()->toArray();
                $firstOrderDrop = [];

                foreach ($uniqueCustomerDrop as $customerId) {
                    $firstOrder = DB::table('route_plans')
                        ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
                        ->select('orders.id as order_id')
                        ->where('route_plans.rider_id', $rider_id)
                        ->where('orders.customer_id', $customerId)
                        ->whereDate('route_plans.updated_at', $dt)
                        ->orderBy('route_plans.updated_at')
                        ->first();

                    if ($firstOrder) {
                        $firstOrderDrop[$customerId] = $firstOrder->order_id;
                    }
                }

                    // Merge Drop data with existing unique orders
                    foreach ($uniqueCustomerDrop as $customer_id) {
                        $order_id = $firstOrderDrop[$customer_id] ?? null;
                        if ($customer_id !== null && !array_key_exists($customer_id, $unique_orders) && $order_id !== null) {
                            $unique_orders[$customer_id] = $order_id;
                        }
                    }

                    // Final extraction of customer IDs and order IDs
                    $resultdrop_customer_ids = array_keys($unique_orders);
                    $resultdrop_order_ids = array_values($unique_orders);

                //dd($resultdrop_order_ids);
                    $orders         = DB::table('route_plans')
                                        ->orderBy('route_plans.seq')
                                        ->leftjoin('distribution_hubs', 'distribution_hubs.id', '=', 'route_plans.hub_id')
                                        ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                        ->leftjoin('areas', 'areas.id', '=', 'route_plans.area_id')
                                        ->leftjoin('zones', 'zones.id', '=', 'route_plans.zone_id')
                                        // ->leftjoin('riders', 'riders.id', '=', 'route_plans.rider_id')
                                        ->leftjoin('statuses', 'statuses.id', '=', 'route_plans.status_id')
                                        ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                                        ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'route_plans.address_id')

                                        ->orderBy('orders.customer_id')
                                        ->select(
                                                    'route_plans.*',
                                                    'customers.name as cus_name',
                                                    'customers.contact_no as cus_contact',
                                                    // 'riders.color_code',
                                                    'orders.ref_order_id',
                                                    'areas.name as area_name',
                                                    'zones.name as zone_name',
                                                    'statuses.name as status_name',
                                                    'customer_has_addresses.address as cus_address',
                                                    'customer_has_addresses.customer_id as customer_id',
                                            
                                                    DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                                )
                                        ->where('route_plans.rider_id', $rider_id) 
                                        ->where('route_plans.schedule', 1) 
                                        ->whereDate('route_plans.updated_at',$dt)
                                        ->where('route_plans.is_canceled',null)
                                        ->whereIn('route_plans.order_id', $resultdrop_order_ids)
                                        // ->whereDate('route_plans.created_at',$dt)
                                        ->get();
                    foreach ($orders as $key => $value) {
                        $orders[$key]->gen_bags         = ($this->count_tags(($value->order_id),0));
                        $orders[$key]->scan_bags        = ($this->count_tags(($value->order_id),1));
                        $hub_id                         = $value->hub_id;
                    }
                    if($orders){

                        $details            = view('scheduled_plans.rider_plan_table',
                                                compact('orders'))
                                                ->render();
                        
                        // dd($details);
                        return response()->json(['data'=>$orders,'details'=>$details]);
                    }else{
                        return response()->json(['error'=>"Data not found"]);
                    }

                }
            }

    public function update_rider_plan(Request $request){

        if($request->ajax()){
            // set variables
            $rides              = $request->ride_id;
            $orders             = $request->order_id;
            $complete           = $request->complete;
            $rider_id           = $request->riders;
            $hanager            = $request->hanger;

            // find rider name
            $rdr                = Rider::leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                                        ->select('vehicle_types.hanger','riders.name')
                                        ->findOrFail($rider_id);
            if(isset($rdr)){
                $rider_name     = $rdr->name;
                $rider_hanger   = $rdr->hanger;
            }else{
                $rider_name     = "";
                $rider_hanger   = 0;
            }

            // Checking:: all rides must be in-complete
            if(isset($rides)){
               foreach($rides as $key => $value){
                   if($complete[$key] == 1){
                        return response()->json(['error'=>[0=>'Order no# '.$orders[$key].' has been completed and its rider cannot be changed.']]);
                   }
                   if(($hanager[$key] == 1)  && ($rider_hanger == 0)){
                    return response()->json(['error'=>[0=>'Order no# '.$orders[$key].' requires hanger vehicle while rider: '.$rider_name.' has bike.']]);
                   }
               }

               $msg = "";
                //  Looping the rides for updating the riders
                foreach($rides as $key => $value){

                    // find order in route_plan table
                    $rec            = Route_plan::select('route_plans.order_id','route_plans.status_id')
                                        ->findOrFail($key);
                    if(isset($rec)){
                        // set variables 
                        $order_id   = $rec->order_id;
                        $status_id  = $rec->status_id;

                        // Checking:: Condition
                        // if status is pickup then update pickup rider in "Orders" table 
                        // if status is (drop off) or (pick & drop) then update delivery rider in "Orders" table 

                        // Update rider in "orders" table according to the status
                        if($status_id == 1){  // (1: pickup)
                            $upd    = DB::table('orders')
                                            ->where('orders.id',$order_id)
                                            ->update([
                                                        'orders.pickup_rider'=> $rider_name,
                                                        'orders.pickup_rider_id'=> $rider_id
                                                    ]); 
                        }else{  // (2: drop off) and (3: pick & Drop)
                            $upd    = DB::table('orders')
                                            ->where('orders.id',$order_id)
                                            ->update([
                                                        'orders.delivery_rider'=> $rider_name,
                                                        'orders.delivery_rider_id'=> $rider_id
                                                    ]); 
                        }

                        // Update rider in "route_plans" table according to the status
                        $record     = DB::table('route_plans')
                                        ->where('route_plans.complete',0)
                                        ->where('route_plans.id',$key)
                                        ->update(['route_plans.rider_id'=> $rider_id]); 
                    }else{
                        return response()->json(['error'=>[0=>'Order not found!']]);  
                    }

                    $txt            = $orders[$key].",";
                    $msg            = $msg." ". $orders[$key].",";
                }

                if($msg!=""){
                    return response()->json(['success'=>'Order no# '.$msg.' rider has been changed successfully.']);
                }else{
                    return response()->json(['error'=>[0=>'Something went wrong.']]);
                }

            }else{

                return response()->json(['error'=>[0=>'Please select orders.']]);
            }

          
        }
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
 
    public function cancel_order(Request $request)
    {
        if($request->ajax()){
            
            $id             = $request->route_id;
        
            // CHECK:: Order should NOT be completed, 
            $data           = Route_plan::where('complete', '=', 0)->find($id);
           
            if(!(empty($data))){
                $order_id   = $data->order_id;
                // if order is not completed than delete it.
                $rec        = DB::table("route_plans")->where('complete', '=', 0)->where('id', '=', $id)->delete();

                if($rec){
                    $record     = DB::table('orders')
                                    ->where('orders.id',$order_id)
                                    ->update(['status_id2'=> 16]);  // 16 :cancelled

                                   
                    if($record){
                        // BEGIN:: Store order history
                            $val                     = new Order_history();
                            // $val->type               = 1;
                            $val->order_id           = $order_id;
                            // $val->detail             = Null;
                            $val->created_by         = Auth::user()->id;
                            $val->status_id          = 16;  // 16 :cancelled
                            $val->save();
                        // END:: Store order history
                        (new NotificationController)->cancel_order($order_id);
                        return response()->json(['success'=>'Order no# '.$order_id.' cancelled successfully.']);
                    }else{
                        return response()->json(['error'=>[0=>'Order no# '.$order_id.' not cancelled successfully.']]); 
                    }
                }else{
                    return response()->json(['error'=>[0=>'Order no# '.$order_id.' not cancelled successfully.']]);
                }
            }else{
                return response()->json(['error'=>[0=>'Order no# '.$order_id.' not cancelled successfully.']]);
            }
        }
    }


    

    public function edit($id){
        // dd($id);
      
        $data               = explode('.',$id);
        $id                 = $data[0];
        $dt                 = $data[1];

        $rider              = DB::table('riders')
                                ->where('riders.id', $id)
                                ->select(
                                            'riders.name',
                                        )
                                ->first();
                            

        $riders             = DB::table('hub_has_riders')
                                ->leftjoin('riders', 'riders.id', '=', 'hub_has_riders.rider_id')
                                ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                                ->select(
                                            'riders.id',
                                            // 'riders.name',
                                            DB::raw('CONCAT(riders.name,  "  -  ",
                                            (CASE 
                                                WHEN vehicle_types.hanger = "0" THEN "(B)" 
                                                WHEN vehicle_types.hanger = "1" THEN "(CH)" 
                                            END)
                                            ) as name')
                                        )
                                // ->where('hub_has_riders.hub_id',$hub_id)
                                ->where('riders.status',1)
                                ->pluck('name','id')
                                ->all();
                            
        return view('scheduled_plans.edit',compact('riders','id','rider','dt'));
    }


    public function count_tags($order_id, $state){
        
        if($state == 1){
     
            $data           =  DB::table('order_has_bags')
                                ->where('order_has_bags.order_id',$order_id)
                                ->where('order_has_bags.tag_scanned',1)
                                ->count('order_has_bags.id');
        }else{
            $data           =  DB::table('order_has_bags')
                                ->where('order_has_bags.order_id',$order_id)
                                ->count('order_has_bags.id');
        }


        if(isset($data)){
            return $data;
        }else{
            return 0;
        }
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy(Request $request)
    { 
    }

}
