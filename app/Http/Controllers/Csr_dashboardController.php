<?php
namespace App\Http\Controllers;

use DB;
use Auth;
use Validator;
use DataTables;
use Carbon\Carbon;
use App\Models\Day;
use App\Models\Zone;
use App\Models\Order;
use App\Models\Holiday;
use App\Models\Time_slot;
use App\Models\Route_plan;
use App\Models\Payment_ride;
use Illuminate\Http\Request;
use App\Models\Order_history;
use App\Models\Hub_has_zone;
use App\Models\Zone_has_area;
use App\Models\Payment_ride_history;
use App\Models\Customer_has_address;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;

class Csr_dashboardController extends Controller
{
    public $today;
    function __construct()
    {
         $this->middleware('permission:csr_dashboard-list', ['only' => ['index','show']]);
         $this->middleware('permission:csr_dashboard-create', ['only' => ['create','store']]);
         $this->middleware('permission:csr_dashboard-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:csr_dashboard-delete', ['only' => ['destroy']]);
         $this->today =  date('Y-m-d');
    }

    public function index(Request $request)
    {
        $statuses       = DB::table('statuses')
                            ->where('statuses.id', 1)
                            // ->orWhere('statuses.id', 3)
                            ->pluck('name','id')
                            ->all();
                            // dd($statuses);

        $areas          = DB::table('areas')
                            ->pluck('center_points','id')
                            ->all();


        $areas          = DB::table('areas')
                            ->pluck('poly_points','id')
                            ->all();


        $holidays       = DB::table('holidays')
                                ->select(
                                            // 'holiday_date'
                                            DB::raw('DATE_FORMAT(holidays.holiday_date, "%Y/%m/%d") as holiday_date')
                                        )
                                ->get();
                                

        $time_slots     = DB::table('time_slots')
                            ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                            ->pluck('name','id')
                            ->all();

        return 
            view('csr_dashboards.index',
                compact(
                    'areas',
                    'statuses',
                    'holidays',
                    'time_slots',
                )
            );
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data           = Order::orderBy('id','DESC')
                            ->select(
                                        'orders.id',
                                        'orders.customer_id',
                                        DB::raw('@srno  := @srno  + 1 AS srno')
                                    )
                                    
                            ->whereIn('orders.status_id', [4])
                            ->get();

        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group"> 
                        <a class="btn btn-secondary btn-sm" href="distribution_hubs/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="distribution_hubs/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                     
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('distribution_hub_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
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
        if(isset($data->time_slot_name)){
            return $data->time_slot_name;
        }else{
            return 0;
        }
        
    }

    public function get_hub_id($area_id){
        if(isset($area_id)){
            $zone           = Zone_has_area::select('zone_has_areas.zone_id')
                                ->where('zone_has_areas.area_id',$area_id)
                                ->first();
        }else{
            return 0;
        }

        if(isset($zone->zone_id)){
            $hub            = hub_has_zone::select('hub_has_zones.hub_id')
                                ->where('hub_has_zones.zone_id',$zone->zone_id)
                                ->first();
        }else{
            return 0;
        }

        if(isset($hub->hub_id)){
            return $hub->hub_id;
        }else{
            return null;
        }

    }

    public function schedule_payment_ride(Request $request)
    {
        // $data                =  Payment_rid::orderBy('payment_rides.id','DESC')
        //                         ->where('customer_id',$request['customer_id'])
        //                         ->whereNotIn('status_id',[15,16])
        //                         // ->whereDate('pickup_date',$request['pickup_date'])
        //                         ->first();

        // if(!($data)){
      
            $validator = Validator::make($request->all(),
                [
                    'customer_id'           => 'required|array',
                    'timeslot_id'           => 'required|array',
                    'ride_date'             => 'required|array',
                ],
                [
                    'customer_id.required'  =>'Please select at least one ride',
                    'timeslot_id.max'       =>'Please select at least one ride',
                ]
            );

            if ($validator->passes()) {
                $customer = $request['customer_id'];

                foreach($customer as $key => $value){
                    $input['customer_id']       = $key;
                    $input['address_id']        = $request['address_id'][$key];
                    $input['bill']              = $request['bill'][$key];
                    $input['ride_date']         = $request['ride_date'][$key];
                    $input['timeslot_id']       = $request['timeslot_id'][$key];
                    $input['status_id']         = 5;
                    $input['created_by']        = Auth::user()->id;
                    $data                       = Payment_ride::create($input);
                    if($data){
                        $input['payment_ride_id'] = $data['id'];
                        Payment_ride_history::create($input);
                    }
                }
                return response()->json(['success'=>'All selected rides are scheduled successfully.']);
            }
            return response()->json(['error'=>$validator->errors()->all()]);
        
    }

    public function add_order(Request $request)
    {
      
        $dt = $request['pickup_date'];
        // checking :: is the same customer has order in queue in "orders" tables
        $data               =  Order::orderBy('orders.id','DESC')
                                ->select('orders.id','orders.status_id')
                                ->where('customer_id',$request['customer_id'])
                                // ->where('status_id',1)
                                ->Where(function($query) use ($dt){
                                    $query->whereDate('orders.pickup_date',$dt)
                                        ->OrWhereDate('orders.delivery_date',$dt);
                                })
                                // ->whereDate('pickup_date',$request['pickup_date'])
                                ->first();

        // checking :: is the same customer has order in queue in "route_plans" table
        $data2               =  DB::table('route_plans')
                                ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                                ->select('route_plans.id','orders.status_id')
                                ->where('orders.customer_id',$request['customer_id'])
                                // ->where('orders.status_id',1)
                                ->whereDate('orders.pickup_date',$request['pickup_date'])
                                ->first();

        $hub_id             = $this->get_hub_id($request['area_id']);
               

        if((isset($hub_id)) && ($hub_id != 0 )){
            // do nothing..
        }else{
            return response()->json(['error'=>[0=>"Unable to find hub-id for this order"]]);
        }
                                
                

        if( (!(isset($data->id))) && (!(isset($data2->id))) ){
      
            $validator = Validator::make($request->all(), [
                'customer_id'                  => 'required',
                'cus_address_id'               => 'required',
                'pickup_date'                  => 'required|date',
                'delivery_date'                => 'required|date|after:pickup_date',
            ],
            [
                'customer_id.required'          =>'Please select customer',
                'cus_address_id.required'       =>'Please select customer address'
            ]);

            $waver = $request->waver_delivery;
  
            if ($validator->passes()) {
                $inputs                        = $request->all();
                $inputs['hub_id']              = $hub_id;
                $inputs['pickup_address_id']   = $inputs['cus_address_id'];
                $inputs['pickup_timeslot_id']  = $inputs['timeslot_id'];
                $inputs['waver_delivery']      = $waver;
                $inputs['pickup_address']       = $this->get_address($inputs['cus_address_id']);
                $inputs['pickup_timeslot']      = $this->get_timeslot($inputs['timeslot_id']);
         
                 if($waver == 1){
                 $inputs['phase']                    = "Csr dashboard";
                $currentDateTime                = Carbon::now()->format('Y-m-d H:i:s');
                $inputs['DW_when']                  = $currentDateTime;
                $inputs['DW_who']                     = Auth::user()->id;
                }
                $data                           = Order::create($inputs);
                $order_id                       = $data['id'];
                $status_id                      = $request['status_id'];
                
               
            
                if($data){
                    if($status_id){
                        $var                    = new Order_history();
                        $var->order_id          = $order_id;
                        $var->created_by        = Auth::user()->id;
                        $var->status_id         = $status_id;
                        $var->save();
                        
                        
                    }

                }
                return response()->json(['success'=>'Order of '.$request['name']. ' added successfully.']);
            }
            return response()->json(['error'=>$validator->errors()->all()]);
        }else{

            if( ((isset($data->status_id)) && (($data->status_id) == 1)) || ((isset($data2->status_id)) && (($data2->status_id) == 1)) ){
                return response()->json(['error'=>[0=>"Order of this customer is already in queue on same date"]]);
            }else if( ((isset($data->status_id)) && (($data->status_id) == 2)) || ((isset($data2->status_id)) && (($data2->status_id) == 2)) ){
                return response()->json(['error'=>[0=>"Order of this customer is already in queue on same date, Please change status from drop to pick and drop"]]);
            }else{
                return response()->json(['error'=>[0=>"Order(pick & drop) of this customer is already in queue on same date, You can not add further order"]]);
            }
            
        }
    }

    public function get_customer_lat_lng(Request $request)
    {
        
        $addresses          = DB::table('customer_has_addresses')
                                ->select('id','latitude', 'longitude')
                                ->where('customer_has_addresses.id',$request->id)
                                ->first();
                             
        if($addresses){
            return response()->json(['data'=>$addresses]);
        }else{
            return response()->json(['error'=>"Data not found"]);
        }
            
    }

    public function fetch_customer_details(Request $request)
    {
        if($request->ajax()){
            $customer           = DB::table('customers')
                                        ->select('customers.id','customers.name','customers.email','customers.permanent_note')
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

    public function fetch_payment_orders(Request $request)
    {
        if($request->ajax()){
            $dt         = ($request->dt);
            $ids        = array();

            // get all non-complete orders
            // 15: complete
            // 16: cancelled
            $orders     = DB::table('orders')
                            ->select(
                                        'orders.id',
                                        'orders.customer_id'
                                    )
                            ->whereNotIn('orders.status_id',[15,16])
                            ->get();

            if(!($orders->isEmpty())){
                foreach ($orders as $key => $value) {
                    $ids[$key] = $value->id;
                }
            }


            $orders     = DB::table('customer_has_wallets')
                                ->leftjoin('customers', 'customers.id', '=', 'customer_has_wallets.customer_id')
                                ->leftjoin('customer_has_addresses', 'customer_has_addresses.customer_id', '=', 'customers.id')
                                ->select(
                                            'customers.id as customer_id',
                                            'customers.name as customer_name',
                                            'customers.contact_no as customer_contact_no',
                                            'customer_has_addresses.id as address_id',
                                            'customer_has_addresses.address',
                                            DB::raw('(sum(in_amount)-sum(out_amount)) AS bill'),
                                        )
                                ->groupBy('customers.id')
                                ->groupBy('customers.name')
                                ->groupBy('customers.contact_no')
                                ->groupBy('customer_has_addresses.id')
                                ->groupBy('customer_has_addresses.address')
                                ->whereNotIn('customers.id', function($q){
                                    // $q->select('customer_id')->whereNotIn('payment_rides.status_id',[16])->from('payment_rides');
                                    $q->select('customer_id')->whereIn('payment_rides.status_id',[5,6])->from('payment_rides');
                                })
                                ->where('customer_has_addresses.status',0)
                                ->whereNotIn('customer_has_wallets.order_id',$ids)
                                ->orWhereNull('customer_has_wallets.order_id')
                                ->having(DB::raw('(sum(in_amount))'),'<',DB::raw('(sum(out_amount))'))
                                ->get();
            

            if($orders){
                $time_slots     = DB::table('time_slots')
                                    ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                    ->pluck('name','id')
                                    ->all();
                $holidays       =  DB::table('holidays')
                                    ->select('holiday_date')
                                    ->get();

                $details = view('csr_dashboards.payment_order_table',
                                    compact(
                                            'dt',
                                            'orders',
                                            'time_slots',
                                            'holidays'
                                        )
                                    )
                                    ->render();
                return response()->json(['data'=>$orders,'details'=>$details,'dt'=>$dt]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
            
        }

    }

    public function fetch_reg_orders(Request $request)
    {
        if($request->ajax()){
            $dt             = ($request->dt);
            $orders         = DB::table('orders')
                                    ->orderBy('customers.name','ASC')
                                    // ->orderBy('orders.customer_id','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select(
                                                'orders.id',
                                                'orders.ref_order_id',
                                                'orders.order_note',
                                                'orders.polybags_printed',
                                                'orders.customer_id',
                                                'customers.name as customer_name',
                                                'customers.contact_no as customer_contact_no',
                                                // 'orders.pickup_date',
                                                // 'orders.delivery_date',
                                                DB::raw('DATE_FORMAT(orders.pickup_date, "%Y/%m/%d") as pickup_date'),
                                                DB::raw('DATE_FORMAT(orders.delivery_date, "%Y/%m/%d") as delivery_date'),
                                                'orders.status_id',
                                                DB::raw('(CASE 
                                                    WHEN isNULL(orders.ref_order_id) THEN "REG" 
                                                    ELSE "HFQ"
                                                    END) AS order_type'
                                                ),
                                                DB::raw('
                                                (CASE 
                                                    WHEN (status_id = "1" ) THEN orders.pickup_timeslot_id 
                                                    ELSE orders.delivery_timeslot_id
                                                END) AS timeslot_id'
                                                )
                                            )
                                  
                                    // ->where(function ($query) use ($dt){
                                    //     $query->WhereDate('orders.pickup_date',$dt)
                                    //         ->whereNull('orders.tags_printed');
                                    // })
                                    // ->orWhere(function($query) use ($dt){
                                    //         $query->WhereDate('orders.delivery_date',$dt)
                                    //             ->whereNotNull('orders.tags_printed');
                                    // })
                                   
                                    ->whereNotIn('orders.id', function($q){
                                        $q->select('order_id')->from('route_plans')
                                        // ->where('route_plans.schedule', '=' ,1)
                                        ->where('route_plans.complete', '=' ,0)
                                        ->orWhere('route_plans.is_move_to_hub', '=' ,0);
                                    })
                                    ->Where(function($query) use ($dt){
                                        $query->whereDate('orders.pickup_date',$dt)
                                            ->OrWhereDate('orders.delivery_date',$dt);
                                    })
                                    ->Where(function($query) use ($dt){
                                        $query->whereNotIn('orders.status_id2',[1,7,8,9,10,16])
                                                ->whereNotIn('orders.status_id',[15,17])
                                                ->orWhereNull('orders.status_id2')
                                                ->whereIn('orders.status_id', [1,2,3]);
                                    })

                                    ->whereNotIn('orders.id', function($q) use ($dt){
                                        $q->select('id')->from('orders')
                                            ->whereDate('orders.pickup_date',$dt)
                                            ->whereIn('orders.status_id',[4]);
                                    })


                                    ->get();

            if($orders){

                $statuses       = DB::table('statuses')
                                    ->where('statuses.id', 1)
                                    // ->orWhere('statuses.id', 3)
                                    ->pluck('name','id')
                                    ->all();

                $areas          = DB::table('areas')
                                    ->pluck('center_points','id')
                                    ->all();
                // all statuses
                $all_statuses   = DB::table('statuses')
                                    ->whereIn('statuses.id', [1,2,3])
                                    ->pluck('name','id')
                                    ->all();

                // custom statuses
                $cus_statuses   = DB::table('statuses')  
                                    ->whereIn('statuses.id', [2,3])
                                    ->pluck('name','id')
                                    ->all();

                $holidays       =  DB::table('holidays')
                                        ->select(
                                            // 'holiday_date',
                                        DB::raw('DATE_FORMAT(holidays.holiday_date, "%Y/%m/%d") as holiday_date')
                                        )
                                        ->get();

                $time_slots     = DB::table('time_slots')
                                    ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                    ->pluck('name','id')
                                    ->all();


                $details = view('csr_dashboards.reg_order_table',
                                    compact( 
                                                'orders',
                                                'statuses',
                                                'holidays',
                                                'time_slots',
                                                'cus_statuses',
                                                'all_statuses',
                                                'dt'
                                                ))
                                    ->render();
                return response()->json(['data'=>$orders,'details'=>$details,'dt'=>$dt]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
            
        }

    }

    public function fetch_hfq_orders(Request $request)
    {
        if($request->ajax()){
            $dt             = $request->dt;
            $orders         = DB::table('orders')
                                    // ->orderBy('orders.id','ASC')
                                    ->orderBy('customers.name','ASC')
                                    // ->orderBy('orders.customer_id','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select(
                                                'orders.id',
                                                'orders.order_note',
                                                'orders.polybags_printed',
                                                'orders.customer_id',
                                                'customers.name as customer_name',
                                                'customers.contact_no as customer_contact_no',
                                                // 'orders.pickup_date',
                                                // 'orders.delivery_date',
                                                
                                                DB::raw('DATE_FORMAT(orders.pickup_date, "%Y/%m/%d") as pickup_date'),
                                                DB::raw('DATE_FORMAT(orders.delivery_date, "%Y/%m/%d") as delivery_date'),
                                                'orders.status_id',
                                                DB::raw('(CASE 
                                                    WHEN isNULL(orders.ref_order_id) THEN "REG" 
                                                    ELSE "HFQ"
                                                    END) AS order_type'
                                                ),
                                                DB::raw('
                                                (CASE 
                                                    WHEN status_id = "1" THEN orders.pickup_timeslot_id 
                                                    ELSE orders.delivery_timeslot_id
                                                END) AS timeslot_id'
                                                )
                                            )
                                    ->whereNotIn('orders.id', function($q){
                                        $q->select('order_id')->from('route_plans')
                                        // ->where('route_plans.schedule', '=' ,1)
                                        ->where('route_plans.complete', '=' ,0);
                                    })
                                    // ->Where(function($query) use ($dt){
                                    //     $query->whereDate('orders.pickup_date',$dt)
                                    //         ->OrWhereDate('orders.delivery_date',$dt);
                                    // })
                                    ->whereNotNull('orders.ref_order_id')
                                    ->where('orders.status_id','=',17)
                                    ->get();

            if($orders){
                $statuses       = DB::table('statuses')
                                    ->where('statuses.id', 1)
                                    // ->orWhere('statuses.id', 3)
                                    ->pluck('name','id')
                                    ->all();

                $areas          = DB::table('areas')
                                    ->pluck('center_points','id')
                                    ->all();
                    // all statuses
                $all_statuses   = DB::table('statuses')
                                    ->whereIn('statuses.id', [1,2,3])
                                    ->pluck('name','id')
                                    ->all();

                    // custom statuses
                $cus_statuses   = DB::table('statuses')  
                                    ->whereIn('statuses.id', [2,3])
                                    ->pluck('name','id')
                                    ->all();

                $holidays       =  DB::table('holidays')
                                        ->select(
                                                    // 'holiday_date'
                                                    DB::raw('DATE_FORMAT(holidays.holiday_date, "%Y/%m/%d") as holiday_date')
                                                )
                                        ->get();

                $time_slots     = DB::table('time_slots')
                                    ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                    ->pluck('name','id')
                                    ->all();


                $details = view('csr_dashboards.hfq_order_table',
                                    compact( 
                                                'orders',
                                                'statuses',
                                                'holidays',
                                                'time_slots',
                                                'cus_statuses',
                                                'all_statuses',
                                                'dt'
                                                ))
                                    ->render();
                return response()->json(['data'=>$orders,'details'=>$details,'dt'=>$dt]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
            
        }

    }

    public function fetch_cancel_orders(Request $request)
    {
        if($request->ajax()){
            $dt             = $request->dt;
            $orders         = DB::table('orders')
                                    ->orderBy('customers.name','ASC')
                                    // ->orderBy('orders.id','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select(
                                                'orders.id',
                                                'orders.order_note',
                                                'orders.polybags_printed',
                                                'orders.customer_id',
                                                'customers.name as customer_name',
                                                'customers.contact_no as customer_contact_no',
                                                // 'orders.pickup_date',
                                                // 'orders.delivery_date',
                                                
                                                DB::raw('DATE_FORMAT(orders.pickup_date, "%Y/%m/%d") as pickup_date'),
                                                DB::raw('DATE_FORMAT(orders.delivery_date, "%Y/%m/%d") as delivery_date'),
                                                'orders.status_id',
                                                DB::raw('
                                                (CASE 
                                                    WHEN status_id = "1" THEN orders.pickup_timeslot_id 
                                                    ELSE orders.delivery_timeslot_id
                                                END) AS timeslot_id')
                                            )
         
                                    ->whereNotIn('orders.id', function($q){
                                        $q->select('order_id')->from('route_plans')
                                        // ->where('route_plans.schedule', '=' ,1)
                                        ->where('route_plans.complete', '=' ,0);
                                    })
                                    // ->Where(function($query) use ($dt){
                                    //     $query->whereDate('orders.pickup_date',$dt)
                                    //         ->OrWhereDate('orders.delivery_date',$dt);
                                    // })
                                    ->Where(function($query) use ($dt){
                                        $query->where('orders.status_id2','=', 16);
                                            // ->whereIn('orders.status_id', [1,2,3]);
                                    })
                                    ->get();

            if($orders){
               
                // all statuses
                $all_statuses   = DB::table('statuses')
                                    ->whereIn('statuses.id', [1,2,3])
                                    ->pluck('name','id')
                                    ->all();

                $cus_statuses   = DB::table('statuses')  
                                    ->whereIn('statuses.id', [2,3])
                                    ->pluck('name','id')
                                    ->all();

                $statuses       = DB::table('statuses')
                                    ->whereIn('statuses.id', [1,2,3])
                                    ->pluck('name','id')
                                    ->all();


                $holidays       =  DB::table('holidays')
                                    ->select(
                                                // 'holiday_date'
                                                DB::raw('DATE_FORMAT(holidays.holiday_date, "%Y/%m/%d") as holiday_date')
                                            )
                                    ->get();

                $time_slots     = DB::table('time_slots')
                                    ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                    ->pluck('name','id')
                                    ->all();

                $details = view('csr_dashboards.cancel_order_table',
                                    compact( 
                                                'holidays',
                                                'orders',
                                                'time_slots',
                                                'cus_statuses',
                                                'all_statuses',
                                                'statuses',
                                                'dt'
                                                ))
                                    ->render();
                return response()->json(['data'=>$orders,'details'=>$details,'dt'=>$dt]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
            
        }

    }

    public function create_cstmr_arry($orders){
     
        $all_customers          = array();
        $customers              = array();

        // fetching out distint customer_id
        foreach ($orders as $key => $value) {
            // if((count($all_customers)) > 0){
            //     foreach ($all_customers as $k => $v) {
            //         if($value->customer_id == $v ){
            //             continue;
            //         }
            //         array_push($all_customers,$value->customer_id);
            //     }
            // }else{
                array_push($all_customers,$value->customer_id);
            // }
        }
    
        // Assigning 0 to customer_id
        foreach ($all_customers as $key => $value) {
            $customers[$value]  = 0;
        }
     
        return $customers;
    }
    public function is_cstmr_exist($customers, $customer_id){
        foreach ($customers as $key => $value) {
            if(($key ==  $customer_id) &&  ($value != 0)){
                return false;

            }
        }
        // customer_id is working as a key 
        $customers[$customer_id] = 1;
        return true;
    }


    public function fetch_summary_orders(Request $request)
    {
        if($request->ajax()){
            // $dt             = "2021-03-16";
            $dt             = $request->dt;
            // $cus_state      = array('3' , '2' , '4' , '1');
            $orders         = DB::table('orders')
                                // ->orderBy('orders.id','ASC')
                                ->orderBy('orders.customer_id','DESC')
                                ->orderBy('orders.status_id','DESC')
                                // ->orderByRaw("FIELD(clan_rank , 'Owner', 'Admin', 'Member') ASC");
                                // ->orderByRaw(DB::raw('FIELD(orders.status_id, ":values")', ['values' => $cus_state]))
                                // ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                ->select(
                                            'orders.id',
                                            // 'orders.ref_order_id',
                                            'orders.customer_id',
                                            'orders.pickup_date',
                                            'orders.delivery_date',
                                            'orders.status_id',
                                            'orders.pickup_timeslot_id',
                                            'orders.delivery_timeslot_id',
                                            // DB::raw('(CASE 
                                            //     WHEN isNULL(orders.ref_order_id) THEN "REG" 
                                            //     ELSE "HFQ"
                                            //     END) AS order_type'
                                            // ),
                                            
                                        )
                                // ->Where(function($query) use ($dt){
                                //     $query->WhereDate('orders.pickup_date',$dt)
                                //             ->whereNull('orders.tags_printed');
                                // })
                                ->whereNotIn('orders.id', function($q){
                                    $q->select('order_id')->from('route_plans')
                                        // ->where('route_plans.schedule', '=' ,1)
                                        ->where('route_plans.complete', '=' ,0)
                                        ->orWhere('route_plans.is_move_to_hub', '=' ,0);
                                })
                                ->Where(function($query) use ($dt){
                                    $query->whereDate('orders.pickup_date',$dt)
                                        ->OrWhereDate('orders.delivery_date',$dt);
                                })
                                
                                ->Where(function($query) use ($dt){
                                     $query->whereNotIn('orders.status_id2',[1,7,8,9,10,16])
                                                ->whereNotIn('orders.status_id',[15,17])
                                                ->orWhereNull('orders.status_id2')
                                                ->whereIn('orders.status_id', [1,2,3]);
                                                
                                                
                                    // $query->where('orders.status_id2','!=', 16)
                                    //         ->where('orders.status_id','!=', 17)
                                    //         ->where('orders.status_id','!=', 15)
                                    //         ->where('orders.status_id2', '!=' ,7)
                                    //         ->orWhereNull('orders.status_id2')
                                    //         ->whereIn('orders.status_id', [1,2,3]);
                                    // $query->where('orders.status_id2','!=', 16)
                                    //     ->orWhereNull('orders.status_id2');
                                        // ->whereIn('orders.status_id', [1,2,3]);
                                })
                                ->whereNotIn('orders.id', function($q) use ($dt){
                                    $q->select('id')->from('orders')
                                        ->whereDate('orders.pickup_date',$dt)
                                        ->whereIn('orders.status_id',[4]);
                                })
                                // ->whereIn('orders.status_id', [1,2,3,4])
                               
                                ->get();

            // create an array or customer having customer_id as key and initilize every key with 0;
            $customers      = $this->create_cstmr_arry($orders);

            
          
            if($orders){
                $statuses           = DB::table('statuses')
                                        ->orderBy('statuses.id','ASC')
                                        ->select(
                                                    'statuses.id',
                                                    'statuses.name'
                                                )
                                                
                                        ->whereIn('statuses.id', [1,2,3])
                                        ->get();
                                        // dd($statuses);

                $timeslots          = DB::table('time_slots')
                                        ->orderBy('time_slots.id','ASC')
                                        ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                        ->get();
                    

                $sts                = array();
                $tms                = array();
                $tms[0]             = 0;
                foreach ($statuses as $key => $value) {
                    $sts[$value->id] = 0;
                } 
                foreach ($timeslots as $key => $value) {
                    $tms[$value->id] = 0;
                }
               
                foreach ($orders as $key => $value) {
                    $chk    = $this->is_cstmr_exist($customers, $value->customer_id);
                    if($chk){
                        $customers[$value->customer_id] = 1;
                        if(($value->status_id) != 1){
                            if(isset($value->delivery_timeslot_id)){
                        
                                
                                    $tms[$value->delivery_timeslot_id]++; // 2 or 3
                            
                            }else{
                                $tms[0]++;     // 0: Non-selected Timeslot
                            }
                            
                            if((isset($value->status_id)) &&  ((($value->status_id) == 1) || (($value->status_id) == 4))){
                                $sts[2] = (($sts[2]) + 1); //inc "drop off"
                            }else{
                                $sts[$value->status_id]++; 
                                // echo "id: $value->status_id ";
                                // $sts[$value->status_id]; // pick  Or pick& drop
                            }
                        }else{
                            if(isset($value->pickup_timeslot_id)){
                                if((($value->status_id) == 4) ){
                                    if(isset($value->delivery_timeslot_id)){
                                        $tms[$value->delivery_timeslot_id]++;
                                    }else{
                                        // echo "asdfasdf";
                                        $tms[0]++;
                                    }
                                }else{
                                    // dd("asdf");
                                    $tms[$value->pickup_timeslot_id]++;
                                }
                            }else{
                                $tms[0]++;// 0: Non-selected Timeslot
                            }
            
                            if((isset($value->status_id))){
                                if((($value->status_id) == 4) ){
                                    $sts[2]++;
                                }else{
                                    $sts[$value->status_id]++; // pick  Or pick& drop
                                }
                            }
                        }
                    }
                }
                
                $c          = collect(new Time_slot);
                $c->id      = '0';
                $c->name    = 'Non-selected timelsot';
                $timeslots->add($c);
                $details   = view('csr_dashboards.summary_table',
                                        compact( 
                                                'sts',
                                                'tms',
                                                'statuses',
                                                'timeslots',
                                                )
                                        )
                                        ->render();
                return response()->json(['data'=>$orders,'details'=>$details,'dt'=>$dt]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
            
        }

    }

    public function edit_order($id)
    {
        $data              = Order::orderBy('orders.id','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select('orders.*',
                                             
                                            'customers.name as name',
                                            'customers.contact_no')
                                    ->find($id);
                                   
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

        return response()->json($data);
        
    }

     public function show_order($id)
    {
        $data           = Order::orderBy('orders.id','DESC')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->leftjoin('customer_has_addresses', 'customer_has_addresses.id', '=', 'orders.pickup_address_id')
                            ->leftjoin('time_slots', 'time_slots.id', '=', 'orders.pickup_timeslot_id')
                            ->leftjoin('users','users.id','=','orders.DW_who')
                            ->where('orders.id', $id)
                            ->select('orders.*',
                                    'customers.name',
                                    'customers.permanent_note',
                                    'customers.permanent_note',
                                    'customers.contact_no',
                                    'address as address_name',
                                    'orders.waver_delivery',
                                    'orders.DW_who',
                                    'users.name as order_DW_who',
                                    'orders.phase',
                                    DB::raw('CONCAT(time_slots.start_time," - ", time_slots.end_time ) as time_slot_name'))
                            ->first();

        // $histories      = DB::table('order_histories')
        //                     ->leftjoin('statuses', 'statuses.id', '=', 'order_histories.status_id')
        //                     ->leftjoin('users', 'users.id', '=', 'order_histories.created_by')
        //                     ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        //                     ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
        //                     ->where('order_histories.order_id', $id)
        //                     ->select('statuses.id as status_id',
        //                             'statuses.name as status_name',
        //                             'users.name as user_name',
        //                             'roles.name as role_name',
        //                             'order_histories.created_at as created_at')
        //                     ->get()
        //                     ->all();
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
                        // it is remaining

        return view('csr_dashboards.show',
                    compact('data','histories')
                   );
    }

     public function update_order(Request $request)
{
    $id = $request->input('order_id');
    $data = Order::find($id);
    $temp = $request->input('status_id');
    $hub_id = $this->get_hub_id($request->input('area_id'));

    $chk1 = Order::orderBy('orders.id', 'DESC')
        ->select('orders.id')
        ->where('customer_id', $request->input('customer_id'))
        ->where('status_id', 1)
        ->where('orders.id', '!=', $id)
        ->whereDate('pickup_date', $request->input('pickup_date'))
        ->first();

    $chk2 = DB::table('route_plans')
        ->select('route_plans.id')
        ->leftJoin('orders', 'orders.id', '=', 'route_plans.order_id')
        ->where('orders.customer_id', $request->input('customer_id'))
        ->where('orders.status_id', 1)
        ->where('orders.id', '!=', $id)
        ->whereDate('orders.pickup_date', $request->input('pickup_date'))
        ->first();

    if (isset($chk1->id) || isset($chk2->id)) {
        return response()->json(['error' => [0 => "Order of this customer is already in queue on same date"]]);
    }

    if (!isset($hub_id) || $hub_id == 0) {
        return response()->json(['error' => [0 => "Unable to find hub-id for this order"]]);
    }

    if ($data->status_id2 == 16) {
        $request->merge(['status_id2' => $this->get_last_order_status($id)]);
        $var = new Order_history();
        $var->order_id = $id;
        $var->created_by = Auth::user()->id;
        $var->status_id = $request->input('status_id');
        $var->save();
    }

    if ($temp == 16) {
        $request->merge(['status_id' => $data->status_id, 'status_id2' => 16]);
    }

    if ($request->input('status_id') != 1) {
        $request->merge(['pickup_date' => $data->pickup_date]);
    }

    $waver = $request->input('waver_delivery');

    if ($data) {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'cus_address_id' => 'required',
            'pickup_date' => 'required|date',
            'delivery_date' => 'required|date|after:pickup_date',
        ]);

        if ($validator->passes()) {
            $inputs = $request->all();
            $address = $this->get_address($inputs['cus_address_id']);
            $timeslot = $this->get_timeslot($inputs['timeslot_id']);
            $inputs['hub_id'] = $hub_id;
            if ($waver === null) {
                $inputs['waver_delivery'] = 0;
            }

           if($waver  == 1){
                $inputs['phase']  = "Csr dashboard edit";
            $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
             $inputs['DW_when']  = $currentDateTime;
             $inputs['DW_who']= Auth::user()->id;
           }

            if ($temp == 16) {
                $inputs['status_id'] = $data->status_id;
                $inputs['status_id2'] = 16;

                // storing history of cancellation of order
                $var = new Order_history();
                $var->order_id = $id;
                $var->created_by = Auth::user()->id;
                $var->status_id = 16;
                $var->save();
            }

            if ($inputs['status_id'] == 1) {
                // for rescheduling: changed to null
                if ($temp == 16) {
                    $inputs['pickup_timeslot_id'] = null;
                    $inputs['pickup_timeslot'] = null;
                } else {
                    $inputs['pickup_timeslot_id'] = $inputs['timeslot_id'];
                    $inputs['pickup_timeslot'] = $timeslot;
                }
                $inputs['pickup_address_id'] = $inputs['cus_address_id'];
                $inputs['pickup_address'] = $address;
            } else {
                // for rescheduling: changed to null
                if ($temp == 16) {
                    $inputs['delivery_timeslot_id'] = null;
                    $inputs['delivery_timeslot'] = null;
                } else {
                    $inputs['delivery_timeslot_id'] = $inputs['timeslot_id'];
                    $inputs['delivery_timeslot'] = $timeslot;
                }

                $inputs['delivery_address_id'] = $inputs['cus_address_id'];
                $inputs['delivery_address'] = $address;
            }

            $upd = $data->update($inputs);
            return response()->json(['success' => 'Order of ' . $request->input('name') . ' added successfully.']);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }

    return response()->json(['error' => [0 => "Order of this customer is already in queue on same date"]]);
}

    
    public function delete_order($id)
    { 
         // Send pickup cancel sms
        (new NotificationController)->csr_cancel_pickup($id);

        DB::table("complaints")->where('order_id', '=', $id)->delete();
        DB::table("order_histories")->where('order_id', '=', $id)->delete();
        DB::table("order_has_addons")->where('order_id', '=', $id)->delete();
        DB::table("order_has_items")->where('order_id', '=', $id)->delete();
        DB::table("order_has_services")->where('order_id', '=', $id)->delete();
        DB::table("order_has_bags")->where('order_id', '=', $id)->delete();
        DB::table("order_has_tags")->where('order_id', '=', $id)->delete();

        $data = DB::table("orders")->whereIn('id',explode(",",$id))->delete();
        return response()->json(['success'=>$data." Order deleted successfully."]);
    }

    public function reschedule_reg_orders(Request $request){

        // Checking:: same customer must have same timeslots
        if(isset($request['customer_id'] )){
            foreach($request['customer_id'] as $key => $outer_id){ 
                foreach($request['customer_id'] as $k => $inner_id){
                    if( $outer_id  == $inner_id){
                        if($request['timeslot_id'][$key] != $request['timeslot_id'][$k]){
                            $customer   = DB::table('customers')
                                            ->select('customers.name')
                                            ->where('customers.id', $request['customer_id'][$k])
                                            ->first();
                            if(isset($customer)){
                                return response()->json(['error'=>[0=>$customer->name . " has different timeslots"]]);
                            }else{
                                return response()->json(['error'=>[0=>"Something went wrong!!!!"]]);
                            }
                        }
                        if($request['id'][$key] != $request['id'][$k]){
                            if(($request['status_id'][$key] == 3) && ($request['status_id'][$k] == 3)){
                                $customer   = DB::table('customers')
                                                ->select('customers.name')
                                                ->where('customers.id', $request['customer_id'][$k])
                                                ->first();
                                if(isset($customer)){
                                    return response()->json(['error'=>[0=>$customer->name . " has more than 1 pick & drop"]]);
                                }else{
                                    return response()->json(['error'=>[0=>"Something went wrong!!!!"]]);
                                }
                            }
                        }
                        
                    }
                }  
            }
        }
     
       

        if(isset($request['re_schedule'])){
            $validator = Validator::make($request->all(), [
                'id'                    => 'required|array',
                'id.*'                  => 'required|distinct', 
                'pickup_date'           => 'required|array',
                'pickup_date.*'         => 'required|date',
                'delivery_date'         => 'required|array',
                'delivery_date.*'       => 'required|date|after:pickup_date.*',
                'timeslot_id'           => 'required|array',
                'timeslot_id.*'         => 'required|numeric',
                // 'timeslot_id.*'         => 'required|min:1|numeric',
                
            ],
            [
                // 'timeslot_id.*.min'     => 'Please select timeslots of yellow orders',
                'delivery_date.*.after' => "The delivery must be after pickup date"
            ]
        );

    
        if ($validator->passes()) {
            // BEGIN:: Validating the orders that has same customer already order in queue 
            if(isset($request['id'] )){
            
                foreach ($request['id'] as $key => $id) {
                    $rec1               = Order::select('orders.customer_id','orders.status_id')->find($id);
                    $rec2               = Order::select('orders.id','orders.status_id','customers.name as cus_name')
                                            ->leftjoin('customers', 'orders.customer_id', '=', 'customers.id')
                                            ->where('orders.id','!=',$id)
                                            ->where('customer_id',($rec1->customer_id))
                                            ->whereDate('pickup_date',$request['pickup_date'][$key])
                                            // ->orWhereDate('delivery_date',$request['pickup_date'][$key])
                                            ->get();
                        
                    if( (!($rec2->isEmpty())) && (isset($rec1->status_id)) ){
                        foreach($rec2 as $k => $value) {
                            if( ((isset($rec1->status_id)) && (($rec1->status_id) == 1)) && ((isset($value['status_id'])) && (($value['status_id']) == 1)) ){
                                return response()->json(['error'=>[0=>"Order (Pickup) of " .$value['cus_name']. " customer is already in queue on " .($request['pickup_date'][$key]) ]]);
                            }
                            else if( ((isset($rec1->status_id)) && (($rec1->status_id) == 1)) && ((isset($value['status_id'])) && (($value['status_id']) == 3)) ){
                                return response()->json(['error'=>[0=>"Order (Pick & Drop) of " .$value['cus_name']. " customer is already in queue on " .($request['pickup_date'][$key]) ]]);
                            }
                           
                            else if( ((isset($request['status_id'][$key])) && (($request['status_id'][$key]) == 3)) && ((isset($value['status_id'])) && (($value['status_id']) == 3)) ){
                                return response()->json(['error'=>[0=>"Order (Pick & Drop) of " .$value['cus_name']. " customer is already in queue on " .($request['pickup_date'][$key])." , you can not add two (pick and drop)" ]]);
                            }
                            else if( ((isset($rec1->status_id)) && (($rec1->status_id) == 1)) && ((isset($value['status_id'])) && (($value['status_id']) == 2)) ){
                                return response()->json(['error'=>[0=>"Order (Drop off) of " .$value['cus_name']. " customer is already in queue on ".($request['pickup_date'][$key]).", Please change status from drop to pick and drop " ]]);
                            }
                        }
                    }
                }
            }
            // END:: Validating the orders that has same customer already order in queue 
            
            foreach ($request['id'] as $key => $id) {
                $data                               = Order::find($id);
                if($data){
                    $req['id']                      = $id;
                    $req['pickup_date']             = $request['pickup_date'][$key];
                    $req['status_id']               = $request['status_id'][$key];

                     // reschedule if date dates are not same and put history
                    if($req['status_id'] != 1) {
                        if(  $data['delivery_date']   != $request['delivery_date'][$key] ){
                            // storing history of reschedule of order
                            $var                    = new Order_history();
                            $var->order_id          = $id;
                            $var->created_by        = Auth::user()->id;
                            $var->status_id         = 5;
                            $var->save();
                        }
                    }

                    $req['delivery_date']           = $request['delivery_date'][$key];
                    
                    // if timeslot is set then schedule them otherwise not.
                    if(($request['timeslot_id'][$key]) == 0 ){
                        continue;
                    }
                    
                    $timeslot                       = $this->get_timeslot($request['timeslot_id'][$key]);
                    // dd($timeslot);
                   
                  
                    if($req['status_id'] == 1){
                        $req['pickup_timeslot']     = $timeslot; 
                        $req['pickup_timeslot_id']  = $request['timeslot_id'][$key];
                    }else{
                    
                        $req['delivery_address_id'] = $data['pickup_address_id']; 
                        $req['delivery_address']    = $data['pickup_address']; 
                        $req['delivery_timeslot']   = $timeslot; 
                        $req['delivery_timeslot_id']= $request['timeslot_id'][$key];
                        
                    }
                   
                    $upd        = $data->update($req);

                }else{
                    return response()->json(['error'=>[0=>"No record found!"]]);
                }
                
            }

            return response()->json(['success'=>'All orders have been re-scheduled successfully.']);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
            
        }
    }

    public function has_order_hanger($order_id)
    {
        $data           =  DB::table('order_has_services')
                            ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                            ->where('order_has_services.order_id',$order_id)
                            ->where('services.hanger',1)
                            ->select('services.hanger')
                            ->orderBy('order_has_services.order_number','ASC')
                            ->first();

        if(isset($data->hanger)){
            return 1;       //if order has hanger
        }else{
            return 0;       //if order has no hanger
        }
    }

    public function get_zone_id($area_id)
    {
        $zone       = Zone_has_area::select('zone_has_areas.zone_id')
                        ->where('zone_has_areas.area_id',$area_id)
                        ->first();
        if( isset($zone->zone_id) ){
            return $zone->zone_id;
        }else{
            return 0;
        }
       
    }

    public function get_order_weight($order_id){
        $data           =  DB::table('order_has_services')
                            ->where('order_has_services.order_id',$order_id)
                            ->sum('order_has_services.weight');
        return $data;        
    }

    public function finalize_orders(Request $request){

        if(isset($request['re_schedule'])){
            $validator = Validator::make($request->all(), [
                'id'                    => 'required|array',
                'id.*'                  => 'required|distinct', 
                'pickup_date'           => 'required|array',
                'pickup_date.*'         => 'required|date',
                'delivery_date'         => 'required|array',
                'delivery_date.*'       => 'required|date|after:pickup_date.*',
                'timeslot_id'           => 'required|array',
                'timeslot_id.*'         => 'required|min:1|numeric',
                
            ],
            [
                'id.required'           => 'No orders found!!!!!',
                'timeslot_id.*.min'     => 'Please select timeslots of yellow orders'
            ]
        );

    
        if ($validator->passes()) {

             // BEGIN:: Past and Future orders can not be finalize in present
                if( (isset($request['dt']))){
                    $rdate  = $request['dt'];                                // rdtate  = rquest date
                    $tdate  = $this->today;                                 //  tdate   =  today
                    $tmrw   = date('Y-m-d', strtotime($tdate . ' +1 day')); //  tmrw    = tomorrow


                    // if( (($rdate) != ($this->today) )  || ( ($rdate) != ($this->today) ) ){
                    // if( (($tday) < ($this->today) )  && ( ($tmrw) > ($this->today) ) ){
                    if( ( ($rdate) > ($tmrw) ) ){
                        return response()->json(['error'=>[0=>"Orders after tomorrow can not be finalized in present"]]);
                    }else  if( ( ($rdate) < ($tdate) ) ){
                        return response()->json(['error'=>[0=>"Orders before today can not be finalized in present"]]);
                    }
                }else{
                    return response()->json(['error'=>[0=>"Something went wrong with Plan Date!!!!"]]);
                }
            // END:: Past and Future orders can not be finalize in present

            
            // BEGIN::Checking same customer must have same timeslots
                if(isset($request['customer_id'] )){
                    foreach($request['customer_id'] as $key => $outer_id){
                        foreach($request['customer_id'] as $k => $inner_id){
                            if( $outer_id  == $inner_id){
                                if($request['timeslot_id'][$key] != $request['timeslot_id'][$k]){
                                    $customer   = DB::table('customers')
                                                    ->select('customers.name')
                                                    ->where('customers.id', $request['customer_id'][$k])
                                                    ->first();
                                    if(isset($customer)){
                                        return response()->json(['error'=>[0=>$customer->name . " has different timeslots"]]);
                                    }else{
                                        return response()->json(['error'=>[0=>"Something went wrong!!!!"]]);
                                    }
                                }
                            }
                        }  
                    }
                }else{
                    return response()->json(['error'=>[0=>"Customer not found!!!!"]]);
                }
            // END::Checking same customer must have same timeslots

            // BEGIN:: Validating the orders that has same customer already order in queue 
                if(isset($request['id'] )){
                    
                    foreach ($request['id'] as $key => $id) {
                        $rec1               = Order::select('orders.customer_id','orders.status_id')->find($id);
                        $rec2               = Order::select('orders.id','orders.status_id','customers.name as customer_name')
                                                ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                                ->where('orders.id','!=',$id)
                                                ->where('customer_id',($rec1->customer_id))
                                                ->whereDate('pickup_date',$request['pickup_date'][$key])
                                                // ->orWhereDate('delivery_date',$request['pickup_date'][$key])
                                                ->get();
                            
                        if( (!($rec2->isEmpty())) && (isset($rec1->status_id)) ){
                            foreach($rec2 as $k => $value) {
                                if( ((isset($rec1->status_id)) && (($rec1->status_id) == 1)) && ((isset($value['status_id'])) && (($value['status_id']) == 1)) ){
                                    return response()->json(['error'=>[0=>"Order (Pickup) of ".$value['customer_name']." is already in queue on " .($request['pickup_date'][$key]) ]]);
                                }
                                else if( ((isset($rec1->status_id)) && (($rec1->status_id) == 1)) && ((isset($value['status_id'])) && (($value['status_id']) == 3)) ){
                                    return response()->json(['error'=>[0=>"Order (Pick & Drop) of ".$value['customer_name']." is already in queue on " .($request['delivery_date'][$key]) ]]);
                                }
                            
                                else if( ((isset($request['status_id'][$key])) && (($request['status_id'][$key]) == 3)) && ((isset($value['status_id'])) && (($value['status_id']) == 3)) ){
                                    return response()->json(['error'=>[0=>"Order (Pick & Drop) of ".$value['customer_name']." is already in queue on " .($request['delivery_date'][$key])." , you can not add two (pick and drop)" ]]);
                                }
                                else if( ((isset($rec1->status_id)) && (($rec1->status_id) == 1)) && ((isset($value['status_id'])) && (($value['status_id']) == 2)) ){
                                    return response()->json(['error'=>[0=>"Order (Drop off) of this customer is already in queue on ".($request['delivery_date'][$key]).", Please change status from drop to pick and drop " ]]);
                                }
                            }
                        }
                    }
                }else{
                    return response()->json(['error'=>[0=>"Orders not found!!!!"]]);
                }
            // END:: Validating the orders that has same customer already order in queue 

            // BEGIN::Update Order data 
                foreach ($request['id'] as $key => $id) {
                    $req = array();
                    $data                               = Order::find($id);
                    if($data){
                        $req['id']                      = $id;
                        $req['pickup_date']             = $request['pickup_date'][$key];
                        $req['delivery_date']           = $request['delivery_date'][$key];
                        $req['status_id']               = $request['status_id'][$key];

                        $timeslot                       = $this->get_timeslot($request['timeslot_id'][$key]);
                        if($req['status_id'] == 1){
                            $req['pickup_timeslot']     = $timeslot; 
                            $req['pickup_timeslot_id']  = $request['timeslot_id'][$key];
                        }else{
                            $req['delivery_address_id'] = $data['pickup_address_id']; 
                            $req['delivery_address']    = $data['pickup_address']; 
                            $req['delivery_timeslot']   = $timeslot; 
                            $req['delivery_timeslot_id']= $request['timeslot_id'][$key];
                        }
                        $upd        = $data->update($req);

                    }else{
                        return response()->json(['error'=>[0=>"No record found!"]]);
                    }
                    
                }
            // END::Update Order data

            // BEGIN::storing orders in route plan table
                foreach ($request['id'] as $key => $id) {
                    $data                               = Order::find($id);
                    if($data){

                        $req['order_id']                = $id;
                        $req['area_id']                 = $data['area_id'];
                        $req['hub_id']                  = $data['hub_id'];
                        $req['zone_id']                 = $this->get_zone_id($data['area_id']);
                        $req['hanger']                  = $this->has_order_hanger($id);
                        $req['weight']                  = $this->get_order_weight($id);
                        $req['status_id']               = $request['status_id'][$key];
                        $req['timeslot_id']             = $request['timeslot_id'][$key];

                        if($req['status_id'] == 1){
                            $req['address_id']          = $data['pickup_address_id'];
                            // BEGIN :: Delete details if it is pickup   
                            DB::table("order_has_addons")->where('order_id', '=', $id)->delete();
                            DB::table("order_has_items")->where('order_id', '=', $id)->delete();
                            DB::table("order_has_services")->where('order_id', '=', $id)->delete();
                            // END :: Delete details if it is pickup 
                        }else{

                             // BEGIN::Insert order status (Drop off / Pick & Drop) in Order_history table//
                                $val                    = new Order_history();
                                $val->order_id          = $id;
                                $val->created_by        = Auth::user()->id;
                                $val->status_id         = $req['status_id'];
                                $val->save();
                            // BEGIN::Insert order status (Drop off / Pick & Drop) in Order_history table//

                            if(isset($data['delivery_address_id'])){
                                $req['address_id']      = $data['delivery_address_id'];
                            }else{
                                $req['address_id']      = $data['pickup_address_id'];
                            }
                        }
                        $chk                            = DB::table('route_plans')
                                                            ->select('route_plans.id')
                                                            ->where('route_plans.order_id', $req['order_id'])
                                                            ->where('route_plans.status_id', $req['status_id'])
                                                            ->where('route_plans.schedule', 0)
                                                            ->first();
                                                            
                        $req['created_at']              = date($rdate .' '."H:i:s" );
                        $req['updated_at']              = date($rdate .' '."H:i:s" );                               

                        if(!(isset($chk))){
                            $rec                        = Route_plan::create($req);
                        }else{
                            DB::table("route_plans")
                                ->where('route_plans.order_id', $req['order_id'])
                                ->where('route_plans.status_id', $req['status_id'])
                                ->where('route_plans.schedule', 0)
                                ->delete();
                            $rec                        = Route_plan::create($req);
                        }

                    }else{
                        return response()->json(['error'=>[0=>"No record found!"]]);
                    }
                }
            // END::storing orders in route plan table

            // BEGIN::Sending the msg to clients if messages are allowed 
                foreach ($request['id'] as $key => $order_id) {
                    if($request['status_id'][$key] == 1){
                        // Send pickup sms
                        (new NotificationController)->pickup_msg($order_id);
                    }else if($request['status_id'][$key] == 2){
                        // Send drop sms
                        (new NotificationController)->drop_msg($order_id);
                    }else{
                        // Send pick and drop off sms
                        (new NotificationController)->pick_n_drop_msg($order_id);
                    }
                }
            // END::Sending the msg to clients if messages are allowed 
            
            return response()->json(['success'=>'All orders have been finalized successfully.']);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
            
        }
    }

    public function reschedule_hfq_orders(Request $request){

       
        // BEGIN:: validation: Does orders are selected, is timeslot and (pickup & delivery dates are correct);
        if(isset($request['row_id']) ){
            foreach ($request['row_id'] as $key => $i) {
                $id             = $request['id'][$key];

                // Checking:: is there any Pick & drop on this day 
                if(($request['status_id'][$key]) == 3){
                    $ord        = DB::table('orders')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select('orders.id','customers.name as customer_name')
                                    ->where('orders.status_id', 3) 
                                    ->where('orders.customer_id', ($request['customer_id'][$key])) 
                                    ->whereDate('orders.delivery_date',($request['delivery_date'][$key]))  
                                    ->first();

                    if(isset($ord->id)){
                        return response()->json(['error'=>[0=>"Order No: ". $id .", cannot be scheduled for (pick & drop) because ".($ord->customer_name)." has already (pick & drop) order in regular orders "]]);
                    }
                }
               
                if( (($request['pickup_date'][$key]) >= ($request['delivery_date'][$key]))  ){
                    return response()->json(['error'=>[0=>"Order No: ". $id .", delivery date must be date after pickup date! "]]);
                }
                if(($request['timeslot_id'][$key]) == 0){
                    return response()->json(['error'=>[0=>"Order No: ". $id .", timeslot is not correct! "]]);
                }
                
            }
        }else{
             return response()->json(['error'=>[0=>"Please select at least one order "]]);
        }
        // END:: validation: Does orders are selected, is timeslot and (pickup & delivery dates are correct);

        // Checking:: same customer must have same timeslots
        if(isset($request['customer_id'] )){
            foreach($request['row_id'] as $key => $od){
                $outer_id           = $request['customer_id'][$key];
                
                
                foreach($request['row_id'] as $k => $ind){
                    $inner_id       = $request['customer_id'][$k];
                    
                    if( $outer_id  == $inner_id){
                        if($request['timeslot_id'][$key] != $request['timeslot_id'][$k]){
                            $customer   = DB::table('customers')
                                            ->select('customers.name')
                                            ->where('customers.id', $outer_id)
                                            ->first();
                            if(isset($customer->name)){
                                return response()->json(['error'=>[0=>$customer->name . " has different timeslots"]]);
                            }else{
                                return response()->json(['error'=>[0=>"Something went wrong!!!!"]]);
                            }
                        }
                    }
                }  
            }
        }


       
        if(isset($request['re_schedule'])){
            $validator = Validator::make($request->all(), [
                    'row_id'                => 'required|array',
                    'id'                    => 'required|array',
                    'id.*'                  => 'required|distinct', 
                    // 'pickup_date'           => 'required|array',
                    // 'pickup_date.*'         => 'required|date',
                    // 'delivery_date'         => 'required|array',
                    // 'delivery_date.*'       => 'required|date|after:pickup_date.*',
                ]
                ,
                [
                    'row_id.required'  =>'Please select at least one order'
                ]
            );
        if ($validator->passes()) {
            foreach ($request['row_id'] as $key => $i) {
                $id                                 = $request['id'][$key];
                $data                               = Order::find($id);
                if($data){
                    $req['id']                      = $id;
                    $req['pickup_date']             = $request['pickup_date'][$key];
                    $req['delivery_date']           = $request['delivery_date'][$key];
                    $req['status_id']               = $request['status_id'][$key];
                    $timeslot                       = $this->get_timeslot($request['timeslot_id'][$key]);
                    if($req['status_id'] == 1){
                        $req['pickup_timeslot']     = $timeslot; 
                        $req['pickup_timeslot_id']  = $request['timeslot_id'][$key];
                    }else{
                        $req['delivery_address_id'] = $data['pickup_address_id']; 
                        $req['delivery_address']    = $data['pickup_address']; 
                        $req['delivery_timeslot']   = $timeslot; 
                        $req['delivery_timeslot_id']= $request['timeslot_id'][$key];
                    }
                    $upd        = $data->update($req);
                }else{
                    return response()->json(['error'=>[0=>"No record found!"]]);
                }
            }
            return response()->json(['success'=>'All orders have been re-scheduled successfully.']);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
            
        }

    }

    public function get_last_order_status($order_id){
        $myvar =$order_id;
        $last_status    = DB::table('order_histories')
                            // ->select('order_histories.status_id')
                            ->where('order_histories.order_id',$order_id)
                            
                            ->where('order_histories.status_id','<','15')
                            ->max('order_histories.status_id');
                            // ->where('order_histories.status_id', (DB::raw("SELECT MAX(status_id) FROM `order_histories` where status_id < 15  and order_id = :myvar"), array(
                            //     'myvar' => $myvar
                            //   ))) 
                            // ->setBindings([$order_id])
                            // ->first($last_status);
        //                     dd($last_status);
        // if(isset($last_status->status_id)){
        //    return ($last_status->status_id);
        // }

        if(isset($last_status)){
            return ($last_status);
         }
        // if status not found... then it means that order was cancelled
        return  null;
        
    }
    

    public function reschedule_cancel_orders(Request $request){
       
        if(isset($request['re_schedule'])){
            // BEGIN:: validation: Does orders are selected, is timeslot and (pickup & delivery dates are correct);
            if(isset($request['row_id']) ){
                foreach ($request['row_id'] as $key => $i) {
                    $id         = $request['id'][$key];
             
                    if( (($request['pickup_date'][$key]) >= ($request['delivery_date'][$key]))  ){
                        return response()->json(['error'=>[0=>"Order No: ". $id .", delivery date must be date after pickup date! "]]);
                    }
                    if(($request['timeslot_id'][$key]) == 0){
                        return response()->json(['error'=>[0=>"Order No: ". $id .", timeslot is not correct! "]]);
                    }

                        // BEGIN :: checking is order already picked up; if yes it can not be picked up again 
                        $hstry      = DB::table('order_histories')
                                            // ->leftjoin('orders', 'orders.id', '=', 'order_histories.order_id')
                                            ->select('order_histories.order_id')
                                            ->where('order_histories.order_id', $id)
                                            ->where('order_histories.status_id', 4) // 4: picked up
                                            // ->whereNull('orders.ref_order_id')
                                            ->first();

                        // echo $request['status_id'][$key];
                        
                        if((isset($hstry->order_id)) && ($request['status_id'][$key] == 1)){
                            return response()->json(['error'=>[0=>"Order No: ". $id .", is already picked up! It can only be scheduled to drop off or pickup & drop!"]]);
                        }elseif((isset($hstry->order_id)) && ($request['status_id'][$key] != 1)){
                            // do nothing -- because order is already picked up and can be schdule for drop off and pick and drop
                            // return response()->json(['error'=>[0=>"Order No: ". $id .", is already picked up! It can only be scheduled to drop off or pickup & drop!"]]);
                        }else{
                            $is_ord_hfq     = DB::table('orders')
                                                // ->leftjoin('orders', 'orders.id', '=', 'order_histories.order_id')
                                                ->select('orders.id')
                                                ->where('orders.id', $id)
                                                // ->where('order_histories.status_id', 4) // 4: picked up
                                                ->whereNotNull('orders.ref_order_id')
                                                ->first();

                            if(isset($is_ord_hfq->id)){
                                // do nothing -- because it is HFQ
                            }else{
                                if($request['status_id'][$key] != 1){
                                    return response()->json(['error'=>[0=>"Order No: ". $id .", only be schedule to pickup, because It is not picked up before!"]]);
                                }
                            }

                            
                        }
                    // END :: checking is order already picked up; if yes it can not be picked up again 

                    // BEGIN:: checking is there any Pick & drop on this day of this customer 
                        if(($request['status_id'][$key]) == 3){
                            $ord        = DB::table('orders')
                                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                            ->select('orders.id','customers.name as customer_name')
                                            ->where('orders.status_id', 3) 
                                            ->where('orders.customer_id', ($request['customer_id'][$key])) 
                                            ->whereDate('orders.delivery_date',($request['delivery_date'][$key]))  
                                            ->first();

                            if(isset($ord->id)){
                                return response()->json(['error'=>[0=>"Order No: ". $id .", cannot be scheduled for (pick & drop) because ".($ord->customer_name)." has already (pick & drop) order in regular orders "]]);
                            }
                        }
                    // END:: checking is there any Pick & drop on this day of this customer
                   
                }
            }else{
                 return response()->json(['error'=>[0=>"Please select at least one order "]]);
            }
            // END:: validation: Does orders are selected, is timeslot and (pickup & delivery dates are correct);




            $validator = Validator::make($request->all(), [
                    'row_id'                => 'required|array',
                    'id'                    => 'required|array',
                    'id.*'                  => 'required|distinct', 
                    // 'pickup_date'           => 'required|array',
                    // 'pickup_date.*'         => 'required|date',
                    // 'delivery_date'         => 'required|array',
                    // 'delivery_date.*'       => 'required|date|after:pickup_date.*',
                ]
                ,
                [
                    'row_id.required'  =>'Please select at least one order'
                ]
            ); 
            if ($validator->passes()) {
                foreach ($request['row_id'] as $key => $id) {
                    $id                                 = $request['id'][$key];
                    $data                               = Order::find($id);
                    if($data){
                        $req['id']                      = $id;
                        $req['pickup_date']             = $request['pickup_date'][$key];
                        $req['delivery_date']           = $request['delivery_date'][$key];
                        $req['status_id']               = $request['status_id'][$key];

                        if($req['status_id']  == 1){
                            if($req['pickup_date'] < ($this->today) ){
                                return response()->json(['error'=>[0=>"Order No:". $id ." pickup date is less than the today "]]);
                            }
                        }else{
                            if($req['delivery_date'] < ($this->today) ){
                                return response()->json(['error'=>[0=>"Order No:". $id ." delivery date is less than the today "]]);
                            }
                        }
                       
                        $timeslot                       = $this->get_timeslot($request['timeslot_id'][$key]);
                        if($data->status_id2==16){
                            $req['status_id']           = $request['status_id'][$key];
                            $req['status_id2']          = $this->get_last_order_status($id);

                          
                            

                            // storing history of cancellation of order
                            $var                        = new Order_history();
                            $var->order_id              = $req['id'];
                            $var->created_by            = Auth::user()->id;
                            $var->status_id             = $request['status_id'][$key];
                            $var->save();
                        }

                        if($req['status_id'] == 1){
                            
                            $req['status_id2']          = null; 
                            $req['pickup_timeslot']     = $timeslot; 
                            $req['pickup_timeslot_id']  = $request['timeslot_id'][$key];
                        }else{
                            $req['delivery_address_id'] = $data['pickup_address_id']; 
                            $req['delivery_address']    = $data['pickup_address']; 
                            $req['delivery_timeslot']   = $timeslot; 
                            $req['delivery_timeslot_id']= $request['timeslot_id'][$key];
                        }
                        $upd        = $data->update($req);

                    }else{
                        return response()->json(['error'=>[0=>"No record found!"]]);
                    }
                    
                }


                return response()->json(['success'=>'All cancelled orders have been re-scheduled successfully.']);
            }
            return response()->json(['error'=>$validator->errors()->all()]);
            
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
