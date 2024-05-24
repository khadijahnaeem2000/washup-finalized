<?php
namespace App\Http\Controllers;

use DB;
use DataTables;
use Validator;
use App\Models\Order;
use App\Models\Route_plan;
use App\Models\Rider;
use App\Models\Status;
use App\Models\Holiday;
use App\Models\Time_slot;
use Illuminate\Http\Request;
use App\Models\Order_history;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\Customer_has_address;

class OrderController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:order-list', ['only' => ['index','show']]);
         $this->middleware('permission:order-create', ['only' => ['create','store']]);
         $this->middleware('permission:order-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('orders.index');
    }

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'from_date'             => 'required|date',
                // 'to_date'               => 'required|date|after:from_date',
                'to_date'               => 'required|date',
            ],
            [
                // 'hub_id.required'       =>'Please select at least one hub',
                // 'rider_id.required'     =>'Please select rider',
                // 'to_date.after'         =>'Please select "To date" greater than "From date" ',
            ]
        );

        if ($validator->passes()) {
            
            $rec    = DB::table('orders AS ords')
                        ->leftjoin('customers', 'customers.id', '=', 'ords.customer_id')
                        ->leftjoin('statuses as st', 'st.id', '=', 'ords.status_id')
                        ->leftjoin('statuses as st2', 'st2.id', '=', 'ords.status_id2')
                        ->leftjoin('riders', 'riders.id', '=', 'ords.pickup_rider_id')
                        ->select(
                                    'ords.id as id',
                                    'ords.ref_order_id as ref_order_id',
                                    'customers.name',
                                    'customers.contact_no',
                                    'ords.pickup_date',
                                    'ords.delivery_date',
                                    'riders.name as rider_name',
                                    DB::raw(
                                                    '(CASE 
                                                        WHEN ( select count(orders.id) from orders where orders.ref_order_id = ords.id  > 0) THEN  "Yes"
                                                        ELSE "No"
                                                        END
                                                    ) AS has_hfq'
                                        ),
                                    DB::raw(
                                                '(CASE 
                                                    WHEN ords.status_id2 = 16 THEN st2.name
                                                    WHEN ((ords.status_id2 >= 15 ) AND (ords.status_id2 <=18) AND (ords.status_id2 !=16) ) OR (ISNULL(ords.status_id2)) THEN st.name
                                                    WHEN ords.status_id2 != "NULL" THEN st2.name
                                                    END
                                                ) AS status_name'
                                    )
                                )
                        
                        ->whereDate('ords.pickup_date','>=', $request->from_date)  
                        ->whereDate('ords.pickup_date','<=', $request->to_date) 
                        ->get();
                        if(!($rec->isEmpty())){
                            $details    = view('orders.order_table',
                                        compact('rec'))
                                        ->render();
                            return response()->json(['orders'=>$rec,'details'=>$details]);
                        }else{
                            return response()->json(['error'=>[0=>"Data not found"  ]]);
                        }
                       
        }else{
            return response()->json(['error'=>$validator->errors()->all()]);
        }

       
    }

    public function send_invoice($order_id) {

        $record             = array();

        
        if(!(isset($order_id))){
            return redirect()
            ->back()
            ->with('permission','Invalid order id!');
        }

        $histories              = DB::table('order_histories')
                                        ->where('order_histories.order_id', $order_id)
                                        ->where('order_histories.status_id', 8)  // content verified
                                        ->select(
                                                    'order_histories.id'
                                                )
                                        ->get()
                                        ->first();
        if(!(isset($histories->id))){
            return redirect()
                    ->back()
                    ->with('permission','Order is not verified yet!');

        }


        $orders             = DB::table('orders')
                                ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                ->where('orders.id', $order_id)
                                ->select(
                                            'orders.*',
                                            DB::raw('DATE_FORMAT(orders.pickup_date, "%d-%m-%Y") as pickup_date'),
                                            DB::raw('DATE_FORMAT(orders.delivery_date, "%d-%m-%Y") as delivery_date'),
                                            'customers.name as customer_name',
                                            'customers.contact_no as contact_no',
                                            'customers.email as customer_email',
                                        )
                                ->first();
        if($orders !=null){

            $selected_services      = DB::table('order_has_services')
                                        ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                        ->leftjoin('units', 'units.id', '=', 'services.unit_id')
                                        ->where('order_has_services.order_id', $order_id)
                                        ->select(
                                                    'services.id as service_id',
                                                    'units.id as unit_id',
                                                    'services.name as service_name',
                                                    'order_has_services.weight as weight',
                                                    'order_has_services.qty as service_qty',
                                                )
                                        ->orderBy('order_number','ASC')
                                        ->get()
                                        ->all(); 


            
            foreach ($selected_services as $service_key => $service_value) {

                if($service_value->unit_id == 2){

                    // unit id : 2 means item wise rate
                    $selected_items         = DB::table('order_has_items')
                                                ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                                // ->leftjoin('customer_has_items', 'customer_has_items.item_id', '=', 'order_has_items.item_id')
                                                ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                                // ->leftjoin('order_has_services', 'order_has_services.order_id', '=', 'order_has_items.order_id')
                                                ->where('order_has_items.order_id', $order_id)
                                                // ->where('customer_has_items.service_id', $service_value->service_id)
                                                // ->where('customer_has_items.customer_id', $orders->customer_id)
                                                ->where('order_has_items.service_id', $service_value->service_id)
                                                ->select(
                                                            'items.id as item_id',
                                                            'items.short_name as item_name',
                                                            'order_has_items.service_id as service_id',
                                                            'order_has_items.pickup_qty as pickup_qty',
                                                            'services.name as service_name',
                                                            // 'customer_has_items.item_rate as item_rate',
                                                            'order_has_items.cus_item_rate as item_rate',
                                                            'order_has_items.id as ord_itm_id'
                                                        )
                                                ->get()
                                                ->all(); 
                                            
                }else{
                    $selected_items         = DB::table('order_has_items')
                                                ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                                ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                                ->leftjoin('order_has_services', 'order_has_services.service_id', '=', 'order_has_items.service_id')
                                                ->where('order_has_items.order_id', $order_id)
                                                ->where('order_has_services.order_id', $order_id)
                                                ->where('order_has_items.service_id', $service_value->service_id)
                                                ->select(
                                                            'items.id as item_id',
                                                            'items.short_name as item_name',
                                                            'order_has_items.service_id as service_id',
                                                            'order_has_items.pickup_qty as pickup_qty',
                                                            'order_has_services.cus_service_rate as service_rate',
                                                            'services.name as service_name',
                                                            'order_has_items.id as ord_itm_id'
                                                        )
                                                ->get()
                                                ->all();   
                                                // dd($selected_items);
                }
            

                foreach ($selected_items as $item_key => $item_value) {
                    $selected_addons        = DB::table('order_has_addons')
                                                ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                                ->where('order_has_addons.order_id', $order_id)
                                                ->where('order_has_addons.service_id', $service_value->service_id)
                                                ->where('order_has_addons.item_id', $item_value->item_id)
                                                ->where('order_has_addons.ord_itm_id', $item_value->ord_itm_id)
                                                ->select('addons.id as addon_id',
                                                        'addons.name as addon_name',
                                                        // 'addons.rate as addon_rate',
                                                        'order_has_addons.cus_addon_rate as addon_rate',
                                                        'order_has_addons.item_id as item_id',
                                                        'order_has_addons.service_id as service_id',
                                                        'order_has_addons.ord_itm_id as ord_itm_id',
                                                        )
                                                ->get()
                                                ->all();

                    $selected_items[$item_key]->addons = $selected_addons;  
                 
             
                }                            

                $record[$service_value->service_id]         = $service_value;
                $record[$service_value->service_id]->items  = $selected_items;

            }

            $data = $orders;
            return view('orders.show_invoice',
            compact('data',
                    'record'
                )
            );
       
        }else{
            return 0;
        }

        

       
    }

    public function fetchCustomerDetail(Request $request)
    {
        if($request->ajax()){
            $customer           = DB::table('customers')
                                        ->select('customers.id','customers.name','customers.permanent_note')
                                        ->where('customers.contact_no',$request->contact_no)
                                        ->first();
            if($customer){
                $customer_id    = $customer->id;
                $addresses      = DB::table('customer_has_addresses')
                                        ->select('id','status', 'address as name')
                                        ->where('customer_has_addresses.customer_id',$customer_id)
                                        ->get();

                $customer_address = view('orders.ajax-address',compact('addresses'))->render();
                return response()->json(['data'=>$customer,'customer_address'=>$customer_address]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
            
        }

    }

    public function get_lat_lng(Request $request)
    {
        $addresses          = DB::table('customer_has_addresses')
                                ->select('id','latitude', 'longitude')
                                ->where('customer_has_addresses.id',$request->id)
                                ->first();
                                // dd($addresses);
        if($addresses){
            return response()->json(['data'=>$addresses]);
        }else{
            return response()->json(['error'=>"Data not found"]);
        }
    }

    public function create()
    {
        $statuses       = DB::table('statuses')
                                ->where('statuses.id', 1)
                                // ->orWhere('statuses.id', 3)
                                ->pluck('name','id')
                                ->all();

        $holidays       =  DB::table('holidays')
                                ->select('holiday_date')
                                ->get();

                                
        $time_slots     = DB::table('time_slots')
                            ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                            ->pluck('name','id')
                            ->all();

        $areas         = DB::table('areas')
                            ->pluck('center_points','id')
                            ->all();
        
        return view('orders.create',compact('statuses','time_slots','holidays','areas'));
    }

    public function get_address($address_id){
        $data = Customer_has_address::where('customer_has_addresses.id', $address_id)
                        ->select('address as address_name')
                        ->first();
        return $data->address_name;
    }

    public function get_timeslot($time_slot_id){
        $data = Time_slot::where('time_slots.id', $time_slot_id)
                        ->select(
                                DB::raw('CONCAT(time_slots.start_time," - ", time_slots.end_time ) as time_slot_name'))
                        ->first();
        return $data->time_slot_name;
    }

    public function get_rider($rider_id){
        $data = Rider::where('riders.id', $rider_id)
                        ->select('riders.name as rider_name')
                        ->first();
        return $data->rider_name;
    }

    public function store(Request $request)
    {
       $data                =  Order::orderBy('orders.id','DESC')
                                ->where('customer_id',$request['customer_id'])
                                ->where('status_id',1)
                                ->whereDate('pickup_date',$request['pickup_date'])
                                ->first();

        if(!($data)){
            request()->validate([
                'customer_id'           => 'required',
                'pickup_address_id'     => 'required',
                'pickup_date'           => 'required|date',
                'delivery_date'         => 'required|date|after:pickup_date',
            ]);

            $inputs                     = $request->all();
            $pickup_address             = $this->get_address($inputs['pickup_address_id']);
            $pickup_timeslot            = $this->get_timeslot($inputs['pickup_timeslot_id']);
            
            $inputs['pickup_address']   = $pickup_address;
            $inputs['pickup_timeslot']  = $pickup_timeslot;

            $data                       = Order::create($inputs);
            $order_id                   = $data['id'];
            $status_id                  = $request['status_id'];

            if($data){
                if($status_id){
                    $var                =  new Order_history();
                    $var->order_id      = $order_id;
                    $var->status_id     = $status_id;
                    $var->save();
                }
            }
            return redirect()->route('orders.index')
                        ->with('success','Order of '.$request['name']. ' added successfully.');
        }else{
            return redirect()->back()
                        ->with('permission','Order of this customer is already in queue.');
            
        }
        
    }

   

    public function show($id)
    {
        $details                = null;
        $histories              = null;
        $selected_items         = null;
        $selected_addons        = null;
        $selected_services      = null;

        $data                   = Order::orderBy('orders.created_at','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->leftjoin('statuses as st', 'st.id', '=', 'orders.status_id')
                                    ->leftjoin('statuses as st2', 'st2.id', '=', 'orders.status_id2')
                                    ->leftjoin('wash_house_has_orders', 'wash_house_has_orders.order_id', '=', 'orders.id')
                                    ->leftjoin('wash_houses', 'wash_houses.id', '=', 'wash_house_has_orders.wash_house_id')
                                    ->leftjoin('users','users.id','=','orders.DW_who')
                                    ->select(
                                                'orders.id',
                                                'orders.ref_order_id',
                                                'customers.id as customer_id',
                                                'users.name as order_DW_who',
                                                'customers.name',
                                                'customers.contact_no',
                                                'orders.id as order_id',
                                                'orders.pickup_date',
                                                'orders.waver_delivery',
                                                'orders.phase',
                                                'orders.DW_when',
                                                'orders.DW_who',
                                                'customers.permanent_note',
                                                'orders.order_note',
                                                'orders.delivery_date',
                                                'wash_houses.name as washhouse_name',
                                                // 'st.name as status_name',
                                                'orders.status_id2',
                                                DB::raw('(CASE 
                                                    WHEN (orders.status_id2 >= 15 AND orders.status_id2 <=18) OR (ISNULL(orders.status_id2)) THEN st.name
                                                    WHEN orders.status_id2 != "NULL" THEN st2.name
                                                    END) AS status_name'),
                                                'orders.pickup_address',
                                                'orders.pickup_timeslot',
                                                'orders.delivery_address',
                                                'orders.delivery_timeslot',
                                            )
                                    // ->whereNull('orders.delivery_rider_id')
                                    // ->whereNotNull('orders.pickup_rider_id')
                                    ->findOrFail($id);


            if($data){
                $polybags               = null;
                $is_packed              = DB::table('order_histories')
                                            ->where('order_histories.order_id', $id)
                                            ->where('order_histories.status_id', 14)
                                            ->exists();

                if($is_packed) {
                    $polybags           = DB::table('order_has_bags')
                                            ->where('order_has_bags.order_id', $id)
                                            ->count();
                }              
                                      
                                            
                $histories              = DB::table('order_histories')
                                            ->leftjoin('statuses', 'statuses.id', '=', 'order_histories.status_id')
                                            // ->leftjoin('users', 'users.id', '=', 'order_histories.created_by')
                                            // ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                            // ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                                            ->where('order_histories.order_id', $id)
                                            ->select(
                                                        'order_histories.id as history_id',
                                                        'statuses.id as status_id',
                                                        'statuses.name as status_name',
                                                        // 'users.name as user_name',
                                                        // 'roles.name as role_name',
                                                        'order_histories.detail',
                                                        'order_histories.created_by',
                                                        'order_histories.created_at as created_at',
                                                        'order_histories.type'
                                                    )
                                            ->get()
                                            ->all();
                                            
                // get user_name and user_role 
                // join users table if type = null
                // join riders table if type = 1
                foreach ($histories as $key => $value) {
                    $user_id = $value->created_by;
            
                    if($value->type == 1){ // "1" it is rider while "null" is user
                        $user                   = DB::table('riders')
                                                    ->where('riders.id', $user_id)
                                                    ->select(
                                                                'riders.name as user_name'
                                                            )
                                                    ->first();
                        
                    }else{
                        $user                   = DB::table('users')
                                                    ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                                    ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                                                    ->where('users.id', $user_id)
                                                    ->select(
                                                                'users.name as user_name',
                                                                'roles.name as role_name',
                                                            )
                                                    ->first();
                        
                    }
        
                    if(isset($user->user_name)){
                        $histories[$key]->user_name = $user->user_name;
                    }else{
                        $histories[$key]->user_name = "";
                    }
                    if(isset($user->role_name)){
                        $histories[$key]->role_name = $user->role_name;
                    }else{
                        $histories[$key]->role_name = "Rider";
                    }


                }


                $selected_services      = DB::table('order_has_services')
                                            ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                            ->where('order_has_services.order_id', $id)
                                            ->select('services.id as service_id',
                                                    'services.name as service_name',
                                                    'order_has_services.weight as service_weight',
                                                    'order_has_services.qty as service_qty')
                                            ->orderBy('order_has_services.order_number','ASC')
                                            ->get()
                                            ->all();  



                $details                = array();
                if(!empty($selected_services)){
                    foreach($selected_services as $key => $value)
                    {

                        $record             = DB::table('order_has_items')
                                                ->where('order_has_items.order_id', $id)
                                                ->where('order_has_items.service_id',  $value->service_id)
                                                ->select(['order_has_items.service_id',
                                                    DB::raw("SUM(order_has_items.pickup_qty) as pickup_qty"),
                                                    DB::raw("SUM(order_has_items.scan_qty) as scan_qty"),
                                                    DB::raw("SUM(order_has_items.bt_qty) as bt_qty"),
                                                    DB::raw("SUM(order_has_items.nr_qty) as nr_qty"),
                                                    DB::raw("SUM(order_has_items.hfq_qty) as hfq_qty"),
                                                ])
                                                ->groupBy('order_has_items.service_id')
                                                ->first();

                        $details[$key]       = array(
                            'service_id'     => $value->service_id,
                            'service_name'   => $value->service_name,
                            'service_weight' => $value->service_weight,
                            'pickup_qty'     => $record->pickup_qty,
                            'scan_qty'       => $record->scan_qty,
                            'bt_qty'         => $record->bt_qty,
                            'nr_qty'         => $record->nr_qty,
                            'hfq_qty'        => $record->hfq_qty,
                            );

                        $selected_items     = DB::table('order_has_items')
                                                ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                                ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                                ->where('order_has_items.order_id', $id)
                                                ->select('items.id as item_id',
                                                        'items.name as item_name',
                                                        'order_has_items.service_id as service_id',
                                                        'order_has_items.pickup_qty as pickup_qty',
                                                        'order_has_items.scan_qty as scan_qty',
                                                        'order_has_items.bt_qty as bt_qty',
                                                        'order_has_items.nr_qty as nr_qty',
                                                        'order_has_items.hfq_qty as hfq_qty',
                                                        'order_has_items.note as note',
                                                        'order_has_items.item_image as item_image',
                                                        'services.name as service_name')
                                                ->get()
                                                ->all(); 

                        $selected_addons    = DB::table('order_has_addons')
                                                ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                                ->where('order_has_addons.order_id', $id)
                                                ->select('addons.id as addon_id',
                                                        'addons.name as addon_name',
                                                        'order_has_addons.item_id as item_id',
                                                        'order_has_addons.service_id as service_id')
                                                ->get()
                                                ->all(); 

                        

                        
                    }
                    return view('orders.show',
                                    compact('data',
                                    'selected_services',
                                    'selected_items',
                                    'selected_addons',
                                    'histories',
                                    'details',
                                    'polybags')
                            );
                }else{
                    return view('orders.show',
                                    compact('data',
                                    'selected_services',
                                    'selected_items',
                                    'selected_addons',
                                    'histories',
                                    'details',
                                    'polybags')
                            );
                    
                }
            }else{
                return redirect()->route('orders.index')
                         ->with('permission','No Record Found!!!');
            }
    }


    public function fetch_history($history_id){
        $detail                 = null;
        $data              = DB::table('order_histories')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'order_histories.status_id')
                                    // ->leftjoin('users', 'users.id', '=', 'order_histories.created_by')
                                    // ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                    // ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                                      ->select(   
                                                'order_histories.*',
                                                'statuses.name as status_name',
                                                // 'users.name as user_name',
                                                // 'roles.name as role_name'
                                            )
                                    ->where('order_histories.id', $history_id)
                                    ->first();
                                    



        if(isset($data->id)){
            $user_id = $data->created_by;
    
            if($data->type == 1){ // "1" it is rider while "null" is user
                $user                   = DB::table('riders')
                                            ->where('riders.id', $user_id)
                                            ->select(
                                                        'riders.name as user_name'
                                                    )
                                            ->first();
                
            }else{
                $user                   = DB::table('users')
                                            ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                            ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                                            ->where('users.id', $user_id)
                                            ->select(
                                                        'users.name as user_name',
                                                        'roles.name as role_name',
                                                    )
                                            ->first();
                
            }

            if(isset($user->user_name)){
                $data->user_name = $user->user_name;
            }else{
                $data->user_name = "";
            }
            if(isset($user->role_name)){
                $data->role_name = $user->role_name;
            }else{
                $data->role_name = "Rider";
            }
        }
        if(isset($data->detail)){
            $detail             = json_decode($data->detail);
        }
        return view('orders.show_history',
                    compact('data',
                            'detail'
                        )
                    );
    }
 
    public function edit($id)
    {
        $data              = Order::orderBy('orders.id','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select('orders.*',
                                            'customers.name as name',
                                            'customers.contact_no')
                                    ->findOrFail($id);
                                   
        $statuses           = DB::table('statuses')
                                ->where('statuses.id', 1)
                                ->pluck('name','id')
                                ->all();

        $holidays           =  DB::table('holidays')
                                ->select('holiday_date')
                                ->get();
                                
        $time_slots         = DB::table('time_slots')
                                ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                ->pluck('name','id')
                                ->all();

        $areas              = DB::table('areas')
                                ->pluck('center_points','id')
                                ->all();
        
        return view('orders.edit',
                    compact('data',
                            'statuses',
                            'time_slots',
                            'holidays',
                            'areas'
                        )
                    );
    }

    public function assign_rider(){

        // $today      = date('Y-m-d');

        // $data       = Order::where('status_id', 1)
        //                 ->whereNull('orders.pickup_rider_id')
        //                 ->whereDate('orders.pickup_date',$today)
        //                 ->update(
        //                         ['pickup_rider_id' => '1']
        //                 );

        $orders     =  DB::table('route_plans')
                        ->select('route_plans.*')
                        ->get();

        foreach ($orders as $key => $value) {
            $riders     = DB::table('hub_has_riders')
                                ->leftjoin('riders', 'riders.id', '=', 'hub_has_riders.rider_id')
                                ->select('hub_has_riders.rider_id','riders.name')
                                ->where('hub_has_riders.hub_id',$value->hub_id)
                                ->first();
                               

            $route      = Route_plan::where('route_plans.order_id',$value->order_id)
                            ->whereNull('route_plans.rider_id')
                            ->update(
                                    ['rider_id' => $riders->rider_id]
                            );
            if($value->status_id == 1){

                $data       = Order::where('orders.id',$value->order_id)
                                ->whereNull('orders.pickup_rider_id')
                                ->update(
                                        ['pickup_rider_id' => $riders->rider_id,'pickup_rider' =>$riders->name]
                                );
            }else{
                $data       = Order::where('orders.id',$value->order_id)
                                ->whereNull('orders.delivery_rider_id')
                                ->update(
                                        ['delivery_rider_id' => $riders->rider_id,'delivery_rider' =>$riders->name]
                                );
            }
        }
        // dd($orders);

    
        if($data){
            $msg    = "Rider assigned successfully";
            $status = "success";
        }else{
            $msg    = "No order found!";
            $status = "permission";
            
        }

        return redirect()->route('csr_dashboards.index')
                         ->with($status,$msg);
    }

    public function update(Request $request, $id)
    {
        $data                       = Order::find($id);
        request()->validate([
            'customer_id'           => 'required',
            'pickup_address_id'     => 'required',
            'pickup_date'           => 'required|date',
            'delivery_date'         => 'required|date|after:pickup_date',
        ]);
        $inputs                     = $request->all();
        $pickup_address             = $this->get_address($inputs['pickup_address_id']);
        $pickup_timeslot            = $this->get_timeslot($inputs['pickup_timeslot_id']);
        
        $inputs['pickup_address']   = $pickup_address;
        $inputs['pickup_timeslot']  = $pickup_timeslot;
        


        $upd = $data->update($inputs);

        return redirect()->route('orders.index')
            ->with('success','Order of '.$request['name']. '  updated successfully');
       
    }

    public function destroy(Request $request)
    { 
        $id     = $request->ids;
        $order  = order::find($id);

                  DB::table("order_histories")->where('order_id', '=', $id)->delete();
        $data   = DB::table("orders")->whereIn('id',explode(",",$id))->delete();
        
        return response()->json(['success'=>$data." Order deleted successfully."]);
    }

}
