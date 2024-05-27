<?php
namespace App\Http\Controllers;
use DB;
use Auth;
use Validator;
use DataTables;
use App\Models\Order;
use App\Models\Status;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Models\Order_history;
use App\Models\Order_has_bag;
use App\Models\Order_has_item;
use App\Models\Order_has_addon;
use App\Http\Controllers\MailController;
use App\Models\Distribution_hub;
use App\Models\Complaint_nature;
use App\Models\Order_has_service;
use App\Http\Controllers\Controller;

use App\Http\Controllers\NotificationController;
class Order_inspectController extends Controller
{
    public $today;
    
    function __construct()
    {
         $this->middleware('permission:order_inspect-list', ['only' => ['index','show']]);
         $this->middleware('permission:order_inspect-create', ['only' => ['create','store']]);
         $this->middleware('permission:order_inspect-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:order_inspect-delete', ['only' => ['destroy']]);
    
         $this->today =  date('Y-m-d');
    }

    public function index(Request $request)
    {
        $user_id            = Auth::user()->id;
        $user               = DB::table('users')
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

        if($user->role_id  == 1){
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
        return view('order_inspects.index',compact('hubs'));
    }
    
 
    public function list($hub_id)
    {
                      DB::statement(DB::raw('set @srno=0'));
        $date       = date("Y-m-d");
        $data       = Order::orderBy('orders.updated_at')
                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                        ->leftjoin('statuses', 'statuses.id', '=', 'orders.status_id2')
                        ->leftjoin('wash_house_has_orders', 'wash_house_has_orders.order_id', '=', 'orders.id')
                        ->leftjoin('wash_houses', 'wash_houses.id', '=', 'wash_house_has_orders.wash_house_id')
                        ->select(
                                    DB::raw('@srno  := @srno  + 1 AS srno'),
                                    'orders.id',
                                    'customers.name',
                                    'customers.contact_no',
                                    'orders.id as order_id',
                                    'orders.pickup_date',
                                    'customers.permanent_note',
                                    'orders.order_note',
                                    'orders.delivery_date',
                                    'orders.delivery_date',
                                    'statuses.id as status_id2',
                                    DB::raw('(CASE 
                                            WHEN orders.status_id2 IS NULL THEN "Moved to hub" 
                                            ELSE statuses.name 
                                            END) AS status_name'
                                    ),
                                    DB::raw('(CASE 
                                                WHEN isNULL(orders.polybags_printed) THEN "No" 
                                                ELSE "Yes"
                                                END) AS polybags_printed'
                                    ),
                                )
                        // 10: Moved to wash-house
                        // 11: recieved to hub
                        // 12: content verified
                        ->where('orders.hub_id',$hub_id)
                        ->where('orders.tags_printed',1)
                        // ->whereNull('orders.polybags_printed')
                        ->whereIn('orders.status_id2',[2,3,4,5,6,10,11,12,13,14,16])
                        ->orWhereNull('orders.status_id2')

                        // ->whereIn('orders.status_id2',[10,11,12,13,14])
                        ->whereNull('orders.ref_order_id')
                        ->whereNotNull('orders.pickup_rider_id')
                        ->get();
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-primary btn-sm" href="order_inspects/'.$data->id.'">
                            <i class="fa fa-print"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm cls'.($data->status_id2).'" href="order_inspects/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                       
                        <a  href="/order_inspects/show_bags/'.$data->id.'" class="btn btn-primary btn-sm cls'.($data->status_id2).'">
                            <i class="fa fa-tags"></i>
                        </a>
                        <a  href="/order_inspects/special_show_bags/'.$data->id.'" class="btn btn-success btn-sm chk_prm cls'.($data->status_id2).'">
                            <i class="fa fa-tags"></i>
                        </a>
                        </div>';
                })
                ->addColumn('checkbox','<div class="checkbox-inline"> <label class="checkbox checkbox-success"><input type="checkbox" name="order_id[{{$id}}]" /><span></span> </label></div>')
                ->rawColumns(['checkbox','','action'])
                ->make(true);
            //     <a class="btn btn-success  btn-sm chk_prm" href="order_inspects/special_inspect/'.$data->id.'" id="'.$data->id.'">
            //     <i class="fas fa-pencil-alt"></i>
            // </a>
    }

    public function create(Request $request)
    {
          // Retrieve the IDs from the request
    $ids = $request->ids;

    // Check if IDs are provided
    if ($ids && count($ids) > 0) {
        try {
            $currentDateTime                = Carbon::now()->format('Y-m-d H:i:s');
            $user                           =Auth::user()->id;
            // Update orders with the provided IDs
            Order::whereIn('id', $ids)->update(['waver_delivery' => 1,'delivery_charges'=> 0,'phase'=> "Inspect Order",'DW_when'=>$currentDateTime,'DW_who'=>$user]);

            // Call send_invoice function for each order
            foreach ($ids as $order_id) {
                // Retrieve order details
                $order = Order::findOrFail($order_id);
                
                // Check if email alert is on for this order
                $email_alert = 1;

                // Send invoice and handle response
                if ($email_alert == 1) {
                    $mail = app('App\Http\Controllers\MailController')->send_invoice($order_id);
                    if ($mail == 1) {
                        $msg = "Order verified and email sent successfully.";
                    } else {
                        $msg = "Order verified but email not sent successfully.";
                    }
                } else {
                    $msg = "Order verified successfully.";
                }
            }

            // If all invoices sent successfully, return success response
            return response()->json(['success' => true, 'message' => 'Orders updated successfully and invoices sent']);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json(['success' => false, 'message' => 'Error updating orders: ' . $e->getMessage()]);
        }
    } else {
        // No IDs provided
        return response()->json(['success' => false, 'message' => 'No IDs were provided']);
    }
    }

    // updating order status for "receiving to hub from wash_houses" //
 
    public function store(Request $request)
    {
        $asgnd_ids = [];
        $not_asgnd_ids = [];
        $msg = "";
        $state = "error"; // Default state

        // Validating the request
        $validator = Validator::make($request->all(), [
            'hub_id' => 'required|numeric|min:1',
            'order_id' => 'required|array',
            'order_id.*' => 'numeric|min:1', // Validate each order ID in the array
        ], [
            'hub_id.required' => 'Please select hub id(s)!',
            'order_id.required' => 'Please select order(s)!',
        ]);

        if ($validator->passes()) {
            // 11: received to hub
            $status = 11;
            $order_ids = $request->input('order_id');

            foreach ($order_ids as $order_id) {
                // Find the order
                $order = Order::find($order_id);

                // Check if order exists
                if (!$order) {
                    array_push($not_asgnd_ids, $order_id);
                    continue;
                }

                // Check if the order is already received to hub
                $last_status = Order_history::where('order_id', $order_id)
                    ->where('status_id', $status)
                    ->first();

                if ($last_status) {
                    array_push($not_asgnd_ids, $order_id);
                    continue;
                }

                // Update order status
                $order->update(['status_id2' => $status]);

                // Insert order status in Order_history table
                $order_history = new Order_history();
                $order_history->order_id = $order_id;
                $order_history->created_by = Auth::user()->id;
                $order_history->status_id = $status;
                $order_history->save();

                array_push($asgnd_ids, $order_id);
            }

            // Generate response message
            if (count($asgnd_ids) > 0) {
                $state = "success";
                $msg = "Order(s) assigned successfully";
            } else {
                $msg = "No valid orders found or already received to hub";
            }
        } else {
            // Validation failed, generate error message
            $msg = $validator->errors()->first();
        }

        // Prepare response
        $response = [
            $state => $msg,
            'not_asgnd_ids' => $not_asgnd_ids
        ];

        return response()->json($response);
    }

    public function show($id)
    {
        $data                   = Order::orderBy('orders.created_at','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'orders.status_id2')
                                    ->select('orders.id',
                                            'customers.id as customer_id',
                                            'customers.name',
                                            'customers.contact_no',
                                            'orders.id as order_id',
                                            'orders.waver_delivery',
                                            'orders.pickup_date',
                                            'customers.permanent_note',
                                            'orders.order_note',
                                            'orders.delivery_date',
                                            'statuses.name as status_name')
                                    // ->whereNull('orders.delivery_rider_id')
                                    ->whereNotNull('orders.pickup_rider_id')
                                    ->find($id);

        if( $data){

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
                                        
                                        // echo $selected_services[0]->service_id;
                                        // dd($selected_services);
                                        
    
         
    
            $details                = array();
            foreach($selected_services as $key => $value)
                {
                 
                    $record         = DB::table('order_has_items')
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
    
                    $details[$key] = array(
                        'service_id'        => $value->service_id,
                        'service_name'      => $value->service_name,
                        'service_weight'    => $value->service_weight,
                        'pickup_qty'        => $record->pickup_qty,
                        'scan_qty'          => $record->scan_qty,
                        'bt_qty'            => $record->bt_qty,
                        'nr_qty'            => $record->nr_qty,
                        'hfq_qty'           => $record->hfq_qty,
                    );
                }
    
            $selected_items         = DB::table('order_has_items')
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
                                                 'services.name as service_name',
                                                 'order_has_items.id as ord_itm_id')
                                        ->get()
                                        ->all(); 
    
            $selected_addons        = DB::table('order_has_addons')
                                        ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                        ->where('order_has_addons.order_id', $id)
                                        ->select('addons.id as addon_id',
                                                 'addons.name as addon_name',
                                                 'order_has_addons.item_id as item_id',
                                                 'order_has_addons.service_id as service_id',
                                                 'order_has_addons.ord_itm_id as ord_itm_id')
                                        ->get()
                                        ->all(); 
    
            // $histories              = DB::table('order_histories')
            //                             ->leftjoin('statuses', 'statuses.id', '=', 'order_histories.status_id')
            //                             ->leftjoin('users', 'users.id', '=', 'order_histories.created_by')
            //                             ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            //                             ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            //                             ->where('order_histories.order_id', $id)
            //                             ->select(
            //                                     'order_histories.id as history_id',
            //                                     'statuses.id as status_id',
            //                                     'statuses.name as status_name',
            //                                     'users.name as user_name',
            //                                     'roles.name as role_name',
            //                                     'order_histories.detail',
            //                                     'order_histories.created_at as created_at'
            //                                     )
            //                             ->get()
            //                             ->all();
                                        // dd($selected_addons);
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
    
    
    
            
            return view('order_inspects.show',
                        compact('data',
                                'selected_services',
                                'selected_items',
                                'selected_addons',
                                'histories',
                                'details'
                            )
                        );
        }else{
            $status     = "permission";
            $msg        = "No order found!";

            return redirect()->route('order_inspects.index')
                         ->with($status,$msg);   
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
        return view('order_verifies.show_history',
                    compact('data',
                            'detail'
                        )
                    );
    }
    
    public function fetch_items(Request $request)
    {
        if($request->ajax()){

            $services       = Service::find($request->service_id);
            $service_name   = $services->name;
            $unit_id        = $services->unit_id;
            $service_id     = $request->service_id;
            $customer_id    = $request->customer_id;

            if($unit_id == 2){
                $items  = DB::table('customer_has_items')
                                ->leftjoin('items', 'items.id', '=', 'customer_has_items.item_id')
                                ->select('items.id','items.name')
                                ->where('customer_has_items.service_id',$service_id)
                                ->where('customer_has_items.customer_id',$customer_id)
                                ->pluck("name","id")
                                ->all();
            }else{
                $items  = DB::table('service_has_items')
                                ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                                ->select('items.id','items.name')
                                ->where('service_has_items.service_id',$service_id)
                                // ->where('customer_has_items.customer_id',$customer_id)
                                ->pluck("name","id")
                                ->all();
            }

           
            $service_items = view('order_inspects.ajax-items',compact('items'))->render();
            return response()->json(['data'=>$service_items,'service_name'=>$service_name]);
        }

    }

    public function edit($id)
    {

        
        $last_status                    = DB::table('order_histories')
                                            ->select('order_histories.status_id')
                                            ->where('order_histories.order_id',$id)
                                            ->where('order_histories.status_id', 11 )
                                            ->first();
                                            // 11 : recieved to hub
        if(isset($last_status->status_id)){
         
            $data                       = Order::orderBy('orders.created_at','DESC')
                                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                            ->leftjoin('wash_house_has_orders', 'wash_house_has_orders.order_id', '=', 'orders.id')
                                            ->leftjoin('wash_houses', 'wash_houses.id', '=', 'wash_house_has_orders.wash_house_id')
                                            ->select(
                                                        'orders.id',
                                                        'customers.id as customer_id',
                                                        'customers.name',
                                                        'customers.contact_no',
                                                        'orders.id as order_id',
                                                        'orders.pickup_date',
                                                        'customers.permanent_note',
                                                        'orders.order_note',
                                                        'orders.status_id2 as status_id',
                                                        'orders.delivery_date',
                                                        'orders.iron_rating',
                                                        'orders.softner_rating',
                                                        'orders.packed_weight',
                                                        'wash_houses.name as washhouse_name'
                                                    )
                                            // ->whereNull('orders.delivery_rider_id')
                                            ->whereNotNull('orders.pickup_rider_id')
                                            // ->whereIn('orders.status_id2',[10,11])   
                                            // ->where('orders.status_id2',11)  
                                            ->where('orders.id', $id)
                                            ->first();

                                            // 11 received to Hub
                                            // 12 content Inspected
        
            $customer_id                = $data->customer_id;
            $statuses                   = DB::table('statuses')
                                            ->where('statuses.id',12)
                                            ->pluck('name','id')
                                            ->all();

            $reasons                    = DB::table('reasons')
                                            ->pluck('name','id')
                                            ->all();

            $ratings                    = DB::table('ratings')
                                            ->pluck('name','id')
                                            ->all();
            
            //////////////////////////////////////////////////
            $order_tags                 = DB::table('order_has_tags')
                                            ->where('order_has_tags.order_id', $id)
                                            ->where('order_has_tags.tag_scanned',"!=", 1)
                                            ->select(
                                                        'order_has_tags.id',
                                                        'order_has_tags.order_id',
                                                        'order_has_tags.item_id',
                                                        'order_has_tags.service_id',
                                                        'order_has_tags.id as tag_code'
                                                    )
                                            ->pluck('tag_code')
                                            ->all();


            $tags_adn                 = DB::table('order_has_tags')
                                            ->join('order_has_addons', 'order_has_addons.ord_itm_id', '=', 'order_has_tags.ord_itm_id')
                                            ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                            ->where('order_has_tags.order_id', $id)
                                            ->whereNotNull('order_has_tags.ord_itm_id')
                                            ->where('order_has_tags.tag_scanned',"!=", 1)
                                            ->select(
                                                        'order_has_tags.id',
                                                        'addons.name',
                                                        'order_has_addons.ord_itm_id',
                                                    )
                                            ->get();

            
            $selected_services          = DB::table('order_has_services')
                                            ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                            ->where('order_has_services.order_id', $id)
                                            ->select('services.id as service_id',
                                                    'services.name as service_name',
                                                    'order_has_services.weight as service_weight',
                                                    'order_has_services.qty as service_qty')
                                            ->orderBy('order_has_services.order_number','ASC')
                                            ->get()
                                            ->all();        


            $selected_items             = DB::table('order_has_items')
                                            ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                            ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                            ->where('order_has_items.order_id', $id)
                                            ->select(
                                                    'order_has_items.id as ord_itm_id',
                                                    'items.id as item_id',
                                                    'items.name as item_name',
                                                    'order_has_items.service_id as service_id',
                                                    'order_has_items.pickup_qty as pickup_qty',
                                                    'order_has_items.scan_qty as scan_qty',
                                                    'order_has_items.bt_qty as bt_qty',
                                                    'order_has_items.nr_qty as nr_qty',
                                                    'order_has_items.hfq_qty as hfq_qty',
                                                    'order_has_items.reason as reason',
                                                    'order_has_items.reason_id as reason_id',
                                                    'services.name as service_name')
                                            ->get()
                                            ->all(); 

                                    
            return view('order_inspects.edit',
                        compact(
                                    'data',
                                    'reasons',
                                    'ratings',
                                    'tags_adn',
                                    'statuses',
                                    'order_tags',
                                    'selected_items',
                                    'selected_services',
                                    // 'order_scanned_tags'
                                )
                            );
        }else{
            $status     = "permission";
            $msg        = "No order found! First change its status to Received to Hub";
        }

        return redirect()->route('order_inspects.index')
                         ->with($status,$msg);   
    }

    public function get_tag_detail($tag_code){

        $data        = DB::table('order_has_tags')
                            ->where('order_has_tags.id',$tag_code)
                            ->first();

        return $data;
    }

    public function update_order_tags_status(Request $request)
    {

        // dd($request['scan_order_tag']);
        $tag                        = $request['scan_order_tag'][0]; 
        foreach($request['scan_order_tag'] as $key => $value){
            

            $data                       = $this->get_tag_detail($value);
            $order_id                   = $data->order_id;
            $service_id                 = $data->service_id;
            $item_id                    = $data->item_id;

            $qty                        = 1;
            $data                       = DB::table('order_has_tags')
                                            ->where('order_id',$order_id)
                                            ->where('service_id',$service_id)
                                            ->where('item_id',$item_id)
                                            ->where('tag_scanned',0)
                                            ->select('ord_itm_id')
                                            ->first();
            $upd                        = DB::table('order_has_tags')
                                            ->where('order_has_tags.id',$value)
                                            ->update(['tag_scanned' =>1]);

            if(isset($data->ord_itm_id)){
                $data2                 = DB::table('order_has_items')
                                            ->where('order_id',$order_id)
                                            ->where('service_id',$service_id)
                                            ->where('item_id',$item_id)
                                            ->where('id',$data->ord_itm_id)
                                            ->select('hfq_qty','pickup_qty','scan_qty','bt_qty','nr_qty')
                                            ->first();
                if(($data2->scan_qty )!= NULL){
                    $dl_qty = ( ($data2->scan_qty) + ($data2->bt_qty) + ($data2->nr_qty)+ ($data2->hfq_qty));
                    if( $data2->pickup_qty > $dl_qty ){
                        $qty = (($data2->scan_qty)+1);
                    }else{
                        $qty = ($data2->scan_qty);
                    }
                    
                }
                $upd                    = DB::table('order_has_items')
                                            ->where('order_id',$order_id)
                                            ->where('service_id',$service_id)
                                            ->where('item_id',$item_id)
                                            ->where('id',$data->ord_itm_id)
                                            ->update(['scan_qty' =>$qty]);
            }else{
                continue;
            }

        }
        return response()->json(['data'=>"updated"]);
    }

    public function update_tags_status_HFQ(Request $request)
    {
        $tag                        = $request['code']; 
       

        $data                       = $this->get_tag_detail($tag);
        $order_id                   = $data->order_id;
        $service_id                 = $data->service_id;
        $item_id                    = $data->item_id;
        $qty                        = 1;

        $data                       = DB::table('order_has_tags')
                                        ->where('order_id',$order_id)
                                        ->where('service_id',$service_id)
                                        ->where('item_id',$item_id)
                                        ->where('tag_scanned',0)
                                        ->select('ord_itm_id')
                                        ->first();
                                        // echo "done1";
        $upd                        = DB::table('order_has_tags')
                                        ->where('order_has_tags.id',$tag)
                                        ->update(['tag_scanned' =>1]);
                                        // echo "done2";
        if(isset($data->ord_itm_id)){
            $data2                 = DB::table('order_has_items')
                                        ->where('order_id',$order_id)
                                        ->where('service_id',$service_id)
                                        ->where('item_id',$item_id)
                                        ->where('id',$data->ord_itm_id)
                                        ->select('hfq_qty','pickup_qty','scan_qty','bt_qty','nr_qty')
                                        ->first();
                                        // echo "done3";
            if(($data2->hfq_qty)!= NULL){
                $dl_qty = (($data2->scan_qty) + ($data2->bt_qty) + ($data2->nr_qty)+ ($data2->hfq_qty));
                // echo  $dl_qty ;
                if(($data2->pickup_qty) > $dl_qty){
                    $qty = (($data2->hfq_qty)+1);
                    // echo "done4";
                }else{
                    // echo "else";
                    $qty = (($data2->hfq_qty));
                }
                
            }
            // echo "done5";
            $upd                    = DB::table('order_has_items')
                                        ->where('order_id',$order_id)
                                        ->where('service_id',$service_id)
                                        ->where('item_id',$item_id)
                                        ->where('id',$data->ord_itm_id)
                                        ->update(['hfq_qty' =>$qty]);
                                        // echo "done6";
            if($upd){
                return response()->json(['data'=>"updated"]);
            }else{
                return response()->json(['error'=>"not updated"]);
            }
        }else{
            return response()->json(['error'=>"not updated"]);
        }
    }

    public function show_bags($order_id)
    {
        $is_printed = Order::select('orders.polybags_printed')
                        ->find($order_id);
                       
        if($is_printed->polybags_printed==1){
            return redirect()
                    ->back()
                    ->with('permission','Polybags are already printed.');
        }elseif($is_printed->polybags_printed==0){

            $tot_bags   = DB::table("order_has_bags")
                            ->where('order_has_bags.order_id','=', $order_id)
                            ->count('order_has_bags.order_id');
            if($tot_bags<1){
                return redirect()
                        ->back()
                        ->with('permission','First Inpect the order.');
            }

            $scan_qty = DB::table("order_has_items")
                            ->where('order_has_items.order_id','=', $order_id)
                            ->sum('order_has_items.scan_qty');

            $bt_qty     = DB::table("order_has_items")
                            ->where('order_has_items.order_id','=', $order_id)
                            ->sum('order_has_items.bt_qty');

            $nr_qty     = DB::table("order_has_items")
                            ->where('order_has_items.order_id','=', $order_id)
                            ->sum('order_has_items.nr_qty');

            $packed_qty = ($scan_qty + $bt_qty + $nr_qty);

            $hfq_qty    = DB::table("order_has_items")
                            ->where('order_has_items.order_id','=', $order_id)
                            ->sum('order_has_items.hfq_qty');

            $bags       = DB::table("order_has_bags")
                            ->leftjoin('orders', 'orders.id', '=', 'order_has_bags.order_id')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->select(
                                    'order_has_bags.id',
                                    'order_has_bags.order_id',
                                    'customers.name',
                                    DB::raw('CONCAT(order_has_bags.id,"-",order_has_bags.order_id) as bag_code')
                                    )
                            ->where('order_has_bags.order_id','=', $order_id)
                            ->get();


            $upd    = DB::table('orders')
                        ->where('orders.id','=', $order_id)
                        ->update([
                                'polybags_printed'  => 1,
                                'status_id2'        => 14
                        ]);

            // Storing history
            // 13 : polybag printed
                $val                        = new Order_history();
                $val->order_id              = $order_id;
                $val->created_by            = Auth::user()->id;
                $val->status_id             = 13;
                $val->save();
            // 14: content packed 
                $val                        = new Order_history();
                $val->order_id              = $order_id;
                $val->created_by            = Auth::user()->id;
                $val->status_id             = 14;
                $val->save();
      
            
            return view('order_inspects.show_bag',
                    compact('packed_qty',
                            'hfq_qty',
                            'bags',
                            'tot_bags'
                        )
                    );   
        
        }
    }

    public function special_show_bags($order_id)
    {
        $tot_bags   = DB::table("order_has_bags")
                        ->where('order_has_bags.order_id','=', $order_id)
                        ->count('order_has_bags.order_id');
        if($tot_bags<1){
            return redirect()
                    ->back()
                    ->with('permission','First Inpect the order.');
        }

        $scan_qty = DB::table("order_has_items")
                        ->where('order_has_items.order_id','=', $order_id)
                        ->sum('order_has_items.scan_qty');

        $bt_qty     = DB::table("order_has_items")
                        ->where('order_has_items.order_id','=', $order_id)
                        ->sum('order_has_items.bt_qty');

        $nr_qty     = DB::table("order_has_items")
                        ->where('order_has_items.order_id','=', $order_id)
                        ->sum('order_has_items.nr_qty');

        $packed_qty = ($scan_qty + $bt_qty + $nr_qty);

        $hfq_qty    = DB::table("order_has_items")
                        ->where('order_has_items.order_id','=', $order_id)
                        ->sum('order_has_items.hfq_qty');

        $bags       = DB::table("order_has_bags")
                        ->leftjoin('orders', 'orders.id', '=', 'order_has_bags.order_id')
                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                        ->select(
                                'order_has_bags.id',
                                'order_has_bags.order_id',
                                'customers.name',
                                DB::raw('CONCAT(order_has_bags.id,"-",order_has_bags.order_id) as bag_code')
                                )
                        ->where('order_has_bags.order_id','=', $order_id)
                        ->get();

        $upd    = DB::table('orders')
                        ->where('orders.id','=', $order_id)
                        ->update([
                                'polybags_printed'  => 1,
                                'status_id2'        => 14
                        ]);
            
         // 13 : polybag printed
        // Storing history
        $val                        = new Order_history();
        $val->order_id              = $order_id;
        $val->created_by            = Auth::user()->id;
        $val->status_id             = 13;
        $val->save();
        // 14: content packed 
        $val                        = new Order_history();
        $val->order_id              = $order_id;
        $val->created_by            = Auth::user()->id;
        $val->status_id             = 14;
        $val->save();
        
        return view('order_inspects.show_bag',
                compact('packed_qty',
                        'hfq_qty',
                        'bags',
                        'tot_bags'
                    )
                );   
        
    }

    public function create_json_history($id)
    {
        // $id = 36;
        $services       = DB::table('order_has_services')
                                    ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                    ->where('order_has_services.order_id', $id)
                                    ->select('services.id as service_id',
                                            'services.name as service_name',
                                            'order_has_services.weight as service_weight',
                                            'order_has_services.qty as service_qty')
                                    ->get()
                                    ->all(); 

        
        $record         = array(); 
     
        $tot_weight     = 0;
        $tot_qty        = 0;

        $record['order_id']        = $id ;
        if(isset($services)){
            foreach ($services as $service_key  => $service_value) {

                $items          = DB::table('order_has_items')
                                    ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                    ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                    ->where('order_has_items.order_id', $id)
                                    ->where('order_has_items.service_id', $service_value->service_id)
                                    ->select('items.id as item_id',
                                             'items.name as item_name',
                                             'order_has_items.id as ord_itm_id',
                                             'order_has_items.service_id as service_id',
                                             'order_has_items.pickup_qty as pickup_qty',
                                             'order_has_items.note as note',
                                             'services.id as service_id',
                                             'services.name as service_name',
                                             )
                                    ->get()
                                    ->all(); 
                                    $addons = []; 
                foreach ($items as $item_key  => $item_value) {
                    $addons[]   = DB::table('order_has_addons')
                                    ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                        ->where('order_has_addons.order_id', $id)
                                        ->where('order_has_addons.ord_itm_id',$item_value->ord_itm_id)
                                        ->select(
                                                'addons.id as addon_id',
                                                'addons.name as addon_name',
                                                'order_has_addons.ord_itm_id as ord_itm_id',
                                                )
                                        ->get()
                                        ->all();

                }


                $record['services'][$service_value->service_name] =  array(
                    'service_id'        => $service_value->service_id,
                    'service_name'      => $service_value->service_name,
                    'service_weight'    => $service_value->service_weight,
                    'service_item_qty'  => $service_value->service_qty,
                    'items'             => $items,
                    'addons'            => $addons 
                ); 


                

                $tot_weight                 += $service_value->service_weight;
                $tot_qty                    +=  $service_value->service_qty;
            
            }  
        }
        $record['tot_weight']               = $tot_weight;
        $record['tot_qty']                  = $tot_qty;

        // dd($record);

        if(isset($record)){
            return json_encode($record);
        }else{
            return 0;
        }
       
    }

    function is_sunday($date) {
        $weekDay = date('w', strtotime($date));
        return ($weekDay == 0 );
    }

    function is_holiday($holidays,$date){
        if(!(empty($holidays))){
            foreach($holidays as $key => $value){
                if(($value->holiday_date) == $date){
                    return true;
                }
            }
        }
        return false;
    }

    public function get_delivery_date($pickup_date){
        
        $holidays           = DB::table('holidays')
                                ->select('holiday_date')
                                ->get()
                                ->all();
  
        $delivery_date      = $pickup_date;
        
        for($i=0; $i<4; $i++){
            get_cus_date:
            $delivery_date  = date('Y-m-d', strtotime($delivery_date. ' + 1 days'));
            // echo $delivery_date."<br>";
            $is_sunday      = $this->is_sunday($delivery_date);
            $is_holiday     = $this->is_holiday($holidays,$delivery_date);

            if($is_sunday  || $is_holiday ){
                goto get_cus_date;
                // $delivery_date  = date('Y-m-d', strtotime($delivery_date. ' + 1 days'));
            }
        }
        return $delivery_date;
    }
    
    public function inspect_order(Request $request)
    {
 
    
        $sms_hfq_qty = 0;
        $validator = Validator::make($request->all(), 
            [
                'polybags_qty'          => 'required|min:1|numeric',
                'packed_weight'         => 'required|min:0|numeric',
                'pickup_qty'            => 'required|array',
                'pickup_qty.*.*'        => 'required|min:0',

                'scan_qty'              => 'required|array',
                'scan_qty.*.*'          => 'required|min:0',

                'bt_qty'                => 'required|array',
                'bt_qty.*.*'            => 'required|min:0',

                'nr_qty'                => 'required|array',
                'nr_qty.*.*'            => 'required|min:0',

                'hfq_qty'               => 'required|array',
                'hfq_qty.*.*'           => 'required|min:0',
            ],
            [
                'pickup_qty.*.*.required'   => 'Something went wrong in pickup qty!',
                'scan_qty.*.*.required'     => 'Something went wrong in scan qty!',
                'bt_qty.*.*.required'       => 'Something went wrong in broken qty!',
                'nr_qty.*.*.required'       => 'Something went wrong in non-readable qty!',
                'hfq_qty.*.*.required'      => 'Something went wrong in HFQ qty!',
                
                'pickup_qty.*.*.min'        => 'pickup qty less than 0!',
                'scan_qty.*.*.min'          => 'Scan qty less than 0 !',
                'bt_qty.*.*.min'            => 'Broken qty less than 0!',
                'nr_qty.*.*.min'            => 'Non-readable qty less than 0!',
                'hfq_qty.*.*.min'           => 'HFQ qty less than 0!',
            ]
        );
        if ($validator->passes()) {
            if($request['item_id']){
                $validator = Validator::make($request->all(), 
                    [
                        'customer_id'          => 'required|min:1|numeric',
                    ]
                );
                if (!($validator->passes())) {
                    return response()->json(['error'=>[0=>'The customer id field is required']]);
                }
                // request()->validate([
                //     'customer_id' => 'required',
                // ]);
                $order_id                   = $request['order_id'];
                $data                       = Order::find($order_id);
                $input                      = $request->all();

                // Start of Array Inputs //
                    $services                   = $request['service_id'];
                    $items                      = $request['item_id'];
                    $pickup_qty                 = $request['pickup_qty'];
                    $scan_qty                   = $request['scan_qty'];
                    $bt_qty                     = $request['bt_qty'];
                    $nr_qty                     = $request['nr_qty'];
                    $hfq_qty                    = $request['hfq_qty'];
                    $reason                     = $request['reason'];
                    $ord_itm_id                 = $request['ord_itm_id'];
                    $qtys                       = $request['qty'];
                    $weights                    = $request['weight'];
                // End of Array Inputs //

                // Checking pickup qty are equal to other qty or not!
                foreach($services as $service_key => $service_value){
                    $input['order_id']      = $order_id;
                    $input['service_id']    = $service_value;

                    if($items[$service_key]){
                        foreach($items[$service_value] as $item_key => $item_value){
                            $pu_q = $pickup_qty[$service_key][$item_key];
                            $sc_q = $scan_qty[$service_key][$item_key];
                            $bt_q = $bt_qty[$service_key][$item_key];
                            $nr_q = $nr_qty[$service_key][$item_key];
                            $hf_q = $hfq_qty[$service_key][$item_key];
                            $tot  = ($sc_q + $bt_q + $nr_q + $hf_q );
                            if($pu_q != $tot){
                                $srvc   = DB::table('services')
                                            ->select('services.name')
                                            ->where('services.id',$service_key)
                                            ->first();

                                $itms   = DB::table('items')
                                            ->select('items.name')
                                            ->where('items.id',($item_value))
                                            ->first();

                                if(isset($srvc->name)){
                                    $srvc_name = $srvc->name;
                                }else{
                                    $srvc_name = $service_key;
                                }

                                if(isset($itms->name)){
                                    $itms_name = $itms->name;
                                }else{
                                    $itms_name = ($item_key+1);
                                }

                                return response()->json(['error'=>[0=>'Qty(s) are not match in Services: '.($srvc_name).' and item: '.($itms_name)]]);
                                // return redirect()
                                //     ->back()
                                //     ->withInput($request->input())
                                //     ->with('permission','Qty(s) are not match in Services: '.($srvc_name).' and item: '.($itms_name) );
                            }

                        }
                    }
                }

                $order['softner_rating']    = $request['softner_rating'];
                $order['iron_rating']       = $request['iron_rating'];
                $order['packed_weight']     = $request['packed_weight'];
                $order['status_id2']        = 12;
                
                $order['polybags_printed']  = null;
            

                $upd = $data->update($order);

                $val                        = new Order_history();
                $val->order_id              = $order_id;
                $val->created_by            = Auth::user()->id;
                $val->detail                = $this->create_json_history($order_id);
                $val->status_id             = 12;
                $val->save();

                
                foreach($services as $service_key => $service_value){
                    $input['order_id']      = $order_id;
                    $input['service_id']    = $service_value;

                    if($items[$service_key]){
                        foreach($items[$service_value] as $item_key => $item_value){
                            $input['item_id']       = $item_value;
                            $input['pickup_qty']    = $pickup_qty[$service_key][$item_key];
                            $input['scan_qty']      = $scan_qty[$service_key][$item_key];
                            $input['bt_qty']        = $bt_qty[$service_key][$item_key];
                            $input['nr_qty']        = $nr_qty[$service_key][$item_key];
                            $input['hfq_qty']       = $hfq_qty[$service_key][$item_key];
                            $input['reason']        = $reason[$service_key][$item_key];
                            $input['ord_itm_id']    = $ord_itm_id[$service_key][$item_key];
                        
                            // if hfq exist, create new order of the same order and put order id as reference
                            if($input['hfq_qty']>0){
                                $order              = Order::where('id',$input['order_id'])->first();
                                if($order){
                                    $ref_order      = Order::where('ref_order_id',   '=', $input['order_id'])->count();
                                    if($ref_order>0){
                                        // do nothing
                                    }else{
                                        // duplicating the order in orders tables
                                        $newOrder                       = $order->replicate();
                                        $newOrder->ref_order_id         = $input['order_id'];
                                        
                                        $newOrder->status_id            = 17;  // put HFQ status
                                        $newOrder->status_id2           = 17;
                                        // $newOrder->polybags_qty         = null;
                                        // $newOrder->pickup_date          = $this->today;
                                        $newOrder->delivery_date        = $this->get_delivery_date($this->today);
                                        
                                        $newOrder->delivery_rider       = null;
                                        $newOrder->delivery_rider_id    = null;

                                        $newOrder->delivery_timeslot    = null;
                                        $newOrder->delivery_timeslot_id = null;

                                        $newOrder->delivery_address     = null;
                                        $newOrder->delivery_address_id  = null;
                                        $newOrder->packed_weight        = null;
                                        $newOrder->vat_charges          = 0;
                                        $newOrder->delivery_charges     = 0;
                                        
                                        $newOrder->save();
                                        
                                    
                                        // insert order history in order_histories table
                                        $var                    = new Order_history();
                                        $var->order_id          = $newOrder->id;
                                        $var->created_by        = Auth::user()->id;
                                        $var->status_id         = 17;
                                        $var->save();

                                    }
                                }
                            
                            }

                                DB::table('order_has_items')
                                    ->where('order_id',   '=', $input['order_id'])
                                    ->where('service_id',  '=', $input['service_id'])
                                    ->where('item_id',    '=', $input['item_id'])
                                    ->where('id', '=', $input['ord_itm_id'])
                                    ->update([
                                                'scan_qty'  => $input['scan_qty'],
                                                'bt_qty'    => $input['bt_qty'],
                                                'nr_qty'    => $input['nr_qty'],
                                                'hfq_qty'   => $input['hfq_qty'],

                                            ]);

                            // $data                   = Order_has_item::where('order_id',   '=', $input['order_id'])
                            //                             -> where('service_id',  '=', $input['service_id'])
                            //                             -> where('item_id',    '=', $input['item_id'])
                            //                             -> first();

                            // $data->update($input);
                        }
                    }
                }
                DB::table("order_has_bags")->where('order_id', '=', $order_id)->delete();
                for($i=0; $i<$request['polybags_qty']; $i++){
                    $val                        = new Order_has_bag();
                    $val->order_id              = $order_id;
                    $val->save();
                }

                
                $rec              = Order::select('ref_order_id')->where('ref_order_id',$input['order_id'])->first();

                if(isset($rec->ref_order_id)){
                    (new NotificationController)->fn_hfq($input['order_id']);
                }
                // return redirect()
                //         ->route('order_inspects.index')
                //         ->with('success','Data updated successfully.');
                return response()->json(['success'=>'All orders have been finalized successfully.']);
        
            }else{
                return response()->json(['error'=>[0=>"No Item found."]]);
                // return redirect()
                //         ->back()
                //         ->with('permission','No Item found.');
            }
        }
        return response()->json(['error'=>$validator->errors()->all()]);
    }

    public function update(Request $request, $id)
    {
       
        $sms_hfq_qty = 0;
        $this->validate($request, 
            [
                'polybags_qty'          => 'required|min:1|numeric',
                'packed_weight'         => 'required|min:0|numeric',
                'pickup_qty'            => 'required|array',
                'pickup_qty.*.*'        => 'required|min:0',

                'scan_qty'              => 'required|array',
                'scan_qty.*.*'          => 'required|min:0',

                'bt_qty'                => 'required|array',
                'bt_qty.*.*'            => 'required|min:0',

                'nr_qty'                => 'required|array',
                'nr_qty.*.*'            => 'required|min:0',

                'hfq_qty'               => 'required|array',
                'hfq_qty.*.*'           => 'required|min:0',
            ],
            [
                'pickup_qty.*.*.required'   => 'Something went wrong in pickup qty!',
                'scan_qty.*.*.required'     => 'Something went wrong in scan qty!',
                'bt_qty.*.*.required'       => 'Something went wrong in broken qty!',
                'nr_qty.*.*.required'       => 'Something went wrong in non-readable qty!',
                'hfq_qty.*.*.required'      => 'Something went wrong in HFQ qty!',
                
                'pickup_qty.*.*.min'        => 'pickup qty less than 0!',
                'scan_qty.*.*.min'          => 'Scan qty less than 0 !',
                'bt_qty.*.*.min'            => 'Broken qty less than 0!',
                'nr_qty.*.*.min'            => 'Non-readable qty less than 0!',
                'hfq_qty.*.*.min'           => 'HFQ qty less than 0!',
            ]
        );
       

        if($request['item_id']){
            request()->validate([
                'customer_id' => 'required',
            ]);
            $order_id                   = $id;
            $data                       = Order::find($order_id);
            $input                      = $request->all();

            // Start of Array Inputs //
                $services                   = $request['service_id'];
                $items                      = $request['item_id'];
                $pickup_qty                 = $request['pickup_qty'];
                $scan_qty                   = $request['scan_qty'];
                $bt_qty                     = $request['bt_qty'];
                $nr_qty                     = $request['nr_qty'];
                $hfq_qty                    = $request['hfq_qty'];
                $reason                     = $request['reason'];
                $ord_itm_id                 = $request['ord_itm_id'];
                $qtys                       = $request['qty'];
                $weights                    = $request['weight'];
            // End of Array Inputs //

            // Checking pickup qty are equal to other qty or not!
            foreach($services as $service_key => $service_value){
                $input['order_id']      = $order_id;
                $input['service_id']    = $service_value;

                if($items[$service_key]){
                    foreach($items[$service_value] as $item_key => $item_value){
                        $pu_q = $pickup_qty[$service_key][$item_key];
                        $sc_q = $scan_qty[$service_key][$item_key];
                        $bt_q = $bt_qty[$service_key][$item_key];
                        $nr_q = $nr_qty[$service_key][$item_key];
                        $hf_q = $hfq_qty[$service_key][$item_key];
                        $tot  = ($sc_q + $bt_q + $nr_q + $hf_q );
                        if($pu_q != $tot){
                            $srvc   = DB::table('services')
                                        ->select('services.name')
                                        ->where('services.id',$service_key)
                                        ->first();

                            $itms   = DB::table('items')
                                        ->select('items.name')
                                        ->where('items.id',($item_value))
                                        ->first();

                            if(isset($srvc->name)){
                                $srvc_name = $srvc->name;
                            }else{
                                $srvc_name = $service_key;
                            }

                            if(isset($itms->name)){
                                $itms_name = $itms->name;
                            }else{
                                $itms_name = ($item_key+1);
                            }

                            return redirect()
                                ->back()
                                ->withInput($request->input())
                                ->with('permission','Qty(s) are not match in Services: '.($srvc_name).' and item: '.($itms_name) );
                        }

                    }
                }
            }

            $order['softner_rating']    = $request['softner_rating'];
            $order['iron_rating']       = $request['iron_rating'];
            $order['packed_weight']     = $request['packed_weight'];
            $order['status_id2']        = 12;
           

            $upd = $data->update($order);

            $val                        = new Order_history();
            $val->order_id              = $order_id;
            $val->created_by            = Auth::user()->id;
            $val->detail                = $this->create_json_history($order_id);
            $val->status_id             = 12;
            $val->save();

            
            foreach($services as $service_key => $service_value){
                $input['order_id']      = $order_id;
                $input['service_id']    = $service_value;

                if($items[$service_key]){
                    foreach($items[$service_value] as $item_key => $item_value){
                        $input['item_id']       = $item_value;
                        $input['pickup_qty']    = $pickup_qty[$service_key][$item_key];
                        $input['scan_qty']      = $scan_qty[$service_key][$item_key];
                        $input['bt_qty']        = $bt_qty[$service_key][$item_key];
                        $input['nr_qty']        = $nr_qty[$service_key][$item_key];
                        $input['hfq_qty']       = $hfq_qty[$service_key][$item_key];
                        $input['reason']        = $reason[$service_key][$item_key];
                        $input['ord_itm_id']    = $ord_itm_id[$service_key][$item_key];
                       
                        // if hfq exist, create new order of the same order and put order id as reference
                        if($input['hfq_qty']>0){
                            $order              = Order::where('id',$input['order_id'])->first();
                            if($order){
                                $ref_order      = Order::where('ref_order_id',   '=', $input['order_id'])->count();
                                if($ref_order>0){
                                    // do nothing
                                }else{
                                    // duplicating the order in orders tables
                                    $newOrder                       = $order->replicate();
                                    $newOrder->ref_order_id         = $input['order_id'];
                                    
                                    $newOrder->status_id            = 17;  // put HFQ status
                                    $newOrder->status_id2           = 17;
                                    // $newOrder->polybags_qty         = null;
                                    $newOrder->pickup_date          = $this->today;
                                    $newOrder->delivery_date        = $this->get_delivery_date($this->today);
                                     
                                    $newOrder->delivery_rider       = null;
                                    $newOrder->delivery_rider_id    = null;

                                    $newOrder->delivery_timeslot    = null;
                                    $newOrder->delivery_timeslot_id = null;

                                    $newOrder->delivery_address     = null;
                                    $newOrder->delivery_address_id  = null;
                                    $newOrder->packed_weight        = null;
                                    $newOrder->vat_charges          = 0;
                                    $newOrder->delivery_charges     = 0;
                                    
                                    $newOrder->save();
                                    
                                  
                                    // insert order history in order_histories table
                                    $var                    = new Order_history();
                                    $var->order_id          = $newOrder->id;
                                    $val->created_by        = Auth::user()->id;
                                    $var->status_id         = 17;
                                    $var->save();

                                }
                            }
                           
                        }

                            DB::table('order_has_items')
                                ->where('order_id',   '=', $input['order_id'])
                                ->where('service_id',  '=', $input['service_id'])
                                ->where('item_id',    '=', $input['item_id'])
                                ->where('id', '=', $input['ord_itm_id'])
                                ->update([
                                            'scan_qty'  => $input['scan_qty'],
                                            'bt_qty'    => $input['bt_qty'],
                                            'nr_qty'    => $input['nr_qty'],
                                            'hfq_qty'   => $input['hfq_qty'],

                                        ]);

                        // $data                   = Order_has_item::where('order_id',   '=', $input['order_id'])
                        //                             -> where('service_id',  '=', $input['service_id'])
                        //                             -> where('item_id',    '=', $input['item_id'])
                        //                             -> first();

                        // $data->update($input);
                    }
                }
            }
            DB::table("order_has_bags")->where('order_id', '=', $order_id)->delete();
            for($i=0; $i<$request['polybags_qty']; $i++){
                $val                        = new Order_has_bag();
                $val->order_id              = $order_id;
                $val->save();
            }

            
            $rec              = Order::select('ref_order_id')->where('ref_order_id',$input['order_id'])->first();

            if(isset($rec->ref_order_id)){
                (new NotificationController)->fn_hfq($input['order_id']);
            }
            return redirect()
                    ->route('order_inspects.index')
                    ->with('success','Data updated successfully.');
     
        }else{
            return redirect()
                    ->back()
                    ->with('permission','No Item found.');
        }
    }

    public function destroy(Request $request)
    {
       
    }

      

    
}
