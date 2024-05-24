<?php
namespace App\Http\Controllers;

use DB;
use Auth;
use DataTables;
use App\Models\Wash_house_has_hub;
use App\Models\Wash_house_has_order;
// use Carbon\Carbon;
use Validator;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Distribution_hub;

class PackingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:packing-list', ['only' => ['index','show']]);
        $this->middleware('permission:packing-create', ['only' => ['create','store']]);
        $this->middleware('permission:packing-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:packing-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        // $hub_id = 2;
        // dd($dd);
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
        return view('packings.index',
                        compact('hubs')
                    );
    }

    public function find_wash_house($order_id)
    {
        if(isset($order_id)){
            $wash_houses    = Wash_house_has_order::select('wash_house_has_orders.wash_house_id','wash_houses.name as wash_house_name')
                                ->leftjoin('wash_houses', 'wash_houses.id', '=', 'wash_house_has_orders.wash_house_id')
                                ->where('wash_house_has_orders.order_id',$order_id)
                                ->first();

            // $wash_houses    = wash_house_has_hub::select('wash_house_has_hubs.wash_house_id','wash_houses.name as wash_house_name')
            //                     ->leftjoin('wash_houses', 'wash_houses.id', '=', 'wash_house_has_hubs.wash_house_id')
            //                     ->where('wash_house_has_hubs.hub_id',$hub_id)
            //                     ->first();
            if(isset($wash_houses)){            
                return $wash_houses->wash_house_name;
            }else{
                return ;
            }
        }else{
            return ;
        }
    }

    public function fn_count_polybags($order_id)
    {
        if(isset($order_id)){

            $polybags           = DB::table('order_has_bags')
                                        ->where('order_has_bags.order_id', $order_id)
                                        ->count();

            if(!(isset($polybags))){
                $polybags       = 0;
            }
            return $polybags;
        }else{
            return 0;
        }
    }

    public function fn_calc_pickup_weight($order_id){
        if(isset($order_id)){
            $pickup_weight          = DB::table('order_has_services')
                                        ->where('order_has_services.order_id', $order_id)
                                        ->sum('order_has_services.weight');
            if(!(isset($pickup_weight))){
                $pickup_weight      = 0;
            }
            return $pickup_weight;
        }else{
            return 0;
        }
    }

    public function fn_count_tags($order_id)
    {
        if(isset($order_id)){

            $pickup_tags            = DB::table('order_has_items')
                                        ->where('order_has_items.order_id', $order_id)
                                        ->sum('order_has_items.pickup_qty');

            $scan_tags                = DB::table('order_has_items')
                                        ->where('order_has_items.order_id', $order_id)
                                        ->sum('order_has_items.scan_qty');

            $bt_tags                = DB::table('order_has_items')
                                        ->where('order_has_items.order_id', $order_id)
                                        ->sum('order_has_items.bt_qty');

            $nr_tags                = DB::table('order_has_items')
                                        ->where('order_has_items.order_id', $order_id)
                                        ->sum('order_has_items.nr_qty');

            $hfq_tags               = DB::table('order_has_items')
                                        ->where('order_has_items.order_id', $order_id)
                                        ->sum('order_has_items.hfq_qty');
            if(!(isset($pickup_tags))){
                $pickup_tags         = 0;
            }
            if(!(isset($scan_tags))){
                $scan_tags       = 0;
            }
            if(!(isset($bt_tags))){
                $bt_tags            = 0;
            }
            if(!(isset($nr_tags))){
                $nr_tags            = 0;
            }

            if(!(isset($hfq_tags))){
                $hfq_tags           = 0;
            }
            

            $tags['pickup_tags']    = $pickup_tags;
            $tags['scan_tags']      = $scan_tags;
            $tags['bt_tags']        = $bt_tags;
            $tags['nr_tags']        = $nr_tags;
            $tags['hfq_tags']       = $hfq_tags;
            return $tags;
        }else{
            return 0;
        }
    }

    public function get_route_plan_no($order_id){
        $data                = DB::table('route_plans')
                                        ->where('route_plans.order_id', $order_id)
                                        ->where('route_plans.status_id',"!=", 1)
                                        ->whereNull('route_plans.is_canceled')
                                        ->where('route_plans.status_id',"!=", 1)
                                        ->select(
                                                    'route_plans.route'
                                                )
                                        ->first();

        if( isset($data->route) ){
           return ($data->route);
        }
        return null;
    }

    public function fn_find_packing_history($order_id, $status_id){
        // 14: content packed
        $history                = DB::table('order_histories')
                                    ->leftjoin('users', 'users.id', '=', 'order_histories.created_by')
                                    ->where('order_histories.order_id', $order_id)
                                    ->where('order_histories.status_id', $status_id)
                                    ->select(
                                            'users.name as user_name',
                                            'order_histories.created_at as created_at'
                                            )
                                    ->first();
        if(!(isset($history->user_name))){
            $history = collect();
            $history->user_name   = 0;
            $history->created_at  = 0;
        }
        return $history;
        
    }

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'hub_id'                => 'required|min:1',
                'from_date'             => 'required|date',
                // 'to_date'               => 'required|date|after:from_date',
                'to_date'               => 'required|date|after_or_equal:from_date',
                'find_col'              => 'required',
            ],
            [
                'hub_id.required'       =>'Please select at least one hub',
                'find_col.required'     =>'Please select Find by column',
                // 'to_date.after'         =>'Please select \" To date\" greater than \"From date\" ',
            ]
        );

        if ($validator->passes()) {

            $to     = $request['to_date'];
            $from   = $request['from_date'];
            $col    = $request['find_col'];

            if($to < $from){
                return response()->json(['error'=>[0=>'Please select "To date" greater than "From date" ']]);
            }
            
            if($col   == 'pickup_date'){
                $col  = 'orders.pickup_date';
            }else if($col  == 'delivery_date'){
                $col  = 'orders.delivery_date';
            }else{
                $col  = 'orders.packing_date';
            }

            $hub_id  = 'orders.hub_id';
            if( ($col == 'orders.pickup_date') || ($col == 'orders.delivery_date')  ){
                $orders         = DB::table('orders')
                                    ->orderBy('orders.id','ASC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select(
                                                'orders.id',
                                                'orders.ref_order_id',
                                                'orders.hub_id',
                                                'orders.polybags_printed',
                                                'customers.name as customer_name',
                                                'orders.pickup_date',
                                                'orders.delivery_date',
                                                'orders.packed_weight',
                                                'orders.delivery_rider' 
                                            )
                                    ->distinct('orders.id')
                                    ->where('orders.status_id2', '!=' , 16)
                                    // ->whereNull('orders.ref_order_id')  
                                    ->where($hub_id, $request['hub_id'])
                                    ->whereDate($col,'>=', $from)  
                                    ->whereDate($col,'<=', $to)  
                                    // ->whereBetween($col, [$from, $to])  
                                    ->get();
            }else{

             

                $orders         = DB::table('orders')
                                    ->orderBy('orders.id','ASC')
                                    ->leftjoin('order_histories', 'order_histories.order_id', '=', 'orders.id')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select(
                                                'orders.id',
                                                'orders.ref_order_id',
                                                'orders.hub_id',
                                                'orders.polybags_printed',
                                                'customers.name as customer_name',
                                                'orders.pickup_date',
                                                'orders.delivery_date',
                                                'orders.packed_weight',
                                                'orders.delivery_rider',
                                                'order_histories.created_at'
                                            )
                                    ->where('order_histories.status_id',14)
                                    ->distinct('orders.id')
                                    ->where($hub_id, $request['hub_id'])
                                    ->whereDate('order_histories.created_at','>=',$from)
                                    ->whereDate('order_histories.created_at','<=',$to)
                                    // ->whereBetween('order_histories.created_at', [$from, $to])  
                                    ->get();
            }
            
            if(!($orders->isEmpty())){
                $ord = [];
                foreach ($orders as $key => $value) {
                    $tags                   = $this->fn_count_tags($value->id);
                    $value->wash_name       = $this->find_wash_house($value->id);
                    $value->pickup_weight   = $this->fn_calc_pickup_weight($value->id);
                    $value->polybags        = $this->fn_count_polybags($value->id);
                    $value->route           = $this->get_route_plan_no($value->id);
                    // 14: content packed
                    $packing_hstry          = $this->fn_find_packing_history($value->id,14);
                    if(($packing_hstry->created_at) != 0 ){
                       $pack_dt = date('d-m-Y', strtotime($packing_hstry->created_at));
                       $pack_tm = date('H:i:s', strtotime($packing_hstry->created_at));
                    }else{
                        $pack_dt = 0;
                        $pack_tm = 0;
                    }
    
                    // if(isset($value->ref_order_id)){
                    //     $value->type    = "REG";
                    // }else{
                    //     $value->type    = "HFQ";
                    // }
    
                    // echo  $packing_hstry->user_name;
                    // dd($packing_hstry);
                    $value->packer          = $packing_hstry->user_name;
                    $value->pack_dt         = $pack_dt;
                    $value->pack_tm         = $pack_tm;
    
                    $value->pickup_tags     = $tags['pickup_tags'];
                    $value->scan_tags       = $tags['scan_tags'];
                    $value->bt_tags         = $tags['bt_tags'];
                    $value->nr_tags         = $tags['nr_tags'];
                    $value->hfq_tags        = $tags['hfq_tags'];
                    $value->packed_tags     = ($tags['nr_tags'] + $tags['bt_tags'] + $tags['scan_tags']);
                    $ord[$key]              = $value;
                }
                $rec = collect($ord);
                // dd($rec);
                
                if($orders){

                    $total_ord      = 0;
                    $total_pcs      = 0;
                    $packed_ord     = 0;
                    $packed_pcs     = 0;
                    $unpacked_ord   = 0;
                    $unpacked_pcs   = 0;
                   

                    $total_ord      = sizeof($orders);

                    foreach ($orders as $key => $value) {
                        if((isset($value->polybags_printed)) && ($value->polybags_printed == 1)){
                            $packed_ord++;
                        }else{
                            $unpacked_ord++;
                        }

                        if((isset($value->pickup_tags)) && (($value->pickup_tags)!=0) ){
                            $total_pcs+=($value->pickup_tags);
                        }

                        if((isset($value->packed_tags)) && (($value->packed_tags)!=0) ){
                            $packed_pcs+=($value->packed_tags);
                        }

                        if((isset($value->packed_tags)) && (($value->packed_tags)==0) ){
                            $unpacked_pcs+=($value->pickup_tags);
                        }
                        
                    }
                    
                    $summary['total_ord']       = $total_ord;
                    $summary['total_pcs']       = $total_pcs;
                    $summary['packed_ord']      = $packed_ord;
                    $summary['packed_pcs']      = $packed_pcs;
                    $summary['unpacked_ord']    = $unpacked_ord;
                    $summary['unpacked_pcs']    = $unpacked_pcs;

                    $details    = view('packings.packing_report_table',
                                      compact('rec'))
                                    ->render();
                    return response()->json(['data'=>$orders,'details'=>$details,'summary'=>$summary]);
                }else{
                    return response()->json(['error'=>[0=>'Data not found']]);
                    // return response()->json(['error'=>"Data not found"]);
                }
            }else{
                return response()->json(['error'=>[0=>'Data not found']]);
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
