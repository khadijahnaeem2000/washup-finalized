<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use Validator;
use DataTables;
use App\Models\Order;
use App\Models\Wash_house;
use App\Models\Order_history;
use App\Models\Distribution_hub;
use App\Models\Wash_house_has_order;

class Wash_house_has_orderController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:Wash_house_order-list', ['only' => ['index','show']]);
         $this->middleware('permission:Wash_house_order-create', ['only' => ['create','store']]);
         $this->middleware('permission:Wash_house_order-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:Wash_house_order-delete', ['only' => ['destroy']]);
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
           
        return view('wash_house_has_orders.index',compact('hubs'));
    }

    public function fetch_wash_house(Request $request)
    {
        if($request->ajax()){
            $wash_house         = DB::table('wash_house_has_hubs')
                                    ->leftjoin('wash_houses', 'wash_houses.id', '=', 'wash_house_has_hubs.wash_house_id')
                                    ->where('wash_house_has_hubs.hub_id',$request->hub_id)
                                    ->select('wash_houses.id','wash_houses.name','wash_houses.capacity')
                                    ->get()
                                    ->all();
          
            if($wash_house){   
                $rec            = array();
                foreach ($wash_house as $key => $value) {
                    $pickup_qty       = $this->calc_wash_house_pickups($value->id);
                    $rec[$value->id]  = $value->name . " (".(($value->capacity)-($pickup_qty)) . " Pieces)";
                }

                $data = view('wash_house_has_orders.ajax-wash_houses',compact('rec'))->render();
                return response()->json(['data'=>$data]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
            
        }

    }

    public function calc_order_pickups($order_id){
        // this function sums(addition) that how much order's pick_qty
        $data       = DB::table('order_has_items')
                        // ->leftjoin('orders', 'orders.id', '=', 'order_has_items.order_id')
                        ->select(DB::raw('SUM(order_has_items.pickup_qty) as pickup_qty'))
                        ->where('order_has_items.order_id', $order_id)
                        // ->where('orders.status_id2','>', 10)
                        ->first(); 

        if($data){
            return $data->pickup_qty;
        }else{
            return 0;
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
                        ->where('orders.status_id2', 10)
                        // ->whereDate('orders.delivery_date', '>', $date)
                        // ->where('orders.status_id2','>', 10)
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

    public function list($hub_id)
    {
                  DB::statement(DB::raw('set @srno=0'));
        $date   = date("Y-m-d");
        $data   = DB::table('orders')
                    // ->orderBy('orders.status_id2')
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
                                'statuses.name as status_name',
                                'wash_houses.name as wash_house_name',
                                // DB::raw('(CASE 
                                //     WHEN orders.status_id2 IS NULL THEN "Moved to hub" 
                                //     ELSE statuses.name 
                                //     END) AS status_name'
                                // ),
                                DB::raw('(CASE 
                                    WHEN wash_house_has_orders.summary_printed = "0" THEN "No"
                                    WHEN wash_house_has_orders.summary_printed = "1" THEN "Yes" 
                                    END) AS summary_printed'
                                )
                            )
                    // 15: Moved to wash-house
                    // 10: content verified
                    ->where('orders.hub_id',$hub_id)
                    ->whereNotNull('orders.pickup_rider_id')
                    ->whereIn('orders.status_id2', [7,8,9,10])
                    ->whereDate('orders.delivery_date', '>=', $date)
                    ->get();
        return 
            DataTables::of($data)
                ->addColumn('checkbox','<div class="checkbox-inline"> <label class="checkbox checkbox-success"><input type="checkbox" name="order_id[{{$id}}]" /><span></span> </label></div>')
                ->rawColumns(['checkbox',''])
                ->make(true);
    }

    public function create()
    {
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



   // Assign wash-houses to selected orders//
    public function store(Request $request)
    {
        $msg        = "";
        $state      = "success";
        $ids        = array();
        $asgnd_ids = array();
       
        // Assign wash-houses to selected orders//
        $validator  = Validator::make($request->all(), 
            [
                'wash_house_id'         => 'required|numeric|min:1',
                'order_id'              => 'required'
            ],
            [
                'wash_house_id.required'=> 'Please select wash house id(s)!',
                'order_id.required'     => 'Please select order(s)!',
            ]
        );
        if ($validator->passes()) {
             // checking is user super admin or not //
             $order_ids                 = $request['order_id'];
             $check                     = 0;
            if($request['special'] == 1){
                // Checking :: is order move to hub; if yes, do not assign to any washhouse
                foreach($order_ids as $key => $value){
                       $ordr            = Order::where('id',$key)
                                            ->where('status_id2',7)
                                            ->get()
                                            ->first();
                    if($ordr){
                        $check          = 2;
                        break;
                    }
                }
                
                
            }else{
                // checking whether wash house order summary printed or not ///
                foreach($order_ids as $key => $value){
                     // Checking :: is order move to hub; if yes, do not assign to any washhouse
                   $ordr                = Order::where('id',$key)
                                            ->where('status_id2',7)
                                            ->get()
                                            ->first();
                                                
                   // Checking :: is order's summary printed; if yes, do not assign to any washhouse
                    $data               = Wash_house_has_order::select('order_id')
                                            ->where('order_id',$key)
                                            ->where('summary_printed',1)
                                            ->get()
                                            ->first();
                                                
                
                    if($data){
                        $check = 1;
                        break;
                    }else if($ordr){
                        $check = 2;
                        break;
                    }
                }
            }
        
            if($check == 1){
                return response()->json(['error'=>"Cant change the wash-house, summary has been printed!"]);
            }else if($check == 2){
                return response()->json(['error'=>"Cant assign the wash-house, content are verified yet!"]);
            }else{
                $tot_wsh_pickups            = 0;
                $tot_odr_pickups            = 0;
                $wsh_capacity               = 0;
                if($order_ids){
                    foreach($order_ids as $key => $value){
                        $order_id           =  $key;
                        $wash_house_id      =  $request['wash_house_id'];
                        $tot_odr_pickups    += $this->calc_order_pickups(  $order_id );
                    }
                    $tot_wsh_pickups        += $this->calc_wash_house_pickups(  $wash_house_id );
                    $wsh_capacity            = $this->find_capacity(  $wash_house_id );
                }

                
                $free_capacity  = $wsh_capacity - $tot_wsh_pickups ;
                // echo "Washhouse Capacity: ".$wsh_capacity ."<br>";
                // echo "Washhouse Pickups: ".$tot_wsh_pickups ."<br>";
                // echo "Order Pickup: ".$tot_odr_pickups ."<br>";
                // echo "free Capacity: ".$free_capacity ."<br>";

                if( $tot_odr_pickups <= $free_capacity){
                    // echo "yes";
                    foreach($order_ids as $key => $value){
                        $chk1                    = $this->has_whs_servs_n_adns($request['wash_house_id'], $key);
                        // echo "before <br>";

                        if($chk1 == -1){
                            // echo "with in if <br>";
                            array_push($ids,$key);
                            continue;
                        }else{
                            array_push($asgnd_ids,$key);
                        }
                    
                        $input['order_id']      = $key;
                        // $input['status_id']     = 15;
                        $input['status_id2']    = 10;
                        $input['wash_house_id'] = $request['wash_house_id'];

                        $data                   = Wash_house_has_order::where('order_id', $key)
                                                    ->get()
                                                    ->first();

                        if(!empty($data)){
                            $data1              = Wash_house_has_order::where('order_id', $key)
                                                    ->where('wash_house_id',$request['wash_house_id'])
                                                    ->get()
                                                    ->first();
                            if(!empty($data1)){
                                                  $data1->update($input);
                                $chk            = Wash_house::where('id', $request['wash_house_id'])
                                                    ->select('name')
                                                    ->get()
                                                    ->first();
                                $state          = "error";
                                $msg            = "Order already assigned to ". $chk->name ." Washhouse!";

                            }else{
                                $chk            = Wash_house::where('id', $request['wash_house_id'])
                                                    ->select('name')
                                                    ->get()
                                                    ->first();
                                                  $data->update($input);
                                $state          = "success";
                                $msg            = "Order assigned to ". $chk->name ." successfully!";
                                // fetching all items and their rates from "rate_list" table and insert item's rate in "order_has_items" table
                                $this->add_wh_item_rate($request['wash_house_id'], $key);
                                // getting all addons and their rates from "addon_rate_list" table and insert addons's rate in "order_has_addons" table
                                $this->add_wh_addon_rate($request['wash_house_id'], $key);

                            }
                            
                        }else{
                            // insert a row having order_id and wash_house_id when order is not available in wash_has_order table
                            $check  = Wash_house_has_order::where('order_id',$key)->first();
                            if(isset($check)){
                                DB::table("wash_house_has_orders")->where('order_id', '=', $key)->delete();
                            }
                            $var                = new Wash_house_has_order();
                            $var->wash_house_id = $request['wash_house_id'];
                            $var->order_id      = $key;
                            $var->save();

                            
                            // fetching all items and their rates from "rate_list" table and insert item's rate in "order_has_items" table
                            $this->add_wh_item_rate($request['wash_house_id'], $key);
                            // getting all addons and their rates from "addon_rate_list" table and insert addons's rate in "order_has_addons" table
                            $this->add_wh_addon_rate($request['wash_house_id'], $key);

                            $chk                = Wash_house::where('id', $request['wash_house_id'])
                                                    ->select('name')
                                                    ->get()
                                                    ->first();
                        
                            //Update order status in Orders table//
                            $order              = Order::find($key);
                                                  $order->update($input);

                            //Insert order status in Order_history table//
                            $val                = new Order_history();
                            $val->order_id      = $key;
                            $val->created_by    = Auth::user()->id;
                            $val->status_id     = 10;
                            $val->save();
                            $state              = "success";
                            $msg                = "Order assigned to ". $chk->name ." successfully!";
                        }
                    }
                    $my_ids = implode(", ",$ids);
                    if((!empty($ids)) && (count($asgnd_ids) > 0)){
                        return response()->json([$state=>$msg. " but order(s) [".$my_ids."] are not assigned, beacause service or addons are not avaliable on selected wash-house."]);
                    }if((!empty($ids)) && (count($asgnd_ids) == 0)){
                        return response()->json(["error"=>"Order(s) [".$my_ids."] are not assigned, beacause service or addons are not avaliable on selected wash-house."]);
                    }else{
                        return response()->json([$state=>$msg]);
                    }
                }else{
                    return response()->json(['error'=>"Wash house has not enough space to take $tot_odr_pickups more pieces"]);
                }
            }
            // return response()->json(['success'=>'Order of added successfully.']);
        }else{
            // return response()->json(['error'=>[0=>""]]);
            return response()->json(['error'=>$validator->errors()->all()]);
        }
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
