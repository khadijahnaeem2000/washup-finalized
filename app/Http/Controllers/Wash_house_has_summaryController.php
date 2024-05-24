<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use DataTables;
use App\Models\Order;
use App\Models\Rate_list;
use App\Models\Wash_house;
use App\Models\Order_history;
use App\Models\Distribution_hub;
use App\Models\Wash_house_has_order;
use App\Models\Wash_house_has_summary;


class Wash_house_has_summaryController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:Wash_house_summary-list', ['only' => ['index','show']]);
         $this->middleware('permission:Wash_house_summary-create', ['only' => ['create','store']]);
         $this->middleware('permission:Wash_house_summary-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:Wash_house_summary-delete', ['only' => ['destroy']]);
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
     
        return view('wash_house_has_summaries.index',compact('hubs'));
    }

    public function list($hub_id)
    {
                  DB::statement(DB::raw('set @srno=0'));
        $date   = date("Y-m-d");
        $data   = Order::orderBy('orders.created_at','DESC')
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
                                'wash_houses.name as wash_house_name',
                                DB::raw('(CASE 
                                    WHEN orders.status_id2 IS NULL THEN "Moved to hub" 
                                    ELSE statuses.name 
                                    END) AS status_name'
                                ),
                                DB::raw('(CASE 
                                    WHEN wash_house_has_orders.summary_printed = "0" THEN "No"
                                    WHEN wash_house_has_orders.summary_printed = "1" THEN "Yes" 
                                    END) AS summary_printed'
                                )
                            )
                    ->where('orders.hub_id',$hub_id)
                    ->whereNotNull('orders.pickup_rider_id')
                    ->whereIn('orders.status_id2', [10])
                    ->whereDate('orders.delivery_date', '>=', $date)
                    ->whereNull('orders.delivery_rider_id')
                    ->get();
        
        return 
            DataTables::of($data)
                ->make(true);
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

    public function create()
    {
    }

        // Printing summary and update column in order "sumary_printed
    public function store(Request $request)
    {
        request()->validate([
            'pickup_date'   => 'required',
            'wash_house_id' => 'required'
        ]);

        $tot_services       = DB::table('services')
                                ->count('services.id');
                                
        $services           = DB::table('services')
                                ->orderBy('services.id')
                                ->pluck('services.name','services.id')
                                ->all();

        $pickup_date        = $request['pickup_date'];
        $delivery_date      = $this->get_delivery_date($pickup_date);
        //            echo $pickup_date;           
        $wash_house_id      = $request['wash_house_id'];
        $data               = Wash_house::find( $wash_house_id );
        $wash_house_name    = $data->name;


        $reg_orders         = DB::table('wash_house_has_orders')
                                ->leftjoin('order_has_items', 'order_has_items.order_id', '=', 'wash_house_has_orders.order_id')
                                ->leftjoin('orders', 'orders.id', '=', 'wash_house_has_orders.order_id')
                                // ->where('wash_house_has_orders.wash_house_id', $wash_house_id)
                                // ->whereDate('order_has_items.created_at', $pickup_date)
                                ->where('wash_house_has_orders.wash_house_id',$wash_house_id)
                                ->whereDate('orders.pickup_date',"=", $pickup_date)
                                ->whereDate('orders.delivery_date',"=", $delivery_date)
                                ->select('order_has_items.*')
                                ->whereIn('orders.status_id2', [10])
                                ->get();
                                

        $urg_orders         = DB::table('wash_house_has_orders')
                                ->leftjoin('order_has_items', 'order_has_items.order_id', '=', 'wash_house_has_orders.order_id')
                                ->leftjoin('orders', 'orders.id', '=', 'wash_house_has_orders.order_id')
                                // ->where('wash_house_has_orders.wash_house_id', $wash_house_id)
                                // ->whereDate('order_has_items.created_at', $pickup_date)
                                ->whereDate('orders.pickup_date',"=", $pickup_date)
                                ->whereDate('orders.delivery_date',"!=", $delivery_date)
                                ->select('order_has_items.*')
                                ->whereIn('orders.status_id2', [10])
                                ->where('wash_house_has_orders.wash_house_id',$wash_house_id)
                                ->get();

        $order_services     = DB::table('wash_house_has_orders')
                                ->leftjoin('order_has_items', 'order_has_items.order_id', '=', 'wash_house_has_orders.order_id')
                                ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                ->where('wash_house_has_orders.wash_house_id', $wash_house_id)
                                // ->whereDate('order_has_items.created_at', $pickup_date)
                                ->whereDate('wash_house_has_orders.created_at', $pickup_date)
                                ->where('wash_house_has_orders.wash_house_id',$wash_house_id)
                                ->select(
                                            'services.name',
                                            'order_has_items.service_id as service_id'
                                        )
                            
                                ->groupBy('order_has_items.service_id')
                                ->groupBy('services.name')
                                ->orderBy('order_has_items.service_id')

                                ->get();
                            
        $order_items        = DB::table('wash_house_has_orders')
                                ->leftjoin('order_has_items', 'order_has_items.order_id', '=', 'wash_house_has_orders.order_id')
                                ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                // ->leftjoin('wash_house_has_orders', 'order_has_items.order_id', '=', 'order_has_items.order_id')
                                ->where('wash_house_has_orders.wash_house_id', $wash_house_id)
                                // ->whereDate('order_has_items.created_at', $pickup_date)
                                ->whereDate('wash_house_has_orders.created_at', $pickup_date)
                                ->where('wash_house_has_orders.wash_house_id',$wash_house_id)
                                ->select(
                                            'order_has_items.item_id as item_id',
                                            'items.short_name as short_name',
                                            'order_has_items.service_id as service_id',
                                             DB::raw('SUM(order_has_items.pickup_qty) as pickup_qty'),
                                            'services.name as service_name'
                                            )
                                ->groupBy('items.short_name')
                                ->groupBy('services.name')
                                ->groupBy('order_has_items.item_id')
                                ->groupBy('order_has_items.service_id')
                                ->orderBy('order_has_items.service_id')
                                ->get();

                            
        // // // // // // // // // // // // // // // // // // // // // // // // // // 
        $regular_orders          = array();
        foreach($reg_orders as $order)
        {
            if(!array_key_exists( $order->order_id,$regular_orders)){
                $regular_orders[$order->order_id] = array(
                    'order_id'  => $order->order_id,
                    'note'      => $order->note
                );

                // Update summary printed column
                DB::table("wash_house_has_orders")
                    ->where('wash_house_id',$wash_house_id )
                    ->where('order_id',$order->order_id )
                    ->update(array('summary_printed' => 1));

                
                foreach($services as $key=>$value){
                    $regular_orders[$order->order_id][$key] = 0;
                }
                   
                foreach($services as $key=>$value){
                    if($order->service_id == $key){
                        if($order->service_id == $key) $regular_orders[$order->order_id][$key] = $order->pickup_qty;
                    }
                }

            }else{

                foreach($services as $key=>$value){
                    if($order->service_id == $key){
                        $regular_orders[$order->order_id][$key] = $regular_orders[$order->order_id][$key] + $order->pickup_qty;
                    }
                }
            }
        }
      
        $urgent_orders          = array();
        foreach($urg_orders as $order)
        {
            if(!array_key_exists( $order->order_id,$urgent_orders)){
                $urgent_orders[$order->order_id] = array(
                    'order_id' => $order->order_id,
                    'note' => $order->note
                );
                
                // Update summary printed column
                DB::table("wash_house_has_orders")
                    ->where('wash_house_id',$wash_house_id )
                    ->where('order_id',$order->order_id )
                    ->update(array('summary_printed' => 1));

                foreach($services as $key=>$value){
                    $urgent_orders[$order->order_id][$key] = 0;
                }
                   
                foreach($services as $key=>$value){
                    if($order->service_id == $key){
                        if($order->service_id == $key) $urgent_orders[$order->order_id][$key] = $order->pickup_qty;
                    }
                }

            }else{

                foreach($services as $key=>$value){
                    if($order->service_id == $key){
                        $urgent_orders[$order->order_id][$key] = $urgent_orders[$order->order_id][$key] + $order->pickup_qty;
                    }
                }
            }
        }

        if((!($reg_orders->isEmpty())) || (!($urg_orders->isEmpty()))){
            DB::table("wash_house_has_orders")
                    ->where('wash_house_id',$wash_house_id )
                    ->whereDate('created_at',$pickup_date )
                    ->update(array('summary_printed' => 1));

            return view('wash_house_has_summaries.show',
                   compact(
                            'services',
                            'pickup_date',
                            'delivery_date',
                            'urgent_orders',
                            'regular_orders',
                            'order_services',
                            'wash_house_name',
                        ));    
        }else{
            return redirect()->route('wash_house_summaries.index')
                            ->withInput($request->input())
                            ->with('permission','No record found.');
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
