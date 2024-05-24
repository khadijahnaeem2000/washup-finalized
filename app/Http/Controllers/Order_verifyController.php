<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Validator;
use Carbon\Carbon;
use DataTables;
use App\Models\Order;
use App\Models\Status;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Complaint;
use App\Models\Zone_has_area;
use App\Models\Order_history;
use App\Models\Order_has_tag;
use App\Models\Order_has_item;
use App\Models\Order_has_addon;
use App\Models\Distribution_hub;
use App\Models\Complaint_nature;
use App\Models\Order_has_service;
use App\Models\Wash_house_has_zone;
use App\Mail\customMail;
use App\Http\Controllers\MailController;
use App\Models\Customer_has_wallet;
use App\Models\Wash_house_has_order;
use App\Models\Wash_house_has_hub;
use App\Models\Wash_house;
use App\Http\Controllers\NotificationController;


class Order_verifyController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:order_verify-list', ['only' => ['index','show']]);
         $this->middleware('permission:order_verify-create', ['only' => ['create','store']]);
         $this->middleware('permission:order_verify-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:order_verify-delete', ['only' => ['destroy']]);
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
        return view('order_verifies.index',
                        compact('hubs')
                    );
    }


       public function waver_deliverys(Request $request)
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
                $email_alert = $this->is_email_alert_on($order_id);

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
    public function list($hub_id)
    {
                  DB::statement(DB::raw('set @srno=0'));
        $date   = date("Y-m-d");
        $data   = DB::table('orders')
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
                             'statuses.id as status_id',
                             'statuses.name as status_name',
                             'wash_houses.name as wash_house_name',
                            )
                    ->where('orders.hub_id',$hub_id)
                    ->whereIn('orders.status_id', [4])
                    ->whereIn('orders.status_id2', [7,8,9,10])
                    ->whereNull('orders.delivery_rider_id')
                    ->whereNotNull('orders.pickup_rider_id')
                    // ->whereDate('orders.delivery_date', '>', $date)
                    ->get();
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-primary btn-sm" href="order_verifies/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="order_verifies/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                       
                        <a  href="/order_verifies/show_tags/'.$data->id.'" class="btn btn-primary  btn-sm">
                            <i class="fa fa-tags"></i>
                        </a>
                        <a  href="/order_verifies/special_show_tags/'.$data->id.'" class="btn btn-success  btn-sm chk_prm">
                            <i class="fa fa-tags"></i>
                        </a>
                    </div>';
                })

                 ->addColumn('checkbox','<div class="checkbox-inline"> <label class="checkbox checkbox-success"><input type="checkbox" name="order_id[{{$id}}]" /><span></span> </label></div>')
                ->rawColumns(['checkbox','','action'])
                ->make(true);
            //     <a class="btn btn-success  btn-sm chk_prm" href="order_verifies/special_verify/'.$data->id.'" id="'.$data->id.'">
            //     <i class="fas fa-pencil-alt"></i>
            // </a>
    }

    public function create()
    {
        $data                   = Order::orderBy('orders.id','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select('orders.id',
                                             'orders.id as order_id',
                                             'orders.pickup_date',
                                             'orders.delivery_date',
                                             'customers.id as customer_id',
                                             'customers.name',
                                             'customers.contact_no'
                                            )
                                    ->find($id);
        $order_detail_natures   = Complaint_nature::pluck('name','id')->all();
        $statuses               = DB::table('statuses')
                                    ->where('statuses.id', 1)
                                    // ->orWhere('statuses.id', 3)
                                    ->pluck('name','id')
                                    ->all();
                                    // ->take(1)
                                
        $time_slots             = DB::table('time_slots')
                                    ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                    ->pluck('name','id')
                                    ->all();
                
        return view('order_verifies.create',compact('statuses','data','order_detail_natures'));
    }

            public function waver_delivery_request(Request $request)
            {
    // Retrieve the IDs from the request
    $ids = $request->ids;

    // Check if IDs are provided
    if ($ids && count($ids) > 0) {
        try {
            $currentDateTime                = Carbon::now()->format('Y-m-d H:i:s');
            $user                           =Auth::user()->id;
            // Update orders with the provided IDs
            Order::whereIn('id', $ids)->update(['waver_delivery' => 1,'delivery_charges'=> 0,'phase'=> "Verify Order",'DW_when'=>$currentDateTime,'DW_who'=>$user]);

            // Call send_invoice function for each order
            foreach ($ids as $order_id) {
                // Retrieve order details
                $order = Order::findOrFail($order_id);
                
                // Check if email alert is on for this order
                $email_alert = $this->is_email_alert_on($order_id);

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

    public function store(Request $request)
    {
    }

    public function calc_order_pickups($order_id){
        // this function sums(addition) that how much order's pick_qty
        $data       = DB::table('order_has_items')
                        ->select(DB::raw('SUM(order_has_items.pickup_qty) as pickup_qty'))
                        ->where('order_has_items.order_id', $order_id)
                        ->first(); 

        if($data){
            return $data->pickup_qty;
        }else{
            return 0;
        }
    }

    public function special_verify($id)
    {
    
        $data                       = Order::orderBy('orders.created_at','DESC')
                                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                        ->select('orders.id',
                                                'customers.id as customer_id',
                                                'customers.name',
                                                'customers.contact_no',
                                                'orders.id as order_id',
                                                'orders.pickup_date',
                                                'customers.permanent_note',
                                                'orders.order_note',
                                                'orders.rider_note',
                                                'orders.delivery_date')
                                        ->whereNull('orders.delivery_rider_id')
                                        ->whereNotNull('orders.pickup_rider_id')
                                        // ->where('orders.status_id2', 7)
                                        ->whereIn('orders.status_id2', [7,8,9,10])
                                        ->find($id);

        if($data){
            $holidays              =  DB::table('holidays')
                                        ->select('holiday_date')
                                        ->get();

            $customer_id            = $data->customer_id;
            $statuses               = DB::table('statuses')
                                        ->where('statuses.id', 8)
                                        // ->whereBetween('statuses.id', [12, 15])
                                        ->pluck('name','id')
                                        ->all();

            $services               = DB::table('customer_has_services')
                                        ->leftjoin('services', 'services.id', '=', 'customer_has_services.service_id')
                                        ->orderBy('customer_has_services.order_number','ASC')
                                        ->select('services.id','services.name','services.rate')
                                        ->where('customer_has_services.status','1')
                                        ->where('customer_has_services.customer_id', $customer_id)
                                        // ->where('services.unit_id','2')
                                        ->pluck('name','id')
                                        ->all();
                                        
        
           
                
            $customer_items            = array();
                                     
            

            $items                     = DB::table('service_has_items')
                                            ->orderBy('service_has_items.item_id')
                                            ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                                            ->select(
                                                    'items.id as item_id',
                                                    'items.name as item_name',
                                                    'service_has_items.service_id as service_id'
                                                    )
                                            ->get()
                                            ->all();

            $addons                    = DB::table('service_has_addons')
                                            ->orderBy('service_has_addons.item_id')
                                            ->leftjoin('addons', 'addons.id', '=', 'service_has_addons.addon_id')
                                            ->select(
                                                    'addons.id as addon_id' ,
                                                    'addons.name as addon_name',
                                                    'service_has_addons.item_id as item_id',
                                                    'service_has_addons.service_id as service_id'
                                                    )
                                            ->get()
                                            ->all();
            
            $selected_services         = DB::table('order_has_services')
                                            ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                            ->where('order_has_services.order_id', $id)
                                            ->select('services.id as service_id',
                                                    'services.name as service_name',
                                                    'order_has_services.weight as service_weight',
                                                    'order_has_services.qty as service_qty')
                                            ->orderBy('order_has_services.order_number','ASC')
                                            ->get()
                                            ->all();        

            $selected_items            = DB::table('order_has_items')
                                            ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                            ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                            ->where('order_has_items.order_id', $id)
                                            ->select(
                                                    'items.id as item_id',
                                                    'items.name as item_name',
                                                    'order_has_items.id as ord_itm_id',
                                                    'order_has_items.note as note',
                                                    'services.name as service_name',
                                                    'order_has_items.id as ord_itm_id',
                                                    'order_has_items.service_id as service_id',
                                                    'order_has_items.pickup_qty as pickup_qty',
                                                    'order_has_items.item_image as item_image',
                                                    )
                                            ->get()
                                            ->all(); 

            $selected_addons           = DB::table('order_has_addons')
                                            ->orderBy('order_has_addons.service_id')
                                            ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                            ->where('order_has_addons.order_id', $id)
                                            ->select(
                                                    'addons.id as addon_id',
                                                    'addons.name as addon_name',
                                                    'order_has_addons.item_id as item_id',
                                                    'order_has_addons.service_id as service_id',
                                                    'order_has_addons.ord_itm_id as ord_itm_id'
                                                    )
                                            // ->groupBy('order_has_addons.ord_itm_id as ord_itm_id')
                                            ->get();
                                            // dd($selected_addons);
                                            
            $adn                    = array();
            $ord_adn                = DB::table('order_has_addons')
                                        ->select(
                                                // 'order_has_addons.item_id as item_id',
                                                    'order_has_addons.ord_itm_id as ord_itm_id'
                                                )
                                        ->groupBy('order_has_addons.ord_itm_id')    
                                        ->where('order_has_addons.order_id', $id)
                                        ->get()->all();
                           
            if(!empty($ord_adn)){
                foreach ($ord_adn as $adn_key => $adn_value) {
                    $rec = '';$inc = 0;
                    if(!empty($selected_addons)){
                        foreach($selected_addons as $key =>$value){
                            if($adn_value->ord_itm_id == $value->ord_itm_id){
                                if($inc ==0){
                                    $rec = (string)$value->addon_id;
                                }else{
                                    $rec = $rec  . ',' . $value->addon_id;
                                }
                                $inc++;
                            }
                            $adn[$adn_value->ord_itm_id] = $rec;
                        }
                    }
                }
            }
            return view('order_verifies.edit',
                    compact(
                            'adn',
                            'data',
                            'items', 
                            'addons',
                            'statuses',
                            'services',
                            'holidays',
                            'selected_items',
                            'selected_services',
                            'selected_addons'
                        )
                    );
        }else{
            return redirect()->route('order_verifies.index')
                    ->with('permission','Order Details already appended');
        }
    }


    public function calc_wash_house_pickups($wash_house_id){
        // this function sums(addition) that how much order's pick_qty are assigned to wash house
        $date       = date("Y-m-d");
        $data       = DB::table('wash_house_has_orders')
                        // ->orderBy('wash_house_has_orders.wash_house_id')
                        ->leftjoin('order_has_items', 'order_has_items.order_id', '=', 'wash_house_has_orders.order_id')
                        ->leftjoin('orders', 'orders.id', '=', 'order_has_items.order_id')
                        ->select(DB::raw('SUM(order_has_items.pickup_qty) as pickup_qty'))
                        ->groupBy('wash_house_has_orders.wash_house_id')
                        ->where('wash_house_has_orders.wash_house_id', $wash_house_id)
                        ->where('orders.status_id2','=', 10)
                        // ->whereDate('orders.delivery_date', '>', $date)
                        ->first(); 
                        
        if($data){
            return $data->pickup_qty;
        }else{
            return 0;
        }
    }

    public function find_capacity($wash_house_id){
        
        $wash_houses    = DB::table('wash_houses')
                            ->select('capacity')
                            ->where('wash_houses.id', $wash_house_id)
                            ->first();

        if($wash_houses){
            return $wash_houses->capacity;
        }else{
            return 0;
        }
    }

    public function has_whs_servs_n_adns($wash_house_id, $order_id){
        $ord_adns       = DB::table('order_has_addons')
                            ->where('order_has_addons.order_id', $order_id)
                            ->select(
                                    'order_has_addons.addon_id'
                                    )
                            ->groupBy('order_has_addons.addon_id')
                            ->get();

        $ord_serv       = DB::table('order_has_services')
                            ->where('order_has_services.order_id', $order_id)
                            ->select(
                                    'order_has_services.service_id'
                                    )
                            ->groupBy('order_has_services.service_id')
                            ->get();

        $whs_adns       = DB::table('wash_house_has_addons')
                            ->where('wash_house_has_addons.wash_house_id', $wash_house_id)
                            ->select(
                                    'wash_house_has_addons.addon_id'
                                    )
                            ->groupBy('wash_house_has_addons.addon_id')
                            ->get();
                            
        $whs_serv       = DB::table('wash_house_has_services')
                            ->where('wash_house_has_services.wash_house_id', $wash_house_id)
                            ->select(
                                    'wash_house_has_services.service_id'
                                    )
                            ->groupBy('wash_house_has_services.service_id')
                            ->get();
                       
        if(isset($ord_serv)){
            foreach($ord_serv as $serv_key => $serv_value){
                $chk    = false;
                foreach($whs_serv as $whs_key => $whs_value){
                    if($serv_value->service_id == $whs_value->service_id){
                        $chk = true;
                    }
                }
                if($chk == false){
                    // echo "Wash-house is not able to take this order";
                    // return "Wash-house does not have required services";
                    return -1;
                }
            }
        }
        if(isset($ord_adns)){
            foreach($ord_adns as $serv_key => $serv_value){
                $chk    = false;
                foreach($whs_adns as $whs_key => $whs_value){
                    if($serv_value->addon_id == $whs_value->addon_id){
                        $chk = true;
                    }
                }
                if($chk == false){
                    // echo "Wash-house is not able to take addon this order";
                    // return "Wash-house does not have required addons";
                    return -1;
                }
            }
        }
        return 0;
    }

    public function find_n_assign_wash_house($hub_id, $order_id)
    {
        if(isset($hub_id)){
            // Get Area id of order
            $ord                = Order::select('orders.area_id','orders.hub_id')->find($order_id);


            // dd($ord->area_id);
            if(isset($ord->area_id)){
                // Get Wash house id having same area_id as order has.
                $wash_houses    = wash_house_has_hub::select('wash_house_has_hubs.wash_house_id')
                                    ->leftjoin('wash_house_has_zones', 'wash_house_has_zones.wash_house_id', '=', 'wash_house_has_hubs.wash_house_id')
                                    ->leftjoin('zone_has_areas', 'zone_has_areas.zone_id', '=', 'wash_house_has_zones.zone_id')
                                    ->where('zone_has_areas.area_id',($ord->area_id))
                                    ->where('wash_house_has_hubs.hub_id',$hub_id)
                                    ->first();
                                    
                
                                   
                if(isset($wash_houses->wash_house_id)){
                    $wash_house_id              = $wash_houses->wash_house_id;
                    // BEGIN :: Assign wash-houses to selected orders//
                 
                        $tot_odr_pickups        = $this->calc_order_pickups(  $order_id );
                        $tot_wsh_pickups        = $this->calc_wash_house_pickups(  $wash_house_id );
                        $wsh_capacity           = $this->find_capacity(  $wash_house_id );
                
                        $free_capacity          = $wsh_capacity - $tot_wsh_pickups ;
                      
                        if( $tot_odr_pickups   <= $free_capacity){
                            $chk                = $this->has_whs_servs_n_adns($wash_house_id, $order_id);

                         
                            if($chk != -1){  // -1  means all services and addon are NOT available in this wash-house
                                // Moved to Wash-house
                                $input['order_id']      = $order_id;
                                $input['status_id2']    = 10;
                                $input['wash_house_id'] = $wash_house_id;
        
                                
                                //Update order status in Orders table//
                                $order                  = Order::find($order_id);
                                                          $order->update($input);
                                
                                // insert a row having order_id and wash_house_id when order is NOT available in wash_has_order table
                                $check                  = Wash_house_has_order::where('order_id',$order_id)->first();
                                if(isset($chk)){
                                    DB::table("wash_house_has_orders")
                                            ->where('order_id', '=', $order_id)
                                            ->delete();
                                }
                                $var                    = new Wash_house_has_order();
                                $var->wash_house_id     = $wash_house_id;
                                $var->order_id          = $order_id;
                                $var->save();
        
                                // 10: moved to washhouse
                                //Insert order status in Order_history table//
                                $val                    = new Order_history();
                                $val->order_id          = $order_id;
                                $val->created_by        = Auth::user()->id;
                                $val->status_id         = 10;
                                $val->save();
                                return $wash_house_id;
                            }
                        }
                    // END :: Assign wash-houses to selected orders//
                }

            }


            $wash_houses    = wash_house_has_hub::select('wash_house_has_hubs.wash_house_id')
                                ->where('wash_house_has_hubs.hub_id',$hub_id)
                                ->get();

            foreach($wash_houses as $key    => $value){
                $wash_house_id              = $value->wash_house_id;
                // BEGIN :: Assign wash-houses to selected orders//
                    $tot_wsh_pickups        = 0;
                    $tot_odr_pickups        = 0;
                    $wsh_capacity           = 0;
                    if($order_id){
                        $tot_odr_pickups   += $this->calc_order_pickups(  $order_id );
                        $tot_wsh_pickups   += $this->calc_wash_house_pickups(  $wash_house_id );
                        $wsh_capacity       = $this->find_capacity(  $wash_house_id );
                    }
                
                    $free_capacity          = $wsh_capacity - $tot_wsh_pickups ;
                    
                    if( $tot_odr_pickups   <= $free_capacity){
                        $chk                = $this->has_whs_servs_n_adns($wash_house_id, $order_id);
                        if($chk == -1){
                            continue;
                        }

                        // Moved to Wash-house
                        $input['order_id']      = $order_id;
                        $input['status_id2']    = 10;
                        $input['wash_house_id'] = $wash_house_id;

                        //Update order status in Orders table//
                        $order                  = Order::find($order_id);
                                                    $order->update($input);
                        
                        // insert a row having order_id and wash_house_id when order is not available in wash_has_order table
                        $check                  = Wash_house_has_order::where('order_id',$order_id)->first();
                        if(isset($chk)){
                            DB::table("wash_house_has_orders")
                                    ->where('order_id', '=', $order_id)
                                    ->delete();
                        }
                        $var                    = new Wash_house_has_order();
                        $var->wash_house_id     = $wash_house_id;
                        $var->order_id          = $order_id;
                        $var->save();

                        // 10: moved to washhouse
                        //Insert order status in Order_history table//
                        $val                    = new Order_history();
                        $val->order_id          = $order_id;
                        $val->created_by        = Auth::user()->id;
                        $val->status_id         = 10;
                        $val->save();
                        return $wash_house_id;
                    }
                // END :: Assign wash-houses to selected orders//
            }
            
        }else{
            return 0;
        }
    }

    public function add_wh_item_rate($wh_id, $id){
        $items          = DB::table('order_has_items')
                            ->orderBy('order_has_items.service_id','ASC')
                            ->where('order_has_items.order_id', $id)
                            ->select(
                                        'order_has_items.id as ord_itm_id',
                                        'order_has_items.item_id as item_id',
                                        'order_has_items.service_id as service_id',
                                        'order_has_items.order_id as order_id',
                                    )
                            ->get()
                            ->all(); 
                            
        foreach ($items as $key => $value) {
            $list       = DB::table('rate_lists')
                            ->where('rate_lists.wash_house_id', $wh_id)
                            ->where('rate_lists.item_id', $value->item_id)
                            ->where('rate_lists.service_id', $value->service_id)
                            ->select('rate_lists.rate as wh_item_rate')
                            ->first();

            if(isset($list->wh_item_rate)){
                $data   = DB::table('order_has_items')
                            ->where('order_has_items.order_id', $id)
                            ->where('order_has_items.item_id', $value->item_id)
                            ->where('order_has_items.service_id', $value->service_id)
                            ->update(['order_has_items.wh_item_rate'=> $list->wh_item_rate]);
            }
                        
        }
        return true;

    }

    public function add_wh_addon_rate($wh_id, $id){
        $addons         = DB::table('order_has_addons')
                            ->orderBy('order_has_addons.service_id','ASC')
                            ->where('order_has_addons.order_id', $id)
                            ->select(
                                        'order_has_addons.item_id as item_id',
                                        'order_has_addons.addon_id as addon_id',
                                        'order_has_addons.order_id as order_id',
                                        'order_has_addons.service_id as service_id',
                                    )
                            ->get()
                            ->all(); 
                            
        foreach ($addons as $key => $value) {
            $list       = DB::table('addon_rate_lists')
                            ->where('addon_rate_lists.wash_house_id', $wh_id)
                            ->where('addon_rate_lists.addon_id', $value->addon_id)
                            ->select('addon_rate_lists.rate as wh_addon_rate')
                            ->first();

            if(isset($list->wh_addon_rate)){
                $data   = DB::table('order_has_addons')
                            ->where('order_has_addons.order_id', $id)
                            ->where('order_has_addons.item_id', $value->item_id)
                            ->where('order_has_addons.service_id', $value->service_id)
                            ->where('order_has_addons.addon_id', $value->addon_id)
                            ->update(['order_has_addons.wh_addon_rate'=> $list->wh_addon_rate]);
            }
                        
        }
        return true;

    }

    // tag are also created and inserted in this function when tags are not printed
    public function show_tags($id)
    {
        $data                   = Order::orderBy('orders.created_at','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select('orders.id',
                                            'orders.hub_id',
                                            'customers.name',
                                            'orders.tags_printed',
                                            'orders.status_id',
                                            'orders.status_id2')
                                    ->whereNull('orders.delivery_rider_id')
                                    ->whereNotNull('orders.pickup_rider_id')
                                    ->find($id);

        // 8: content verified
        // 9: Tags printed
        if($data){

            if($data->status_id2 == 8){
                if($data->tags_printed == 1){
                    return redirect()
                        ->back()
                        ->with('permission','Tags already printed.');
                }else{
                    //insert order status in Order_history table//
                    if( $data->status_id2 == 8 ){
                        $val                     = new Order_history();
                        $val->order_id           = $id;
                        $val->created_by         = Auth::user()->id;
                        // $val->detail             = $this->create_json_history($id);
                        $val->status_id          = 9;
                        $val->save();
                    }
                    //BEGIN :: Update order status in Orders table//
                    $input['status_id2']    = 9;
                    $input['tags_printed']  = 1;
                    $data->update($input);
                    //END :: Update order status in Orders table//
    
    
                    //BEGIN :: fetching order items with services
                    $selected_items         = DB::table('order_has_items')
                                                ->where('order_has_items.order_id', $id)
                                                ->select(
                                                            'order_has_items.id as ord_itm_id',
                                                            'order_has_items.item_id as item_id',
                                                            'order_has_items.service_id as service_id',
                                                            'order_has_items.pickup_qty as pickup_qty',
                                                        )
                                                ->orderBy('order_has_items.service_id','ASC')
                                                ->get()
                                                ->all(); 
                    //END :: fetching order items with services
                    
    
                    if($selected_items){
                        DB::table("order_has_tags")->where('order_id', '=', $id)->delete();
                        foreach($selected_items as $value){
                            for($i= $value->pickup_qty; $i>0; $i--){
                                // Storing tag in tables
                                
                                $val                = new Order_has_tag();
                                $val->order_id      = $id;
                                $val->item_id       = $value->item_id;
                                $val->service_id    = $value->service_id;
                                $val->ord_itm_id    = $value->ord_itm_id;
                                $val->save();
                            }
                        }
                    }
    
          
    
                    // assigning wash-house to order
                    $wh_id                  =   $this->find_n_assign_wash_house($data->hub_id, $id);
                    // fetching all items and their rates from "rate_list" table and insert item's rate in "order_has_items" table
                                                $this->add_wh_item_rate($wh_id, $id);
                    // getting all addons and their rates from "addon_rate_list" table and insert addons's rate in "order_has_addons" table
                                                $this->add_wh_addon_rate($wh_id, $id);
    
                    $tags                   = DB::table('order_has_tags')
                                                ->leftjoin('items', 'items.id', '=', 'order_has_tags.item_id')
                                                ->leftjoin('services', 'services.id', '=', 'order_has_tags.service_id')
                                                ->where('order_has_tags.order_id', $id)
                                                ->select(
                                                            'order_has_tags.id',
                                                            'order_has_tags.order_id',
                                                            'items.id as item_id',
                                                            'items.short_name as item_name',
                                                            'services.id as service_id',
                                                            'services.name as service_name',
                                                            'services.web_image as service_image',
                                                            'order_has_tags.id as tag_code'
                                                        )
                                                ->orderBy('order_has_tags.id','ASC')
                                                ->get()
                                                ->all(); 
    
                    $tot_tags               = DB::table('order_has_tags')
                                                ->where('order_has_tags.order_id', $id)
                                                ->count();

                                             
                    if($wh_id != 0){
                        $type               = "success";
                        $msg                = "Wash-house assigned"; 
                        $chk                = Wash_house::where('id', $wh_id)
                                                ->select('name')
                                                ->get()
                                                ->first();
                                         
                        if(isset($chk->name)){
                            $msg            = "Order assigned to ". $chk->name ." successfully!";
                        }
                      
                    }else{
                        $type               = "permission";
                        $msg                = "Wash-house not assigned";
                    }
    
                    return view('order_verifies.show_tag',
                                            compact('data','tot_tags','tags'),
                                            [$type=>$msg]
                                            );
                }
                  
            }else{
                return redirect()
                ->back()
                ->with('permission','Please verify the content first.');
            }
        }else{
                return redirect()
                ->back()
                ->with('permission','Data not found.');
        }


    }

    // tag are also created and inserted in this function however tags are printed or not
    public function special_show_tags($id)
    {

        
        $data       = Order::orderBy('orders.created_at','DESC')
                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                        ->select('orders.id',
                                'customers.name',
                                'orders.tags_printed',
                                'orders.hub_id',
                                'orders.status_id',
                                'orders.status_id2')
                        ->whereNull('orders.delivery_rider_id')
                        ->whereNotNull('orders.pickup_rider_id')
                        ->find($id);


                        $wh_id                 =   $this->find_n_assign_wash_house($data->hub_id, $id);

                      
        // 7: moved to hub
        if($data->status_id == 7){
            return redirect()
                    ->back()
                    ->with('permission','Please verify the content first.');
        }
        if($data){
            //insert order status in Order_history table//
            $val                     = new Order_history();
            $val->order_id           = $id;
            $val->created_by         = Auth::user()->id;
            // $val->detail             = $this->create_json_history($id);
            $val->status_id          = 9;
            $val->save();
        }

        //BEGIN :: Update order status in Orders table//
            $input['status_id2']    = 9;
            $input['tags_printed']  = 1;
            $data->update($input);
        //END :: Update order status in Orders table//

        // fetching tot pickups on particular  order's items
        $pickups                    = DB::table('order_has_items')
                                        ->where('order_has_items.order_id', $id)
                                        ->sum('order_has_items.pickup_qty');

        //BEGIN :: fetching order items with services
        $selected_items         = DB::table('order_has_items')
                                    ->where('order_has_items.order_id', $id)
                                    ->select(
                                            'order_has_items.item_id as item_id',
                                            'order_has_items.service_id as service_id',
                                            'order_has_items.pickup_qty as pickup_qty',
                                            )
                                    ->orderBy('order_has_items.service_id','ASC')
                                    ->get()
                                    ->all(); 
        //END :: fetching order items with services

        if($selected_items){
            DB::table("order_has_tags")->where('order_id', '=', $id)->delete();
            foreach($selected_items as $value){
                for($i= $value->pickup_qty; $i>0; $i--){
                    // Storing tag in tables
                    $val                = new Order_has_tag();
                    $val->order_id      = $id;
                    $val->service_id    = $value->service_id;
                    $val->item_id       = $value->item_id;
                    $val->save();
                }
            }
        }

        // assigning wash-house to order
        $wh_id                 =   $this->find_n_assign_wash_house($data->hub_id, $id);
        
        // fetching all items and their rates from "rate_list" table and insert item's rate in "order_has_items" table
                                    $this->add_wh_item_rate($wh_id, $id);
        // getting all addons and their rates from "addon_rate_list" table and insert addons's rate in "order_has_addons" table
                                    $this->add_wh_addon_rate($wh_id, $id);

        $tags                   = DB::table('order_has_tags')
                                    ->leftjoin('items', 'items.id', '=', 'order_has_tags.item_id')
                                    ->leftjoin('services', 'services.id', '=', 'order_has_tags.service_id')
                                    ->where('order_has_tags.order_id', $id)
                                    ->select(
                                                'order_has_tags.id',
                                                'order_has_tags.order_id',
                                                'items.id as item_id',
                                                'items.short_name as item_name',
                                                'services.id as service_id',
                                                'services.name as service_name',
                                                'services.web_image as service_image',
                                                'order_has_tags.id as tag_code'
                                            )
                                    ->orderBy('order_has_tags.id','ASC')
                                    ->get()
                                    ->all(); 

        $tot_tags               = DB::table('order_has_tags')
                                    ->where('order_has_tags.order_id', $id)
                                    ->count();
        if($wh_id != 0){

            $type               = "success";
            $msg                = "Wash-house assigned"; 
            $chk                = Wash_house::where('id', $wh_id)
                                    ->select('name')
                                    ->get()
                                    ->first();
                             
            if(isset($chk->name)){
                $msg            = "Order assigned to ". $chk->name ." successfully!";
            }

        }else{
            $type               = "permission";
            $msg                = "Wash-house not assigned";
        }

        return view('order_verifies.show_tag',
                        compact('data','tot_tags','tags'),
                        [$type=>$msg]
                    );

       
    }

    public function show($id)
    {
        $data                   = Order::orderBy('orders.created_at','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'orders.status_id')
                                    ->leftjoin('users','users.id','=','orders.DW_who')
                                    ->select(
                                                'orders.id',
                                                'customers.id as customer_id',
                                                'customers.name',
                                                'customers.contact_no',
                                                'orders.id as order_id',
                                                'orders.pickup_date',
                                                'orders.waver_delivery',
                                                'customers.permanent_note',
                                                'orders.order_note',
                                                'orders.rider_note',
                                                'orders.delivery_date',
                                                'statuses.name as status_name',
                                                'orders.ref_order_id',
                                                'orders.phase',
                                                'users.name as order_DW_who',
                                                'orders.DW_when',
                                                
                                            )
                                    // ->whereNull('orders.delivery_rider_id')
                                    ->whereNotNull('orders.pickup_rider_id')
                                    ->find($id);
                                    // dd($data);

        if($data){

       
            if($data->ref_order_id!= NULL){
                $id = $data->ref_order_id;
            }

            $selected_services      = DB::table('order_has_services')
                                        ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                        ->where('order_has_services.order_id', $id)
                                        ->select('services.id as service_id',
                                                'services.name as service_name',
                                                'order_has_services.weight as weight',
                                                'order_has_services.qty as service_qty'
                                                )
                                        ->orderBy('order_has_services.order_number','ASC')
                                        ->get()
                                        ->all();   
                                        

            $selected_items         = DB::table('order_has_items')
                                        ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                        ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                        // ->leftjoin('order_has_services', 'order_has_services.order_id', '=', 'order_has_items.order_id')
                                        ->where('order_has_items.order_id', $id)
                                        ->select(
                                                'items.id as item_id',
                                                'items.name as item_name',
                                                'order_has_items.service_id as service_id',
                                                'order_has_items.pickup_qty as pickup_qty',
                                                'order_has_items.note as note',
                                                'order_has_items.item_image as item_image',
                                                'services.name as service_name',
                                                'order_has_items.id as ord_itm_id'
                                                )
                                        ->get()
                                        ->all(); 
                                    

            $selected_addons        = DB::table('order_has_addons')
                                        ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                        ->where('order_has_addons.order_id', $id)
                                        ->select('addons.id as addon_id',
                                                'addons.name as addon_name',
                                                'order_has_addons.item_id as item_id',
                                                'order_has_addons.service_id as service_id',
                                                'order_has_addons.ord_itm_id as ord_itm_id',
                                                )
                                        ->get()
                                        ->all(); 

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
            
            return view('order_verifies.show',
                        compact('data',
                                'histories',
                                'selected_services',
                                'selected_items',
                                'selected_addons'
                            )
                        );
        }else{
            return redirect()->route('order_verifies.index')
                    ->with('permission','Details not found');
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

           
            $service_items = view('order_verifies.ajax-items',compact('items'))->render();
            return response()->json(['data'=>$service_items,'service_name'=>$service_name]);
        }

    }

    public function fetch_addons(Request $request)
    {
        if($request->ajax()){

            $item_id        = $request->item_id;
            $service_id     = $request->service_id;
            // $customer_id    = $request->customer_id;

           
            $addons      = DB::table('service_has_addons')
                                ->leftjoin('addons', 'addons.id', '=', 'service_has_addons.addon_id')
                                ->select('addons.id' ,'addons.name')
                                ->where('service_has_addons.service_id',$service_id)
                                ->where('service_has_addons.item_id',$item_id)
                                ->pluck("name","id")
                                ->all();
                            

            // $addons         = addonsName($addon_ids->item_addon);


                               
            
            $service_addons = view('order_verifies.ajax-addons',compact('addons','service_id', 'item_id'))->render();
            return response()->json(['data'=>$service_addons]);
        }

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
                                            //  'order_has_items.id as ord_itm_id',
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
                    $addons[]      = DB::table('order_has_addons')
                                    ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                        ->where('order_has_addons.order_id', $id)
                                        ->where('order_has_addons.service_id', $service_value->service_id)
                                        ->where('order_has_addons.item_id', $item_value->item_id)
                                        // ->where('order_has_addons.ord_itm_id',$item_value->ord_itm_id)
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

    public function edit($id)
    {
    
        $data                       = Order::orderBy('orders.created_at','DESC')
                                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                        ->select('orders.id',
                                                'customers.id as customer_id',
                                                'customers.name',
                                                'customers.contact_no',
                                                'orders.id as order_id',
                                                'orders.pickup_date',
                                                'customers.permanent_note',
                                                'orders.order_note',
                                                'orders.rider_note',
                                                'orders.delivery_date')
                                        ->whereNull('orders.delivery_rider_id')
                                        ->whereNotNull('orders.pickup_rider_id')
                                        
                                        ->whereIn('orders.status_id2',[7,8,9,10]) 
                                        // ->where('orders.status_id2', 7)
                                        ->find($id);

        if($data){
            $holidays              =  DB::table('holidays')
                                        ->select('holiday_date')
                                        ->get();

            $customer_id            = $data->customer_id;
            $statuses               = DB::table('statuses')
                                        ->where('statuses.id', 8)
                                        // ->whereBetween('statuses.id', [12, 15])
                                        ->pluck('name','id')
                                        ->all();

            $services               = DB::table('customer_has_services')
                                        ->leftjoin('services', 'services.id', '=', 'customer_has_services.service_id')
                                        ->orderBy('customer_has_services.order_number','ASC')
                                        ->select('services.id','services.name','services.rate')
                                        ->where('customer_has_services.status','1')
                                        ->where('customer_has_services.customer_id', $customer_id)
                                        // ->where('services.unit_id','2')
                                        ->pluck('name','id')
                                        ->all();
                                        
        
           
                
            $customer_items            = array();
                                     
            

            $items                     = DB::table('service_has_items')
                                            ->orderBy('service_has_items.item_id')
                                            ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                                            ->select(
                                                    'items.id as item_id',
                                                    'items.name as item_name',
                                                    'service_has_items.service_id as service_id'
                                                    )
                                            ->get()
                                            ->all();

            $addons                    = DB::table('service_has_addons')
                                            ->orderBy('service_has_addons.item_id')
                                            ->leftjoin('addons', 'addons.id', '=', 'service_has_addons.addon_id')
                                            ->select(
                                                    'addons.id as addon_id' ,
                                                    'addons.name as addon_name',
                                                    'service_has_addons.item_id as item_id',
                                                    'service_has_addons.service_id as service_id'
                                                    )
                                            ->get()
                                            ->all();
            
            $selected_services         = DB::table('order_has_services')
                                            ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                            ->where('order_has_services.order_id', $id)
                                            ->select('services.id as service_id',
                                                    'services.name as service_name',
                                                    'order_has_services.weight as service_weight',
                                                    'order_has_services.qty as service_qty')
                                            ->orderBy('order_has_services.order_number','ASC')
                                            ->get()
                                            ->all();        

            $selected_items            = DB::table('order_has_items')
                                            ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                            ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                            ->where('order_has_items.order_id', $id)
                                            ->select(
                                                    'items.id as item_id',
                                                    'items.name as item_name',
                                                    'order_has_items.id as ord_itm_id',
                                                    'order_has_items.note as note',
                                                    'services.name as service_name',
                                                    'order_has_items.id as ord_itm_id',
                                                    'order_has_items.service_id as service_id',
                                                    'order_has_items.pickup_qty as pickup_qty',
                                                    'order_has_items.item_image as item_image',
                                                    )
                                            ->get()
                                            ->all(); 

            $selected_addons           = DB::table('order_has_addons')
                                            ->orderBy('order_has_addons.service_id')
                                            ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                            ->where('order_has_addons.order_id', $id)
                                            ->select(
                                                    'addons.id as addon_id',
                                                    'addons.name as addon_name',
                                                    'order_has_addons.item_id as item_id',
                                                    'order_has_addons.service_id as service_id',
                                                    'order_has_addons.ord_itm_id as ord_itm_id'
                                                    )
                                            // ->groupBy('order_has_addons.ord_itm_id as ord_itm_id')
                                            ->get();
                                            // dd($selected_addons);
                                            
            $adn                    = array();
            $ord_adn                = DB::table('order_has_addons')
                                        ->select(
                                                // 'order_has_addons.item_id as item_id',
                                                    'order_has_addons.ord_itm_id as ord_itm_id'
                                                )
                                        ->groupBy('order_has_addons.ord_itm_id')    
                                        ->where('order_has_addons.order_id', $id)
                                        ->get()->all();
                           
            if(!empty($ord_adn)){
                foreach ($ord_adn as $adn_key => $adn_value) {
                    $rec = '';$inc = 0;
                    if(!empty($selected_addons)){
                        foreach($selected_addons as $key =>$value){
                            if($adn_value->ord_itm_id == $value->ord_itm_id){
                                if($inc ==0){
                                    $rec = (string)$value->addon_id;
                                }else{
                                    $rec = $rec  . ',' . $value->addon_id;
                                }
                                $inc++;
                            }
                            $adn[$adn_value->ord_itm_id] = $rec;
                        }
                    }
                }
            }
            return view('order_verifies.edit',
                    compact(
                            'adn',
                            'data',
                            'items', 
                            'addons',
                            'statuses',
                            'services',
                            'holidays',
                            'selected_items',
                            'selected_services',
                            'selected_addons'
                        )
                    );
        }else{
            return redirect()->route('order_verifies.index')
                    ->with('permission','Order Details already appended');
        }
    }

    // get customer type id
    public function get_cus_type($customer_id){
        $customer   = DB::table('customers')
                        ->where('customers.id', $customer_id)
                        ->select(
                                    'customers.customer_type_id',
                                )
                        ->first();
        return $customer->customer_type_id;
    }

    
    // get all addons
    public function get_addons(){
        $addons   = DB::table('addons')
                        ->select(
                                    'addons.id',
                                    'addons.rate'
                                )
                        ->get();
        return  $addons;
    }

    // get addon rate
    public function get_addon_rate($addons, $addon_id){
        foreach ($addons as $key => $value) {
            if($value->id == $addon_id ){
                return $value->rate;
            }
        }
        return 0;
    }

    // get customer all services
    public function get_cus_services($customer_id){
        $services   = DB::table('customer_has_services')
                        ->where('customer_has_services.customer_id', $customer_id)
                        ->select(
                                    'customer_has_services.service_id',
                                    'customer_has_services.service_rate'
                                )
                        ->get();
        return  $services;
    }

    // get customer service rate
    public function get_service_rate($cus_services, $service_id){
        foreach ($cus_services as $key => $value) {
            if($value->service_id == $service_id ){
                return $value->service_rate;
            }
        }
        return 0;
    }

    // these are special items like "dry and clean" whose rates are stored in customer_has_items
    public function get_cus_spcl_items($customer_id){

        $items      = DB::table('customer_has_items')
                        ->where('customer_has_items.customer_id', $customer_id)
                        ->select(
                                    'customer_has_items.item_id',
                                    'customer_has_items.item_rate',
                                    'customer_has_items.service_id'
                                )
                        ->get();
        return  $items;
    }

    // these are general items like "wash and fold" whose rates are stored in service_has_items
    public function get_cus_gen_items($service_id){

        $items      = DB::table('service_has_items')
                        ->where('service_has_items.service_id', $service_id)
                        ->select(
                                    'service_has_items.item_id',
                                    'service_has_items.item_rate',
                                    'service_has_items.service_id'
                                )
                        ->get();
        return  $items;
    }

    // get customer item rate 
    public function get_item_rate($cus_items, $service_id, $item_id){
        foreach ($cus_items as $key => $value) {
            if(($value->service_id == $service_id ) && ($value->item_id == $item_id)){
                return $value->item_rate;
            }
        }
        return false;
    }

    public function is_email_alert_on($order_id){
        $data           = DB::table('orders')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->select(
                                        'customers.email_alert',
                                    )
                            ->where('orders.id',$order_id)
                            ->first();

        if(isset($data->email_alert)){
            return $data->email_alert;
        }else{
            return 0;
        }
    }

    public function fn_get_addon_amount($order_id){
        $amount                 = 0;
        $all_addons             =  DB::table('order_has_addons')
                                    ->leftjoin('order_has_items', 'order_has_items.id', '=', 'order_has_addons.ord_itm_id')
                                    ->where('order_has_addons.order_id',$order_id)
                                    ->select(
                                                'order_has_addons.id',
                                                'order_has_addons.order_id',
                                                'order_has_items.pickup_qty',
                                                'order_has_addons.cus_addon_rate as rate',
                                            )
                                    ->get();

        foreach ($all_addons as $key => $value) {
            for ($i=0; $i < $value->pickup_qty; $i++) { 
                $amount += $value->rate;
            }
        }
        return $amount;
                           

    }

    public function fn_get_service_amount($order_id){
        $data                   = "";
        $tot                    = 0;
        $all_services           =  DB::table('orders')
                                    ->leftjoin('order_has_services', 'order_has_services.order_id', '=', 'orders.id')
                                    ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                    ->where('order_has_services.order_id',$order_id)
                                    ->select(
                                                'orders.id',
                                                'services.unit_id',
                                                'orders.customer_id',
                                                'order_has_services.qty',
                                                'order_has_services.weight',
                                                'order_has_services.service_id',
                                                'order_has_services.cus_service_rate as service_rate',
                                            )
                                    ->get();

        foreach ($all_services as $key => $value) {
            if($value->unit_id == 1){
                 // If unit is KG:1 then rate will be based on service weight
                $tot            += (($value->weight) * ( $value->service_rate));

            }else if($value->unit_id == 2){
                // If unit is item:2 then rate will be based on item rate which will be different for all items
                $items      =  DB::table('order_has_items')
                                    ->where('order_has_items.order_id',$value->id)
                                    ->where('order_has_items.service_id',$value->service_id)
                                    ->select(
                                                'order_has_items.order_id',
                                                'order_has_items.item_id',
                                                'order_has_items.pickup_qty',
                                                'order_has_items.service_id as service_id',
                                                'order_has_items.cus_item_rate as item_rate',
                                            )
                                    ->get();
                foreach ($items as $item_key => $item_value) {
                    $tot   +=   (($item_value->pickup_qty) * ($item_value->item_rate));
                }
            }else if($value->unit_id == 3){
                // If unit is piece:3 then rate will be based on rate which will be same for all items
                $tot            += (($value->qty) * ( $value->service_rate));
            }
           
        }

        return $tot;
                           

    }

    public function fn_add_vat_charges($amount){
        $vat_amount     = 0;
        $vat            = DB::table('vats')
                            ->select(
                                        'vats.vat'
                                    )
                            ->first();
        if($vat){
            $val        = $vat->vat;
            $vat_amount = ($amount * $val / 100);
        }
        return $vat_amount;
    }

    public function fn_add_delivery_charges($amount){
        $d_amount           = 0;
        $d_charges          = DB::table('delivery_charges')
                                ->select(
                                            'delivery_charges.order_amount',
                                            'delivery_charges.delivery_charges'
                                        )
                                ->first();
        if($d_charges){
            $order_amount   = $d_charges->order_amount; 
            if($amount < $order_amount){
                $d_amount   = $d_charges->delivery_charges;
            }
        }
       return $d_amount;
    }

    public function get_order_history($order_id, $status_id){

        $detail     = Order_history::where('order_id', $order_id)
                            ->where('status_id', $status_id)
                            ->select('detail')
                            ->latest('id')
                            ->first();

        return $detail;
    }

    public function verify_order(Request $request){
   

        $validator = Validator::make($request->all(), 
            [
                'id'                    => 'required',
                'customer_id'           => 'required',
                'pickup_date'           => 'required|date',
                'delivery_date'         => 'required|date|after:pickup_date',
                'weight'                => 'required|array',
                'weight.*'              => 'required|min:0|not_in:0',
                'item_id'               => 'required|array',
                'item_id.*.*'           => 'required|numeric|min:1',

                'pickup_qty'                   => 'required|array',
                'pickup_qty.*.*'               => 'required|numeric|min:1',
            ],
            [
                'weight.*.required'     => 'Please enter weight !',
                'weight.*.min'          => 'Please use value greater than 0 KG !',
                'weight.*.not_in'       => 'weight cannot be 0 KG !',
                'pickup_qty.*.*.required'        => 'Please enter qty !',
                'pickup_qty.*.*.min'             => 'Please use value greater than 0 !',
                
                
            ]
        );

        if (!($validator->passes())) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }
          
        if(!(isset($request['item_id']))){
            return response()->json(['error'=>[0=>"No Item found."]]);
        }
          $order_id  = $request['id'];

        try {
            // Transaction
            $exception = DB::transaction(function()  use ($request) {
        
                // get all addons with rates
                $cus_addons                 = $this->get_addons();

                // get all services with rates
                $cus_services               = $this->get_cus_services($request['customer_id']);

                // customer special items with rates
                $cus_spcl_items             = $this->get_cus_spcl_items($request['customer_id']);
                $id                         = $request['id'];
                $order_id                   = $id;
                $data                       = Order::find($order_id);
                $input                      = $request->all();
                
                $order['delivery_date']     = $request['delivery_date'];
                $order['order_note']        = $request['order_note'];
                $order['status_id2']        = $request['status_id'];
                // 4: Picked Up
                // 8: Content Verified

                // Array Inputs //
                $services                   = $request['service_id'];
                $items                      = $request['item_id'];
                $pickup_qtys                = $request['pickup_qty'];
                $addons                     = $request['item_addons'];
                // $images                     = $request['item_image'];
                $notes                      = $request['item_note'];

                $qtys                       = $request['qty'];
                $weights                    = $request['weight'];
                $addon_ids                  = $request['addon_ids'];

                // delete previous data of this order_id 
                DB::table("order_has_services")->where('order_id', '=', $id)->delete();
                DB::table("order_has_items")->where('order_id', '=', $id)->delete();
                DB::table("order_has_addons")->where('order_id', '=', $id)->delete();


                if($services){
                    foreach($services as $service_key => $service_value){
                        
                        $var                    = new Order_has_service();
                        $var->order_id          = $order_id;
                        $var->service_id        = $service_value;
                        $var->qty               = $qtys[$service_key];
                        $var->weight            = $weights[$service_key];
                        $var->cus_service_rate  = $this->get_service_rate($cus_services, $service_value);
                        $var->save();

                        if($items[$service_key]){

                            foreach($items[$service_value] as $item_key => $item_value){

                                //  item_id     =  item_value;
                                //  service_id  =  service_value;

                                // call customer special items (like: Dye and clean) from "customer_has_items" table
                                $itm_rate            =  $this->get_item_rate($cus_spcl_items, $service_value, $item_value);
                                if($itm_rate ==  false){
                                    // call customer general items from "service_has_items" table
                                    $cus_gen_items   = $this->get_cus_gen_items($service_value);
                                    $itm_rate        = $this->get_item_rate($cus_gen_items, $service_value, $item_value);
                                }
                            
                                $item                = new Order_has_item();
                                $item->order_id      = $order_id;
                                $item->service_id    = $service_value;
                                $item->item_id       = $item_value;
                                $item->pickup_qty    = $pickup_qtys[$service_key][$item_key];
                                $item->note          = $notes[$service_key][$item_key];
                                $item->cus_item_rate = $itm_rate;
                                $item->save();

                                if(!(empty($addon_ids[$service_key][$item_key]))){
                                    $addons          = explode(',', $addon_ids[$service_key][$item_key]);
                                    if(!(empty($addons))){
                                        foreach ($addons as $addon_key => $addon_value) {
                                            $addon                  = new Order_has_addon();
                                            $addon->order_id        = $order_id;
                                            $addon->item_id         = $item_value;
                                            $addon->addon_id        = $addon_value;
                                            $addon->service_id      = $service_value;
                                            $addon->ord_itm_id      = $item->id;  // get last inserted id of "order_has_items"
                                            $addon->cus_addon_rate  =  $this->get_addon_rate($cus_addons, $addon_value);
                                            $addon->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // get and sum all service / items rates
                $service_tot              = $this->fn_get_service_amount($id);

                // get and sum all addon rates
                $addon_tot                = $this->fn_get_addon_amount($id);
            
                // sum service and addon rates
                $amount_tot               = ( $service_tot + $addon_tot);
                $wavier                   = $data->waver_delivery;
          
                $customer                 = $data->customer_id;
                $id_order                 = $order_id;


                $today                    = Carbon::today();


                $orderIds                 = Order::where('customer_id', $customer)->whereDate('created_at', $today)->pluck('id');

                $totalAmount              = 0;


                foreach ($orderIds as $id)  
                {
  
                  if ($id == $id_order) 
                  {
                    continue;
                  }


                 $service_tot              = $this->fn_get_service_amount($id);


                 $addon_tot                = $this->fn_get_addon_amount($id);


                 $amount_tots              = $service_tot + $addon_tot;

                 $totalAmount              += $amount_tots;
                }

                $grandTotal                = $totalAmount + $amount_tot ;
              

                    
                if($wavier === 1 || $grandTotal >700){
                    $d_amount             = 0;
                }
                else{
                  $d_amount                 = $this->fn_add_delivery_charges($amount_tot);  
                }
         
                $temp_amount              = ($d_amount + $amount_tot); //  sum of items and addons and delivery charges
                $vat_amount               = $this->fn_add_vat_charges($temp_amount);

                $order['delivery_charges'] = $d_amount;
                $order['vat_charges']      = $vat_amount;
                $order['tags_printed']     = null;

                $upd                       = $data->update($order);
            
                // BEGIN::  Storing the Order History
                    $val                     = new Order_history();
                    $val->order_id           = $order_id;
                    $val->created_by         = Auth::user()->id;
                    $val->detail             = $this->create_json_history($order_id);
                    $val->status_id          = $request['status_id'];
                    $val->save();
                // END::    Storing the Order History
            
                // $wallet_id                  = "order:" .$order_id;

                // $wallet                     = DB::table('customer_has_wallets')
                //                                 ->select('customer_has_wallets.id')
                //                                 ->where('customer_has_wallets.detail', $wallet_id)
                //                                 ->first();
                // if(isset($wallet->id)){
                
                //     $data                   = DB::table('customer_has_wallets')
                //                                 ->where('customer_has_wallets.id', ($wallet->id))
                //                                 ->update(['customer_has_wallets.out_amount'=> ($temp_amount + $vat_amount)]);

                // }else{

                //     // BEGIN::store wallet amount as debit/ out_amount
                //         $chw                        = new Customer_has_wallet();
                //         $chw->customer_id           = $data->customer_id;
                //         $chw->out_amount            = ($temp_amount + $vat_amount); // sum of all service, addon, items, vat, and delivery rates
                //         $chw->order_id              = $order_id;
                //         $chw->detail                = ("order:" .$order_id);
                //         $chw->save();
                //     // END::store wallet amount as debit/ out_amount
                // }
            });
         
            if(is_null($exception)) {
                $email_alert                    = $this->is_email_alert_on($order_id);
                
             


                // picked up details
                $pk_detail                      = $this->get_order_history($order_id, 4); // 4: picked up

                // verified details
                $vf_detail                      = $this->get_order_history($order_id, 8); // 8: content verified

             
                $sent_mail                      = true ;
               
                if( (isset($pk_detail->detail))  && (isset($vf_detail->detail)) ){
              
                    if( ($pk_detail->detail) == ($vf_detail->detail)  ){
                        $sent_mail = false;
                    }
    
                }
                
                
                if( ((isset($email_alert)) && (($email_alert) == 1 ) ) && ($sent_mail == true)){
                    $mail    = app('App\Http\Controllers\MailController')->send_invoice($order_id);
                    if($mail == 1){
                        $msg = "Order verified and email sent successfully.";
                    }else{
                        $msg = "Order verified but email not sent successfully.";
                    }
                }else{
                    $msg = "Order verified successfully.";
                }

                if(isset($order_id)){
                    (new NotificationController)->order_modified($order_id);
                }
                return response()->json(['success'=>$msg]);
            } else {
                throw new Exception;
            }
          
        }
        
        catch(\Exception $e) {
            app('App\Http\Controllers\MailController')->send_exception($e);
            return response()->json(['error'=>[0=>"Something went wrong."]]);
        }
    }

    // this function is replaced with verify_order due to ajax 
   /* public function update(Request $request, $id)
    {
        if($request['item_id']){
            $this->validate($request,
                [
                    'customer_id'           => 'required',
                    'pickup_date'           => 'required|date',
                    'delivery_date'         => 'required|date|after:pickup_date',
                    'weight'                => 'required|array',
                    'weight.*'              => 'required|min:0|not_in:0',
                    'item_id'               => 'required|array',
                    'item_id.*.*'           => 'required|numeric|min:1',

                    'pickup_qty'                   => 'required|array',
                    'pickup_qty.*.*'               => 'required|numeric|min:1',
                ],
                [
                    'weight.*.required'     => 'Please enter weight !',
                    'weight.*.min'          => 'Please use value greater than 0 KG !',
                    'weight.*.not_in'       => 'weight cannot be 0 KG !',
                    'pickup_qty.*.*.required'        => 'Please enter qty !',
                    'pickup_qty.*.*.min'             => 'Please use value greater than 0 !',
                    
                    
                ]
            );

            // get all addons with rates
            $cus_addons                 = $this->get_addons();

            // get all services with rates
            $cus_services               = $this->get_cus_services($request['customer_id']);
           
            // customer special items with rates
            $cus_spcl_items             = $this->get_cus_spcl_items($request['customer_id']);
         

            // dd($cus_services);
            $order_id                   = $id;
            $data                       = Order::find($order_id);
            $input                      = $request->all();
            
            $order['delivery_date']     = $request['delivery_date'];
            $order['order_note']        = $request['order_note'];
            $order['status_id2']        = $request['status_id'];
            // 4: Picked Up
            // 8: Content Verified

        
            // $upd                        = $data->update($order);

            // Array Inputs //
            $services                   = $request['service_id'];
            $items                      = $request['item_id'];
            $pickup_qtys                = $request['pickup_qty'];
            $addons                     = $request['item_addons'];
            // $images                     = $request['item_image'];
            $notes                      = $request['item_note'];

            $qtys                       = $request['qty'];
            $weights                    = $request['weight'];
            $addon_ids                  = $request['addon_ids'];

            // delete previous data of this order_id 
            DB::table("order_has_services")->where('order_id', '=', $id)->delete();
            DB::table("order_has_items")->where('order_id', '=', $id)->delete();
            DB::table("order_has_addons")->where('order_id', '=', $id)->delete();


            if($services){
                foreach($services as $service_key => $service_value){
                    
                    $var                    = new Order_has_service();
                    $var->order_id          = $order_id;
                    $var->service_id        = $service_value;
                    $var->qty               = $qtys[$service_key];
                    $var->weight            = $weights[$service_key];
                    $var->cus_service_rate  = $this->get_service_rate($cus_services, $service_value);
                    $var->save();

                    if($items[$service_key]){

                        foreach($items[$service_value] as $item_key => $item_value){

                            //  item_id     =  item_value;
                            //  service_id  =  service_value;

                            // call customer special items (like: Dye and clean) from "customer_has_items" table
                            $itm_rate            =  $this->get_item_rate($cus_spcl_items, $service_value, $item_value);
                            if($itm_rate ==  false){
                                // call customer general items from "service_has_items" table
                                $cus_gen_items   = $this->get_cus_gen_items($service_value);
                                $itm_rate        = $this->get_item_rate($cus_gen_items, $service_value, $item_value);
                            }
                         
                            $item                = new Order_has_item();
                            $item->order_id      = $order_id;
                            $item->service_id    = $service_value;
                            $item->item_id       = $item_value;
                            $item->pickup_qty    = $pickup_qtys[$service_key][$item_key];
                            $item->note          = $notes[$service_key][$item_key];
                            $item->cus_item_rate = $itm_rate;
                            $item->save();

                            if(!(empty($addon_ids[$service_key][$item_key]))){
                                $addons          = explode(',', $addon_ids[$service_key][$item_key]);
                                if(!(empty($addons))){
                                    foreach ($addons as $addon_key => $addon_value) {

                                        $addon                  = new Order_has_addon();
                                        $addon->order_id        = $order_id;
                                        $addon->item_id         = $item_value;
                                        $addon->addon_id        = $addon_value;
                                        $addon->service_id      = $service_value;
                                        $addon->ord_itm_id      = $item->id;  // get last inserted id of "order_has_items"
                                        $addon->cus_addon_rate  =  $this->get_addon_rate($cus_addons, $addon_value);
                                        $addon->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // get and sum all service / items rates
            $service_tot              = $this->fn_get_service_amount($id);

            // get and sum all addon rates
            $addon_tot                = $this->fn_get_addon_amount($id);

            // sum service and addon rates
            $amount_tot               = ( $service_tot + $addon_tot);

            // 
            $d_amount                 = $this->fn_add_delivery_charges($amount_tot);
            $temp_amount              = ($d_amount + $amount_tot); //  sum of items and addons and delivery charges
            $vat_amount               = $this->fn_add_vat_charges($temp_amount);

            $order['delivery_charges'] = $d_amount;
            $order['vat_charges']      = $vat_amount;
            $order['tags_printed']      = null;

            $upd                       = $data->update($order);

            // BEGIN::  Storing the Order History
            $val                     = new Order_history();
            $val->order_id           = $order_id;
            $val->created_by         = Auth::user()->id;
            $val->detail             = $this->create_json_history($order_id);
            $val->status_id          = $request['status_id'];
            $val->save();
            // END::    Storing the Order History

            $wallet_id                  = "order:" .$order_id;

            $wallet                     = DB::table('customer_has_wallets')
                                            ->select('customer_has_wallets.id')
                                            ->where('customer_has_wallets.detail', $wallet_id)
                                            ->first();
            if(isset($wallet->id)){
                $data               = DB::table('customer_has_wallets')
                                        ->where('customer_has_wallets.id', ($wallet->id))
                                        ->update(['customer_has_wallets.out_amount'=> ($temp_amount + $vat_amount)]);

            }else{
                // BEGIN::store wallet amount as debit/ out_amount
                    $chw                        = new Customer_has_wallet();
                    $chw->customer_id           = $data->customer_id;
                    $chw->out_amount            = ($temp_amount + $vat_amount); // sum of all service, addon, items, vat, and delivery rates
                    $chw->order_id              = $order_id;
                    $chw->detail                = ("order:" .$order_id);
                    $chw->save();
                // END::store wallet amount as debit/ out_amount
            }


            $email_alert                    = $this->is_email_alert_on($order_id);
            if($email_alert == 1){
                $mail    = app('App\Http\Controllers\MailController')->send_invoice($order_id);
                if($mail == 1){
                    $msg = "Order verified and email sent successfully.";
                }else{
                    $msg = "Order verified but email not sent successfully.";
                }
            }else{
                $msg = "Order verified successfully.";
            }
            if(isset($order_id)){
                (new NotificationController)->order_modified($order_id);
            }
            return redirect()
                    ->route('order_verifies.index')
                    ->with('success', $msg);
        }else{
            return redirect()
                    ->back()
                    ->with('permission','No Item found.');
        }
    }*/

    public function destroy(Request $request)
    {
     
    }

      

    
}
