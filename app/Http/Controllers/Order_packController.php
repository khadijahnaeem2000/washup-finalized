<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use DataTables;
use App\Models\Order;
use App\Models\Status;
use App\Models\Service;
use App\Models\Complaint;
use App\Models\Order_history;
use App\Models\Order_has_item;
use App\Models\Order_has_addon;
use App\Models\Complaint_nature;
use App\Models\Order_has_service;
use App\Models\Customer_has_address;
use App\Models\Time_slot;





class Order_packController extends Controller
{
    public $today;
    function __construct()
    {
         $this->today =  date('Y-m-d');
         $this->middleware('permission:order_pack-list', ['only' => ['index','show']]);
         $this->middleware('permission:order_pack-create', ['only' => ['create','store']]);
         $this->middleware('permission:order_pack-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:order_pack-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $statuses           = DB::table('statuses')
                                // ->where('statuses.id', 1)
                                ->where('statuses.id', 2)
                                ->orwhere('statuses.id', 3)
                                ->pluck('name','id')
                                ->all();
                                
        $time_slots         = DB::table('time_slots')
                                ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                ->pluck('name','id')
                                ->all();
                                $pickup = array();
                                $dropoff = array();

        foreach($time_slots as $key => $value){
            $pickup[$key]=   0;
            $dropoff[$key]=   0;
        }

        foreach($time_slots as $key => $value){

            $pickup_order       = Order::orderBy('orders.id','DESC')
                                ->select(
                                        'orders.pickup_timeslot_id',
                                        )
                                ->where('orders.pickup_timeslot_id', $key) 
                                ->whereNull('orders.delivery_timeslot_id')
                                ->whereNotNull('orders.pickup_rider_id')
                                ->whereIn('orders.status_id', [2,3,8,15,16]) 
                                ->count();
                               
            $pickup[$key]+=   $pickup_order;
        }
        // dd($pickup);

        foreach($time_slots as $key => $value){

            $delivery_order = Order::orderBy('orders.id','DESC')
                                ->select(
                                        'orders.delivery_timeslot_id',
                                        )
                                ->where('orders.pickup_timeslot_id', $key) 
                                ->whereNotNull('orders.delivery_timeslot_id')
                                ->whereNotNull('orders.pickup_timeslot_id')
                                ->whereIn('orders.status_id', [2,3,8,15,16]) 
                                ->count();
                               
            $dropoff[$key]+=   $delivery_order;
        }



        return view('order_packs.index',
                    compact(
                            'pickup',
                            'dropoff',
                            'statuses',
                            'time_slots',
                        )
                    );
        // return view('order_packs.index');
    }

    public function list()
    {
        $data = Order::orderBy('orders.id','DESC')
                    // ->orderBy('orders.updated_at','DESC')
                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                    ->leftjoin('statuses', 'statuses.id', '=', 'orders.status_id')
                    ->select('orders.id',
                             'orders.ref_order_id',
                             'customers.name',
                             'customers.contact_no',
                             'orders.id as order_id',
                             'orders.pickup_date',
                             'customers.permanent_note',
                             'orders.order_note',
                             'orders.delivery_date',
                             'statuses.id as status_id',
                             'statuses.name as status_name',
                            //   DB::raw('(CASE 
                            //   WHEN orders.status_id = "8" THEN "Packed" 
                            //   ELSE statuses.name 
                            //   END) AS status_name'),
                              DB::raw('(CASE 
                              WHEN orders.status_id2 = "8" THEN "Y" 
                              ELSE "N"
                              END) AS pack_status'),
                              DB::raw('(CASE 
                              WHEN isNULL(orders.ref_order_id) THEN "Reg" 
                              ELSE "HFQ"
                              END) AS order_type')
                            )
                    ->whereNull('orders.delivery_rider_id')
                    ->whereNotNull('orders.pickup_rider_id')
                    // ->where('orders.status_id', 15)
                    // ->where('orders.status_id', 2)
                    // ->Orwhere('orders.status_id', 3)
                    // ->Orwhere('orders.status_id', 8)
                    // 02: drop off
                    // 03: pick & drop
                    // 08: inspection
                    // 15: Moved to wash-house
                    // 16: Recieved to Hub
                    // 05: pending
                    // add 5 if you want to show the pending status order
                    ->whereIn('orders.status_id', [2,3,8,15,16,]) 
                  
                   
                    ->get();
                    // dd($data);

                    
                
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-primary btn-sm" href="order_packs/'.$data->id.'">
                            <i class="fa fa-print"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="order_packs/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    
                    </div>';
                })
                ->addColumn('checkbox','<div class="checkbox-inline"> <label class="checkbox checkbox-success"><input type="checkbox" name="order_id[{{$id}}]" /><span></span> </label></div>')
                ->addColumn('srno','')
                ->rawColumns(['checkbox','srno','','action'])
                ->make(true);
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
  
    // change timeslots and order status
    public function create()
    {
       
    }
    public function get_customer_primary_address_id($customer_id){
        $data  = DB::table('customer_has_addresses')
                    ->select('customer_has_addresses.id','customer_has_addresses.address')
                    ->where('customer_has_addresses.status',0)
                    ->where('customer_has_addresses.customer_id',$customer_id)
                    ->first();

        return $data;
    }


    public function store(Request $request)
    {
        // Assign wash-houses to selected orders//
        $this->validate($request, 
            [
                'order_id'                          => 'required',
                'status_id'                         => 'required',
                'delivery_timeslot_id'              => 'required'
            ],
            [
                'order_id.required'                 => 'Please select order(s)!',
                'status_id.required'                => 'Please select status!',
                'delivery_timeslot_id.required'     => 'Please select time slot!',
                
            ]
        );

       
        $order_ids  = $request['order_id'];
        $status_id  = $request['status_id'];
      
        // checking whether wash house order summary printed or not ///
        foreach($order_ids as $key => $value){
            $data                         = Order::find($key);
            

            $inputs                       = $request->all();
            $delivery_timeslot            = $this->get_timeslot($inputs['delivery_timeslot_id']);
            $inputs['delivery_timeslot']  = $delivery_timeslot;

            $delivery_address             = $this->get_customer_primary_address_id($data->customer_id);
            $inputs['delivery_address_id'] = $delivery_address->id;
            $inputs['delivery_address']   = $delivery_address->address;
            
            $upd = $data->update($inputs);

            $order_id                   = $key;
           

            if($upd){
                $var                = new Order_history();
                $var->order_id      = $order_id;
                $var->status_id     = $status_id;
                $var->save();
            }
            
        }

        

        
        
        return redirect()->route('order_packs.index')
            ->with('success','Delivery detail of Order of '.$request['name']. '  added successfully');
    
     
        
    }

    public function assign_rider(){
        $data       = Order::where('status_id', 2)
                        ->Orwhere('status_id', 3)
                        ->update(
                                ['delivery_rider_id' => '1']
                            );

        return redirect()->route('order_packs.index')
                            ->with('success','Rider assigned for delivery successfully.');
    }


    public function show($id)
    {
        $data                   = Order::orderBy('orders.created_at','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'orders.status_id')
                                    ->select('orders.*',
                                            'customers.id as customer_id',
                                            'customers.name as name',
                                            'customers.contact_no',
                                            'customers.permanent_note',
                                            'statuses.name as status_name',
                                            'orders.ref_order_id',
                                            )
                                    // ->whereNull('orders.delivery_rider_id')
                                    ->whereNotNull('orders.pickup_rider_id')
                                    ->find($id);

                                    $ref_histories ="";

        // $order_id = '.order_id';
        if($data->ref_order_id!=null){
           
            $ref_histories  = DB::table('order_histories')
                                ->leftjoin('statuses', 'statuses.id', '=', 'order_histories.status_id')
                                ->where('order_histories.order_id', $id)
                                ->select('statuses.id as status_id',
                                        'statuses.name as status_name',
                                        'order_histories.created_at as created_at')
                                ->get()
                                ->all();
                                // dd($ref_histories );
            $id = $data->ref_order_id;
        }
        // dd($id);
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
             
                $record            = DB::table('order_has_items')
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




        
        return view('order_packs.show',
                    compact('data',
                            'selected_services',
                            'selected_items',
                            'selected_addons',
                            'histories',
                            'details',
                            'ref_histories'
                        )
                    );
    }
 
  

    public function edit($id)
    {
        
        $data               = Order::orderBy('orders.id','DESC')
                                ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                ->select('orders.*',
                                        'customers.name as name',
                                        'customers.contact_no')
                                ->find($id);
        $customer_id        =  $data->customer_id;


        $addresses           = DB::table('customer_has_addresses')
                                ->select('id',
                                DB::raw('(CASE 
                                WHEN status = "0" THEN CONCAT(address, " (Primary)" ) 
                                WHEN status = "1" THEN CONCAT(address, " (Secondary)" ) 
                                END) AS address_name'))
                                ->where('customer_id', $customer_id)
                                ->pluck('address_name','id')
                                // ->get()
                                ->all();    
                            

                                // dd($addresses);
        $statuses           = DB::table('statuses')
                                // ->where('statuses.id', 1)
                                ->where('statuses.id', 2)
                                ->orwhere('statuses.id', 3)
                                ->pluck('name','id')
                                ->all();
                                
        $time_slots         = DB::table('time_slots')
                                ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                ->pluck('name','id')
                                ->all();

        $holidays       =  DB::table('holidays')
                                ->select('holiday_date')
                                ->get();

        return view('order_packs.edit',
                    compact('data',
                            'statuses',
                            'time_slots',
                            'addresses',
                            'holidays'
                        )
                    );
    }


    public function update(Request $request, $id)
    {
        //    dd($request);
        $data = order::find($id);
        request()->validate([
            'customer_id'           => 'required',
            'delivery_address_id'   => 'required',
            'delivery_date'         =>'required',
            'pickup_date'           => 'required|date',
            'delivery_date'         => 'required|date|after:pickup_date',
        ]);
        $inputs                     = $request->all();
        $delivery_address             = $this->get_address($inputs['delivery_address_id']);
        $delivery_timeslot            = $this->get_timeslot($inputs['delivery_timeslot_id']);
        
        $inputs['delivery_address']   = $delivery_address;
        $inputs['delivery_timeslot']  = $delivery_timeslot;

        $upd = $data->update($inputs);
        $order_id                   = $id;
        $status_id                  = $request['status_id'];

        if($upd){
            if($status_id){
                $var                =  new Order_history();
                $var->order_id      = $order_id;
                $var->status_id     = $status_id;
                $var->save();
            }
        }
        return redirect()->route('order_packs.index')
            ->with('success','Delivery detail of Order of '.$request['name']. '  added successfully');
    }

    public function destroy(Request $request)
    {
       
    }

      

    
}
