<?php
namespace App\Http\Controllers;
use DB;
use Auth;
use Validator;
use DataTables;
use App\Models\Order;
use App\Models\Status;
use App\Models\Service;
use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Models\Order_history;
use App\Models\Order_has_bag;
use App\Models\Order_has_item;
use App\Models\Order_has_addon;
use App\Models\Distribution_hub;
use App\Models\Complaint_nature;
use App\Models\Order_has_service;
use App\Http\Controllers\Controller;

use App\Exports\HfqItemExport;
use Maatwebsite\Excel\Facades\Excel;

class Order_hfqController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:order_hfq-list', ['only' => ['index','show']]);
        $this->middleware('permission:order_hfq-create', ['only' => ['create','store']]);
        $this->middleware('permission:order_hfq-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:order_hfq-delete', ['only' => ['destroy']]);
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
        return view('order_hfqs.index',compact('hubs'));
    }
  
    public function list($hub_id)
    {
                      DB::statement(DB::raw('set @srno=0'));
        $date       = date("Y-m-d");
        $data       = Order::orderBy('orders.updated_at','DESC')
                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                        ->leftjoin('statuses', 'statuses.id', '=', 'orders.status_id2')
                        ->select(
                                    DB::raw('@srno  := @srno  + 1 AS srno'),
                                    'orders.id',
                                    'orders.ref_order_id',
                                    'customers.name',
                                    'customers.contact_no',
                                    'orders.id as order_id',
                                    'orders.pickup_date',
                                    'orders.delivery_date',
                                    'statuses.id as status_id2',
                                    DB::raw('(CASE 
                                            WHEN orders.status_id2 IS NULL THEN "Moved to hub" 
                                            ELSE statuses.name 
                                            END) AS status_name'
                                    )
                                )
                        // 10: Moved to wash-house
                        // 11: recieved to hub
                        // 12: content verified
                        ->where('orders.hub_id',$hub_id)
                        ->whereNotNull('orders.ref_order_id')
                        ->whereIn('orders.status_id2',[6,12,13,14,17])
                        // ->orWhereNull('orders.status_id2')
                        // ->whereNotNull('orders.pickup_rider_id')
                        ->get();
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        
                    <a class="btn btn-secondary btn-sm" href="order_hfqs/'.$data->id.'/edit" id="'.$data->id.'">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <a  href="/order_hfqs/show_bags/'.$data->id.'" class="btn btn-primary  btn-sm">
                        <i class="fa fa-tags"></i>
                    </a>
                    <a  href="/order_hfqs/special_show_bags/'.$data->id.'" class="btn btn-success  btn-sm chk_prm">
                        <i class="fa fa-tags"></i>
                    </a>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);
    }

    public function create()
    {
    }


    public function export_hfq_items(Request $request){
        
        request()->validate([
            'from_date'       => 'required|date',
            'to_date'         => 'required|date|after:from_date',
        ]);

        $orders         = DB::table('order_has_items')
                            ->orderBy('order_has_items.order_id','ASC')
                            ->join('items', 'items.id', '=', 'order_has_items.item_id')
                            ->leftjoin('orders', 'orders.id', '=', 'order_has_items.order_id')
                            ->leftjoin('wash_house_has_orders', 'wash_house_has_orders.order_id', '=', 'orders.id')
                            ->leftjoin('wash_houses', 'wash_houses.id', '=', 'wash_house_has_orders.wash_house_id')
                            
                            
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->select(
                                
                                        'order_has_items.created_at as packing_date',
                                        'order_has_items.order_id',
                                        'order_has_items.order_id as hfq_order_no',
                                        'customers.name as customer_name',
                                        // 'order_has_items.item_id',
                                        'items.name as item_name',
                                        'order_has_items.hfq_qty',
                                        'wash_houses.name as wash_house_name'
                                    )
                            ->where('order_has_items.hfq_qty','>',0)
                            // $request['from_date'], $request['to_date']
                            ->whereBetween('order_has_items.created_at', [$request['from_date'],$request['to_date']])  
                            ->get();

        if(($orders->count()) > 0){
            foreach ($orders as $key => $value) {
                $hfq_order      = $this->get_hfq_ord_no($value->order_id);
                if((isset($hfq_order->id))){
                    $orders[$key]->hfq_order_no     =  $hfq_order->id;
                }
                $orders[$key]->packing_date         = date('d-M-Y', strtotime($value->packing_date));
            }

           
            $data = new HfqItemExport($orders);
            return (Excel::download($data, 'invoices.csv'));
            
        }else{
            $status = "permission";
            $msg    = "No order found! Please select another range";
            return redirect()->route('order_hfqs.index')
                                ->with($status,$msg);  
        }

    }

    public function get_hfq_ord_no($order_id){
        $hfq_order     = DB::table('orders')
                            ->select(
                                        'orders.id',
                                    )
                            ->where('orders.ref_order_id', $order_id)
                            ->first();
        return $hfq_order;
    }


    // updating order status for "receiving to hub from wash_houses" //
    public function store(Request $request)
    {
        // updating order status for "receiving to hub from wash_houses" //

        $order_ids = $request['order_id'];
        if( $order_ids){
            foreach($order_ids as $key => $value){
                $input['order_id'] =$key;
                $input['status_id2'] =16;
                // 16: recieved to hub

                // 15:moved to wash-house

                //Update order status in Orders table//
                $order = Order::find($key);
                if ($order->status_id2 != 15){
                    $status = "permission";
                    $msg = "Order status not match";
                     return redirect()->route('order_hfqs.index')
                         ->with($status,$msg);  
                }
                $order->update($input);

                //Insert order status in Order_history table//
                $val                = new Order_history();
                $val->order_id      = $key;
                $val->status_id     = 16;
                $val->save();

                
            }
            $status = "success";
            $msg = "Selected order Received to Hub.";
        }else{
            $status = "permission";
            $msg = "No order found! Please select order";
        }

        return redirect()->route('order_hfqs.index')
                         ->with($status,$msg);   
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
                                            'orders.pickup_date',
                                            'customers.permanent_note',
                                            'orders.order_note',
                                            'orders.delivery_date',
                                            'statuses.name as status_name')
                                    ->whereNull('orders.delivery_rider_id')
                                    ->whereNotNull('orders.pickup_rider_id')
                                    ->find($id);


        $selected_services      = DB::table('order_has_services')
                                    ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                    ->where('order_has_services.order_id', $id)
                                    ->select('services.id as service_id',
                                             'services.name as service_name',
                                             'order_has_services.weight as service_weight',
                                             'order_has_services.qty as service_qty')
                                    ->orderBy('order_number','ASC')
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


            // dd($details);
                                    


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
                                             'services.name as service_name')
                                    ->get()
                                    ->all(); 

        $selected_addons        = DB::table('order_has_addons')
                                    ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                    ->where('order_has_addons.order_id', $id)
                                    ->select('addons.id as addon_id',
                                             'addons.name as addon_name',
                                             'order_has_addons.item_id as item_id',
                                             'order_has_addons.service_id as service_id')
                                    ->get()
                                    ->all(); 

        $histories              = DB::table('order_histories')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'order_histories.status_id')
                                    ->where('order_histories.order_id', $id)
                                    ->select('statuses.id as status_id',
                                             'statuses.name as status_name',
                                             'order_histories.created_at as created_at')
                                    ->get()
                                    ->all();
                                    // dd($selected_addons);




        
        return view('order_inspects.show',
                    compact('data',
                            'selected_services',
                            'selected_items',
                            'selected_addons',
                            'histories',
                            'details'
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

    public function get_ref_order_id($order_id){
        $data                   = Order::select('orders.ref_order_id')
                                    ->where('orders.id', $order_id)
                                    ->first();
        return $data->ref_order_id;
    }

    public function edit($id)
    {


        // $is_inspected       = DB::table('order_histories')
        //                         ->where('order_histories.order_id', $id)
        //                         ->where('order_histories.status_id', 12)
        //                         ->first();
        //                     // 12 : Content Inspected

        // if(isset($is_inspected->order_id)){
        //     return redirect()->route('order_hfqs.index')
        //                         ->with("permission","Order is already inspected");   
        // }

        // $is_pbag_printed    = DB::table('order_histories')
        //                         ->where('order_histories.order_id', $id)
        //                         ->where('order_histories.status_id', 13)
        //                         ->first();

        // if(isset($is_pbag_printed->order_id)){
        //     return redirect()->route('order_hfqs.index')
        //                      ->with("permission","Order's polybags are already printed");   
        // }


        $data                   = Order::orderBy('orders.created_at','DESC')
                                    ->select('orders.ref_order_id')
                                    ->where('orders.id', $id)
                                    // ->whereIn('orders.status_id2',[6,17])  
                                    ->first();
        if($data){
            $hfq_order_id           = $id;
            $ref_order_id           = $data->ref_order_id;
            $id                     = $ref_order_id;
            // dd("sadf");
            $data                   = Order::orderBy('orders.created_at','DESC')
                                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                        ->select('orders.id',
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
                                                'orders.softner_rating')
                                       // ->whereNull('orders.delivery_rider_id')
                                        // ->whereNotNull('orders.pickup_rider_id')
                                        ->where('orders.id', $hfq_order_id)
                                        // ->whereIn('orders.status_id2', [5])   // received to Hub
                                        ->first();
    
                                        // 16 received to Hub
                                        // 8  inspection
            if($data){
                $customer_id               = $data->customer_id;
            
                                           
                $statuses                  = DB::table('statuses')
                                                ->where('statuses.id',8)
                                                ->pluck('name','id')
                                                ->all();
    
                $reasons                   = DB::table('reasons')
                                                ->pluck('name','id')
                                                ->all();
    
                $ratings                   = DB::table('ratings')
                                                ->pluck('name','id')
                                                ->all();
    
                $services                  = DB::table('customer_has_services')
                                                ->leftjoin('services', 'services.id', '=', 'customer_has_services.service_id')
                                                ->orderBy('order_number','ASC')
                                                ->select('services.id','services.name','services.rate')
                                                ->where('customer_has_services.status','1')
                                                // ->where('services.unit_id','2')
                                                ->pluck('name','id')
                                                ->all();
                                            
               
                $selected_services         = DB::table('order_has_services')
                                                ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                                ->where('order_has_services.order_id', $id)
                                                ->select('services.id as service_id',
                                                        'services.name as service_name',
                                                        'order_has_services.weight as service_weight',
                                                        'order_has_services.qty as service_qty')
                                                ->orderBy('order_number','ASC')
                                                ->get()
                                                ->all();        
    
    
                $selected_items            = DB::table('order_has_items')
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
                                                        'order_has_items.reason as reason',
                                                        'order_has_items.reason_id as reason_id',
                                                        'services.name as service_name')
                                                ->get()
                                                ->all(); 
                
                
                                        
                return view('order_hfqs.edit',
                                compact('data',
                                'statuses','services', 'selected_services',
                                'selected_items',
                                'ratings',
                                'hfq_order_id',
                                'ref_order_id',
                                'reasons',
                                
                            )
                        );
            }else{
                $status = "permission";
                $msg = "This order has already inspected!";
            }
        }else{
            $status = "permission";
            $msg = "This order has already inspected!";
        }
        

        return redirect()->route('order_hfqs.index')
                         ->with($status,$msg);   
    }

    public function show_bags($order_id)
    {
        $is_printed     = Order::select('orders.polybags_printed')
                            ->find($order_id);

        $ref_order_id   = $this->get_ref_order_id($order_id);
                       
        if($is_printed->polybags_printed==1){
            return redirect()
                    ->back()
                    ->with('permission','Tags are already printed.');
        }elseif($is_printed->polybags_printed==0){
            $tot_bags   = DB::table("order_has_bags")
                            ->where('order_has_bags.order_id','=', $order_id)
                            ->count('order_has_bags.order_id');
            if($tot_bags<1){
                return redirect()
                        ->back()
                        ->with('permission','First Inpect the order.');
            }

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

            
            return view('order_hfqs.show_bag',
                    compact(
                            'bags',
                            'tot_bags',
                            'ref_order_id'
                        )
                    );   
        
        }
    }

    public function special_show_bags($order_id)
    {
        $tot_bags   = DB::table("order_has_bags")
                        ->where('order_has_bags.order_id','=', $order_id)
                        ->count('order_has_bags.order_id');

        $ref_order_id   = $this->get_ref_order_id($order_id);
        if($tot_bags<1){
            return redirect()
                    ->back()
                    ->with('permission','First Inpect the order.');
        }


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
        
        return view('order_hfqs.show_bag',
                compact(
                        'bags',
                        'tot_bags',
                        'ref_order_id'
                    )
                );   
        
    }

    public function update(Request $request, $id)
    {
      
        request()->validate([
            'polybags_qty' => 'required|min:1|numeric',
        ]);
      
        if($request['item_id']){
            request()->validate([
                'customer_id'           => 'required',
            ]);
            $order_id                   = $id;
            $data                       = Order::find($order_id);
            $input                      = $request->all();

            $order['softner_rating']    = $request['softner_rating'];
            $order['iron_rating']       = $request['iron_rating'];
            $order['status_id2']        = 12;

            $upd = $data->update($order);

            $val                        = new Order_history();
            $val->order_id              = $order_id;
            $val->created_by            = Auth::user()->id;
            $val->status_id             = 12;
            $val->save();

            DB::table("order_has_bags")->where('order_id', '=', $order_id)->delete();
            for($i=0; $i<$request['polybags_qty']; $i++){
                $val                        = new Order_has_bag();
                $val->order_id              = $order_id;
                $val->save();
            }

            return redirect()
                    ->route('order_hfqs.index')
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
