<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use DataTables;
use App\Models\Order;
use App\Models\Status;
use App\Models\Service;
use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Models\Order_history;
use App\Models\Order_has_item;
use App\Models\Order_has_addon;
use App\Models\Complaint_nature;
use App\Models\Order_has_service;
use App\Http\Controllers\Controller;

class Order_detailController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:order_detail-list', ['only' => ['index','show']]);
         $this->middleware('permission:order_detail-create', ['only' => ['create','store']]);
         $this->middleware('permission:order_detail-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:order_detail-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        
        return view('order_details.index');
    }

    public function list()
    {
                      DB::statement(DB::raw('set @srno=0'));
        $date       = date("Y-m-d");
        $data       = Order::orderBy('orders.created_at','DESC')
                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                        ->leftjoin('statuses', 'statuses.id', '=', 'orders.status_id')
                        ->select('orders.id',
                                'customers.name',
                                'customers.contact_no',
                                'orders.id as order_id',
                                'orders.pickup_date',
                                'customers.permanent_note',
                                'orders.order_note',
                                'orders.delivery_date',
                                'statuses.name as status_name',
                                DB::raw('(CASE 
                                WHEN isNULL(orders.ref_order_id) THEN "Regular" 
                                ELSE "HFQ"
                                END) AS order_type'),
                                DB::raw('@srno  := @srno  + 1 AS srno')
                                )
                        ->whereNotNull('orders.pickup_rider_id')
                        ->whereIn('orders.status_id', [1,2,3,4,5,12])
                        ->whereIn('orders.status_id2', [6]) //6: rider assigned
                        // ->whereNotNull('orders.status_id2')
                        // ->whereDate('orders.pickup_date', '=', '2021-06-08')
                        ->get();
                
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="order_details/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="order_details/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a  href="/order_details/create/'.$data->id.'" class="btn btn-primary  btn-sm">
                            <i class="la la-plus"></i>
                        </a>
                      
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);
          
    }

    public function fn_move_to_hub(){
        $data       = Order::where('pickup_rider_id', 1)
                            ->where('status_id', 4)
                            ->update(
                                    ['status_id2' => '7']
                                );
        if($data){
            $status = "success";
            $msg    = "All order Moved to Hub.";
        }else{
            $status = "permission";
            $msg    = "No order found!";
        }
        return redirect()
                ->route('order_details.index')
                ->with($status,$msg);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function fetch_history($history_id){
        $detail                 = null;
        $data                   = DB::table('order_histories')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'order_histories.status_id')
                                    ->leftjoin('users', 'users.id', '=', 'order_histories.created_by')
                                    ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                    ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                                    ->select(   
                                                'order_histories.*',
                                                'statuses.name as status_name',
                                                'users.name as user_name',
                                                'roles.name as role_name'
                                            )
                                    ->where('order_histories.id',$history_id)
                                    ->first();
        if($data){
            $detail             = json_decode($data->detail);
        }
        // dd($detail);
        return view('order_details.show_history',
                    compact('data',
                            'detail'
                        )
                    );
    }
    
    public function show($id)
    {
        $data                   = Order::orderBy('orders.created_at','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'orders.status_id')
                                    ->select('orders.id',
                                            'customers.id as customer_id',
                                            'customers.name',
                                            'customers.contact_no',
                                            'orders.id as order_id',
                                            'orders.pickup_date',
                                            'customers.permanent_note',
                                            'orders.order_note',
                                            'orders.delivery_date',
                                            'statuses.name as status_name',
                                            'orders.ref_order_id')
                                    // ->whereNull('orders.delivery_rider_id')
                                    ->whereNotNull('orders.pickup_rider_id')
                                    ->find($id);

        if($data){
            if($data->ref_order_id!=null){
                $id = $data->ref_order_id;
            }

            $selected_services      = DB::table('order_has_services')
                                        ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                        ->where('order_has_services.order_id', $id)
                                        ->select('services.id as service_id',
                                                'services.name as service_name',
                                                'order_has_services.weight as weight'
                                                )
                                        ->orderBy('order_has_services.order_number')
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

            // $histories              = DB::table('order_histories')
            //                                     ->leftjoin('statuses', 'statuses.id', '=', 'order_histories.status_id')
            //                                     ->leftjoin('users', 'users.id', '=', 'order_histories.created_by')
            //                                     ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            //                                     ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            //                                     ->where('order_histories.order_id', $id)
            //                                     ->select(
            //                                             'order_histories.id as history_id',
            //                                             'statuses.id as status_id',
            //                                             'statuses.name as status_name',
            //                                             'users.name as user_name',
            //                                             'roles.name as role_name',
            //                                             'order_histories.detail',
            //                                             'order_histories.created_at as created_at'
            //                                             )
            //                                     ->get()
            //                                     ->all();
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
            
            return view('order_details.show',
                        compact('data',
                                'histories',
                                'selected_services',
                                'selected_items',
                                'selected_addons'
                            )
                        );
        }else{
            return redirect()->route('order_details.index')
                    ->with('permission','Order Details not found');
        }
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
                $items      = DB::table('customer_has_items')
                                ->leftjoin('items', 'items.id', '=', 'customer_has_items.item_id')
                                ->select('items.id','items.name')
                                ->where('customer_has_items.service_id',$service_id)
                                ->where('customer_has_items.customer_id',$customer_id)
                                ->pluck("name","id")
                                ->all();
            }else{
                $items      = DB::table('service_has_items')
                                ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                                ->select('items.id','items.name')
                                ->where('service_has_items.service_id',$service_id)
                                // ->where('customer_has_items.customer_id',$customer_id)
                                ->pluck("name","id")
                                ->all();
            }

           
            $service_items = view('order_details.ajax-items',compact('items'))->render();
            return response()->json(['data'=>$service_items,'service_name'=>$service_name]);
        }

    }

    public function fetch_addons(Request $request)
    {
        if($request->ajax()){

            $item_id        = $request->item_id;
            $service_id     = $request->service_id;
            // echo $service_id;
            // $customer_id    = $request->customer_id;
           
            $addons         = DB::table('service_has_addons')
                                ->leftjoin('addons', 'addons.id', '=', 'service_has_addons.addon_id')
                                ->select('addons.id' ,'addons.name')
                                ->where('service_has_addons.service_id',$service_id)
                                ->where('service_has_addons.item_id',$item_id)
                                ->pluck("name","id")
                                ->all();
                                // dd($addons);
            
            $service_addons = view('order_details.ajax-addons',compact('addons','service_id', 'item_id'))->render();
            return response()->json(['data'=>$service_addons]);
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
                                                'orders.delivery_date')
                                        ->whereNull('orders.delivery_rider_id')
                                        ->whereNotNull('orders.pickup_rider_id')
                                        ->find($id);

        if($data){
            $holidays              =  DB::table('holidays')
                                        ->select('holiday_date')
                                        ->get();

            $customer_id            = $data->customer_id;
            $statuses               = DB::table('statuses')
                                        ->where('statuses.id', 4)
                                        // ->whereBetween('statuses.id', [12, 15])
                                        ->pluck('name','id')
                                        ->all();

            $services               = DB::table('customer_has_services')
                                        ->leftjoin('services', 'services.id', '=', 'customer_has_services.service_id')
                                        ->orderBy('customer_has_services.order_number')
                                        ->select('services.id','services.name','services.rate')
                                        ->where('customer_has_services.status','1')
                                        ->where('customer_has_services.customer_id', $customer_id)
                                        // ->where('services.unit_id','2')
                                        ->pluck('name','id')
                                        ->all();
                                        
        
            $selected_special_services = DB::table('customer_has_items')
                                        ->where('customer_has_items.customer_id', $customer_id)
                                        ->get()
                                        ->all();
                
            $customer_items            = array();
                                        foreach($selected_special_services as $cust_items)
                                            {
                                                $customer_items[$cust_items->service_id][$cust_items->item_id] = array(
                                                    'item_rate' => $cust_items->item_rate,
                                                    'status'    => $cust_items->status,
                                                    'item_id'    => $cust_items->item_id,
                                                );
                                            }
            
            $order_detail_natures      = Complaint_nature::pluck('name','id')->all();

            $items                     = DB::table('service_has_items')
                                            ->orderBy('service_has_items.item_id')
                                            ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                                            ->select(
                                                    'items.id as item_id',
                                                    'items.name as item_name',
                                                    'service_has_items.service_id as service_id'
                                                    )
                                            // ->where('service_has_items.service_id',$service_id)
                                            // ->where('customer_has_items.customer_id',$customer_id)
                                            // ->pluck("name","id")
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
                                            // ->where('service_has_addons.service_id',$service_id)
                                            // ->where('service_has_addons.item_id',$item_id)
                                            // ->pluck("name","id")
                                            ->get()
                                            ->all();
            
            $selected_services         = DB::table('order_has_services')
                                            ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                            ->where('order_has_services.order_id', $id)
                                            ->select('services.id as service_id',
                                                    'services.name as service_name',
                                                    'order_has_services.weight as service_weight',
                                                    'order_has_services.qty as service_qty')
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

        

                            // dd($aa);


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
        //    dd($adn);


            // $selected_adds  = array();
            // if(!$selected_addons->isEmpty())
            // {
            //     foreach($selected_addons as $sAddon)
            //     {
            //         $selected_adds[$sAddon->service_id][$sAddon->item_id][] = $sAddon->addon_id;
            //         // $selected_adds[$sAddon->service_id][$sAddon->item_id]['ord_itm_id'] = $sAddon->ord_itm_id;
            //     }
            // }    
            
            // dd($selected_adds);
            return view('order_details.edit',
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
            return redirect()->route('order_details.index')
                    ->with('permission','Order Details already appended');
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
                    $addons[]      = DB::table('order_has_addons')
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

    public function create_order_detail($id)
    {
        $data                   = Order::orderBy('orders.created_at','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select(
                                                'orders.id',
                                                'customers.id as customer_id',
                                                'customers.name',
                                                'customers.contact_no',
                                                'orders.id as order_id',
                                                'orders.pickup_date',
                                                'customers.permanent_note',
                                                'orders.order_note',
                                                'orders.delivery_date'
                                            )
                                    ->whereNull('orders.delivery_rider_id')
                                    ->whereNotNull('orders.pickup_rider_id')
                                    ->where('orders.id', '=', $id)
                                    ->where('orders.status_id', '=', 1)
                                    ->first();

        if(($data)){
            $customer_id            = $data->customer_id;

            $statuses               = DB::table('statuses')
                                        ->where('statuses.id', 4)
                                        ->pluck('name','id')
                                        ->all();

            $services               = DB::table('customer_has_services')
                                        ->leftjoin('services', 'services.id', '=', 'customer_has_services.service_id')
                                        ->orderBy('order_number','ASC')
                                        ->select(
                                                    'services.id',
                                                    'services.name',
                                                    'services.rate'
                                                )
                                        ->where('customer_has_services.status','1')
                                        ->where('customer_has_services.customer_id', $customer_id)
                                        ->pluck('name','id')
                                        ->all();
    
            $holidays                   =  DB::table('holidays')
                                                ->select('holiday_date')
                                                ->get();
            return view('order_details.create',
                         compact(
                                'data',
                                'statuses',
                                'services',
                                'holidays'
                               )
                       );
        }else{
            return redirect()->route('order_details.index')
                            ->with('permission','Order Details already appended');
        }
    }

    public function update(Request $request, $id)
    {
        // $this->create_json_history(68);
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
                ],
                [
                    'weight.*.required'     => 'Please enter weight !',
                    'weight.*.min'          => 'Please use value greater than 0 KG !',
                    'weight.*.not_in'       => 'weight cannot be 0 KG !'
                    
                ]
            );

            $order_id                   = $id;
            $data                       = Order::find($order_id);
            $input                      = $request->all();
            
            $order['delivery_date']     = $request['delivery_date'];
            $order['order_note']        = $request['order_note'];
            $order['status_id']         = $request['status_id'];
            $order['status_id2']        = $request['status_id'];

            $upd = $data->update($order);

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
            DB::table("order_has_services")->where('order_id', '=', $id)->delete();
            DB::table("order_has_items")->where('order_id', '=', $id)->delete();
            DB::table("order_has_addons")->where('order_id', '=', $id)->delete();
            if($services){
                foreach($services as $service_key => $service_value){
                    $var                = new Order_has_service();
                    $var->order_id      = $order_id;
                    $var->service_id    = $service_value;
                    $var->qty           = $qtys[$service_key];
                    $var->weight        = $weights[$service_key];
                    $var->save();

                    if($items[$service_key]){

                        foreach($items[$service_value] as $item_key => $item_value){
                            // $pickup_qtys[$service_key][$item_key];
                            $item                = new Order_has_item();
                            $item->order_id      = $order_id;
                            $item->service_id    = $service_value;
                            $item->item_id       = $item_value;
                            $item->pickup_qty    = $pickup_qtys[$service_key][$item_key];
                            $item->note          = $notes[$service_key][$item_key];
                            $item->save();

                            if(!(empty($addon_ids[$service_key][$item_key]))){
                                $addons          = explode(',', $addon_ids[$service_key][$item_key]);
                                if(!(empty($addons))){
                                    foreach ($addons as $addon_key => $addon_value) {
                                        $addon                = new Order_has_addon();
                                        $addon->order_id      = $order_id;
                                        $addon->item_id       = $item_value;
                                        $addon->addon_id      = $addon_value;
                                        $addon->service_id    = $service_value;
                                        $addon->ord_itm_id    = $item->id;  // get last inserted id of "order_has_items"
                                        $addon->save();
                                    }
                                }
                            }

                            

                            //  Image uploading "PHASE-II"
                            /*
                                if(!empty($request['item_image'][$service_value][$item_key])){
                                    $temp_image = $request['item_image'][$service_value][$item_key];
                                    $this->validate($request,[
                                        'item_image['.$service_value.']['.$item_key.']'=>'image|mimes:jpeg,png,jpg,gif|max:2048']);
                                    $image      = $request->file('item_image')[$service_value][$item_key];
                                    $new_name   = rand().'.'.$request['item_image'][$service_value][$item_key]->getClientOriginalExtension();
                                    $image->move(public_path("uploads/orders"),$new_name);
                                    $item->item_image = $new_name;
                                }
                            */

                            // if(!(empty($addons))){
                            //     if(!(empty($addons[$service_value][$item_value]))){
                            //         foreach($addons[$service_value][$item_value] as $addon_key => $addon_value){
                            //             $addon                = new Order_has_addon();
                            //             $addon->order_id      = $order_id;
                            //             $addon->service_id    = $service_value;
                            //             $addon->item_id       = $item_value;
                            //             $addon->addon_id      = $addon_key;
                            //             $addon->ord_itm_id    = $item->id;  // get last inserted id of "order_has_items"
                            //             $addon->save();
                            //         }
                            //     }
                            // }
                        }
                    }
                }
            }

             // BEGIN::  Storing the Order History
             $val                     = new Order_history();
             $val->order_id           = $order_id;
             $val->created_by         = Auth::user()->id;
             $val->detail             = $this->create_json_history($order_id);
             $val->status_id          = $request['status_id'];
             $val->save();
             // END::    Storing the Order History

            return redirect()
                    ->route('order_details.index')
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
