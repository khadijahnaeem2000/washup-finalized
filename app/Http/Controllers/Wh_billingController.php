<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use Validator;
use DataTables;
use App\Models\Order;
use App\Models\Status;
use App\Models\Service;
use App\Models\Order_has_item;
use App\Models\Order_has_addon;
use App\Models\Distribution_hub;
use App\Models\Order_has_service;
use App\Models\Wash_house;

class Wh_billingController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:wh_billing-list', ['only' => ['index','show']]);
         $this->middleware('permission:wh_billing-create', ['only' => ['create','store']]);
         $this->middleware('permission:wh_billing-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:wh_billing-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        // $request['wash_house_id'] = 2;
        // $request['pickup_date'] = '2021-03-19';


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
                $wash_houses    = Wash_house::pluck('name','id')->all();
            }else{
                $wash_houses    = DB::table('wash_houses')
                                    ->leftjoin('wash_house_has_users', 'wash_house_has_users.wash_house_id', '=', 'wash_houses.id')
                                    ->where('wash_house_has_users.user_id', $user->user_id)
                                    ->select(
                                                'wash_houses.id as id',
                                                'wash_houses.name as name'
                                            )
                                    ->pluck('wash_houses.name','wash_houses.id')
                                    ->all();
            }
        return view('wh_billings.index',
                        compact('wash_houses')
                    );
    }

    public function fn_has_order_addons($order_id){
        $data     = DB::table('order_has_addons')
                        ->select(
                                'order_has_addons.id'
                                )
                        ->where('order_has_addons.order_id',$order_id)
                        ->get();
                        
                        // print_r($data);
        if(!empty($data)){
            return 1;       //if order has addons
        }else{
            return 0;       //if order has no addons
        }
    }

    public function list(Request $request)
    {

        // $request['wash_house_id'] = 2;
        // $request['pickup_date'] = '2021-03-19';
        
        $validator = Validator::make($request->all(), [
            'wash_house_id'       => 'required|min:1|numeric',
                'pickup_date'         => 'required|date',
                
            ],
            [
                'wash_house_id.required'=> 'Please select Washhouse',
                'wash_house_id.min'     => 'Please select valid Washhouse',
                'pickup_date.required'  => 'Please select pickup date',
            ]
        );
        if($validator->passes()) {
            $orders                 = DB::table('wash_house_has_orders')
                                        ->orderBy('order_has_items.service_id')
                                        ->leftjoin('order_has_items', 'order_has_items.order_id', '=', 'wash_house_has_orders.order_id')
                                        ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                        ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                        ->select(
                                                'wash_house_has_orders.order_id',
                                                'order_has_items.order_id',
                                                'order_has_items.service_id',
                                                'order_has_items.item_id',
                                                'order_has_items.pickup_qty',
                                                
                                                'order_has_items.wh_item_rate as rate',
                                                'items.name as item_name',
                                                'services.name as service_name',
                                                // 'order_has_services.service_id'
                                                )
                                        ->where('wash_house_has_orders.wash_house_id',$request['wash_house_id'])
                                        ->whereDate('wash_house_has_orders.created_at',$request['pickup_date'])
                                        ->get();
            if(!($orders->isEmpty())){
                $record                 = array(); 
                foreach($orders as $key => $value){
                    // $rate_list          = DB::table('rate_lists')
                    //                         ->select(
                    //                                     'rate_lists.rate',
                    //                                 )
                    //                         ->where('rate_lists.service_id',$value->service_id)
                    //                         ->where('rate_lists.item_id',$value->item_id)
                    //                         ->where('rate_lists.wash_house_id',$request['wash_house_id'])
                    //                         ->first();

                    // if($rate_list){
                    //     $rate           = $rate_list->rate;
                    // }else{
                    //     $rate           = 0;
                    // }                  
                    $record[$value->service_id][] =  array(
                        'item_id'       => $value->item_id,
                        'service_id'    => $value->service_id,
                        'pickup_qty'    => $value->pickup_qty,
                        'item_name'     => $value->item_name,
                        'service_name'  => $value->service_name,
                        'rate'          => $value->rate,
                    ); 
                }
                
                $data                   = array();
                foreach ($record as $k => $rec) {
                    $data[$k]           = array();
                    foreach ($rec as $key => $value) {
                        $item_id        = $value['item_id'];
                        $service_id     = $value['service_id'];
                        
                        if(!array_key_exists($item_id, $data[$service_id]))
                        {
                            $data[$service_id][$item_id] = array(
                                'item_id'                   => $value['item_id'],
                                'service_id'                => $value['service_id'],
                                'pickup_qty'                => $value['pickup_qty'],
                                'item_name'                 => $value['item_name'],
                                'service_name'              => $value['service_name'],
                                'rate'                      => $value['rate'],
                            );
                        }else
                        {
                            $last_qty   = $data[$service_id][$item_id]['pickup_qty'];
                            $new_qty    = $value['pickup_qty'];
                            $data[$service_id][$item_id]['pickup_qty'] = ($last_qty + $new_qty);
                        }
                    }
                }

                        
                $adn_orders             = DB::table('wash_house_has_orders')
                                            ->leftjoin('order_has_addons', 'order_has_addons.order_id', '=', 'wash_house_has_orders.order_id')
                                            ->leftjoin('order_has_items', 'order_has_items.id', '=', 'order_has_addons.ord_itm_id')
                                            ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                            ->select(
                                                        'order_has_addons.addon_id', 
                                                        'addons.name as addon_name',
                                                        'order_has_addons.wh_addon_rate', 
                                                        // 'order_has_addons.wh_addon_rate as rate',
                                                        'order_has_addons.ord_itm_id', 
                                                        'order_has_items.pickup_qty as qty', 
                                                        DB::raw('(order_has_addons.wh_addon_rate * order_has_items.pickup_qty ) as rate')
                                                    )
                                    
                                            ->whereNotNull('order_has_addons.addon_id')
                                            ->whereDate('wash_house_has_orders.created_at',$request['pickup_date'])
                                            ->where('wash_house_has_orders.wash_house_id',$request['wash_house_id'])
                                            ->get();
                $adn_record                 = array(); 
                if(!(empty($adn_orders))){
                    foreach($adn_orders as $key => $value){
                        if(isset($adn_record[$value->addon_name]['qty'])){
                            $qty    = $adn_record[$value->addon_name]['qty'] + ($value->qty);
                            $rate   = $adn_record[$value->addon_name]['rate'] + ($value->rate);
                        }else{
                            $qty    = ($value->qty);
                            $rate   =  ($value->rate);
                        }

                        $adn_record[$value->addon_name] =  array(
                            'addon_id'      => $value->addon_id,
                            'qty'           => $qty,
                            'addon_name'    => $value->addon_name,
                            'rate'          => $rate,
                        ); 

                    }
                }

                if($data){
                    $details    = view('wh_billings.report',
                                        compact('data','adn_record'))
                                    ->render();
                    return response()->json(['data'=>$orders,'details'=>$details]);
                }else{
                    return response()->json(['error'=>[0=>"No record found!"]]);
                }
            }else{
                return response()->json(['error'=>[0=>"No record found!"]]);
            }
        }else{
            return response()->json(['error'=>$validator->errors()->all()]);
            
        }
        
    }

    public function create()
    {}

    public function store(Request $request)
    {}
   
    public function show($id)
    {}


    public function edit($id)
    {}

    public function update(Request $request, $id)
    {}

    public function destroy(Request $request)
    {}

      

    
}
