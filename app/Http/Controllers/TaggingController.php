<?php
namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Order;
use DataTables;
use Validator;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Distribution_hub;
use App\Models\Wash_house_has_hub;

class TaggingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:tagging-list', ['only' => ['index','show']]);
        $this->middleware('permission:tagging-create', ['only' => ['create','store']]);
        $this->middleware('permission:tagging-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:tagging-delete', ['only' => ['destroy']]);
    }

    public function index()
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
        return view('taggings.index',
                        compact('hubs')
                    );
    }

    public function find_wash_house($hub_id)
    {
        if(isset($hub_id)){
            $wash_houses    = wash_house_has_hub::select('wash_house_has_hubs.wash_house_id','wash_houses.name as wash_house_name')
                                ->leftjoin('wash_houses', 'wash_houses.id', '=', 'wash_house_has_hubs.wash_house_id')
                                ->where('wash_house_has_hubs.hub_id',$hub_id)
                                ->first();
            if(isset($wash_houses)){            
                return $wash_houses->wash_house_name;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    public function fn_get_delivery_charges(){
    
        $data                   =  DB::table('delivery_charges')
                                    ->select(
                                                'order_amount',
                                                'delivery_charges'
                                            )
                                    ->first();
        return $data;
                           

    }

    public function fn_get_addon_amount($order_id){
        $amount = 0;
        $all_addons             =  DB::table('order_has_addons')
                                    // ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                    ->leftjoin('order_has_items', 'order_has_items.id', '=', 'order_has_addons.ord_itm_id')
                                    ->where('order_has_addons.order_id',$order_id)
                                    ->select(
                                                // 'addons.rate',
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
                                    // ->leftjoin('customer_has_services', 'customer_has_services.customer_id', '=', 'orders.customer_id')
                                    ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                    ->where('order_has_services.order_id',$order_id)
                                    // ->where('services.unit_id',2)
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

                                // dd($all_services);
        foreach ($all_services as $key => $value) {
            if($value->unit_id == 1){
                 // If unit is KG:1 then rate will be based on service weight
                // $data           =  DB::table('customer_has_services')
                //                     ->where('customer_has_services.customer_id',$value->customer_id)
                //                     ->where('customer_has_services.service_id',$value->service_id)
                //                     ->select(
                //                                 'customer_has_services.service_id',
                //                                 'customer_has_services.service_rate',
                //                             )
                //                     ->first();
                $tot            += (($value->weight) * ( $value->service_rate));

                //                 echo "Qty: ". $value->weight. "<br>";
                // echo "<pre>";
                // print_r($data);
                // echo "</pre>";
            }else if($value->unit_id == 2){
                // If unit is item:2 then rate will be based on item rate which will be different for all items

                $items      =  DB::table('order_has_items')
                                    // ->leftjoin('order_has_items', 'order_has_items.service_id', '=', 'order_has_services.service_id')
                                    // ->leftjoin('customer_has_items', 'customer_has_items.item_id', '=', 'order_has_items.item_id')
                                    ->where('order_has_items.order_id',$value->id)
                                    ->where('order_has_items.service_id',$value->service_id)
                                    
                                    // ->where('customer_has_items.customer_id',$value->customer_id)
                                    
                                    ->select(
                                                'order_has_items.order_id',
                                                'order_has_items.item_id',
                                                'order_has_items.pickup_qty',
                                                'order_has_items.service_id as service_id',
                                                'order_has_items.cus_item_rate as item_rate',
                                            )
                                    ->get();
                                    // dd($items);
                foreach ($items as $item_key => $item_value) {

                    // $rec      =  DB::table('customer_has_items')
                    //                 // ->where('customer_has_items.order_id',$value->id)
                    //                 ->where('customer_has_items.service_id',$item_value->service_id)
                    //                 ->where('customer_has_items.item_id',$item_value->item_id)
                    //                 ->where('customer_has_items.customer_id',$value->customer_id)
                    //                 ->select(
                    //                             // 'customer_has_items.order_id',
                    //                             'customer_has_items.item_id',
                    //                             'customer_has_items.item_rate',
                    //                         )
                    //                 ->first();

                                    // dd($rec);
                    // echo $item_key."pick_up:  ". ($item_value->pickup_qty) . "<br>";
                    $tot   +=   (($item_value->pickup_qty) * ($item_value->item_rate));
                }
                // dd($tot);
            }else if($value->unit_id == 3){
                // If unit is piece:3 then rate will be based on rate which will be same for all items
                // $data           =  DB::table('customer_has_services')
                //                     ->where('customer_has_services.customer_id',$value->customer_id)
                //                     ->where('customer_has_services.service_id',$value->service_id)
                //                     ->select(
                //                                 'customer_has_services.service_id',
                //                                 'customer_has_services.service_rate',
                //                             )
                //                     ->first();
                $tot            += (($value->qty) * ( $value->service_rate));
            }
           
        }

        return $tot;
                           

    }

    public function fn_hanger_required($order_id){
        $data           =  DB::table('order_has_services')
                            ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                            ->where('order_has_services.order_id',$order_id)
                            ->where('services.hanger',1)
                            ->select('services.hanger')
                            ->first();

        if(isset($data->hanger)){
            return 1;       //if order has hanger
        }else{
            return 0;       //if order has no hanger
        }
    }

    public function fn_calc_pickup_weight_n_pcs($order_id){
        $data['weight']      = 0;
        $data['qty']         = 0;
        if(isset($order_id)){
            $weight         = DB::table('order_has_services')
                                ->where('order_has_services.order_id', $order_id)
                                ->sum('order_has_services.weight');

            $qty            = DB::table('order_has_services')
                                ->where('order_has_services.order_id', $order_id)
                                ->sum('order_has_services.qty');

            $data['weight']         = $weight;
            $data['qty']            = $qty;
            
            if(!(isset($weight))){
                $data['weight']         = 0;
                $data['qty']            = 0;
            }
            return $data;
        }else{
            return $data;
        }
    }

    public function fn_find_tagger($order_id, $status_id){
        // 14: content packed
        $history                = DB::table('order_histories')
                                    ->leftjoin('users', 'users.id', '=', 'order_histories.created_by')
                                    ->where('order_histories.order_id', $order_id)
                                    ->where('order_histories.status_id', $status_id)
                                    ->select(
                                            'users.name as user_name'
                                            )
                                    ->first();
        if(!(isset($history->user_name))){
            $history                = collect();
            $history->user_name     = "";
        }
        return $history;
        
    }

    public function fn_add_tax($amount){
        $d_amount = 0;  $vat_amount =0; 
        $vat            = DB::table('vats')
                            ->select(
                                        'vats.vat'
                                    )
                            ->first();

        $d_charges      = DB::table('delivery_charges')
                            ->select(
                                        'delivery_charges.order_amount',
                                        'delivery_charges.delivery_charges'
                                    )
                            ->first();
        if($d_charges){
            $order_amount  = $d_charges->order_amount; 
            if($amount < $order_amount){
                $d_amount =  $d_charges->delivery_charges;
            }
        }

        if($vat){
            $val        = $vat->vat;
            $vat_amount = ($amount * $val / 100);
        }

        $amount += ($d_amount + $vat_amount);
       return $amount;

    }

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'hub_id'                => 'required|min:1',
                'from_date'             => 'required|date',
                // 'to_date'               => 'required|date|after:from_date',
                'to_date'               => 'required|date',
                'find_col'              => 'required',
            ],
            [
                'hub_id.required'       =>'Please select at least one hub',
                'find_col.required'     =>'Please select Find by column',
                // 'to_date.after'         =>'Please select \" To date\" greater than \"From date\" ',
            ]
        );

        if ($validator->passes()) {

            $to         = $request['to_date'];
            $from       = $request['from_date'];
            $col        = $request['find_col'];

            if($to < $from){
                return response()->json(['error'=>[0=>'Please select "To date" greater than "From date" ']]);
            }
            
            if($col   == 'pickup_date'){
                $col   = 'orders.pickup_date';
            }else if($col  == 'delivery_date'){
                $col  = 'orders.delivery_date';
            }

            $hub_id  = 'orders.hub_id';
            if( ($col == 'orders.pickup_date') || ($col == 'orders.delivery_date')  ){
                $orders         = DB::table('orders')
                                    ->orderBy('orders.id','ASC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->leftjoin('customer_types', 'customer_types.id', '=', 'customers.customer_type_id')
                                    ->select(
                                                'orders.id',
                                                'orders.hub_id',
                                                'orders.order_note',
                                                // 'orders.polybags_printed',
                                                'customers.name as customer_name',
                                                'customers.contact_no as customer_no',
                                                'customer_types.name as customer_type_name',
                                                'orders.pickup_date',
                                                'orders.delivery_date',
                                                'orders.packed_weight',
                                                'orders.delivery_charges',
                                                'orders.vat_charges',
                                            )
                                    ->distinct('orders.id')
                                    ->whereNotNull('orders.tags_printed')  
                                    ->whereNotIn('orders.status_id',[1,16])
                                    ->whereNull('orders.ref_order_id')  
                                    ->where($hub_id, $request['hub_id'])
                                    // ->whereBetween($col, [$from, $to])  
                                    ->whereDate($col,'>=', $from)  
                                    ->whereDate($col,'<=', $to)  
                                    ->get();
            }
            
            if(!($orders->isEmpty())){
                $ord = [];
                $d_charges                 = $this->fn_get_delivery_charges();
                foreach ($orders as $key => $value) {
                  
                    $pickup                 = $this->fn_calc_pickup_weight_n_pcs($value->id);
                    $service_tot            = $this->fn_get_service_amount($value->id);
                    $addon_tot              = $this->fn_get_addon_amount($value->id);
                    $tot                    = ( $service_tot + $addon_tot);
                    $value->service_tot     = ( $service_tot);
                    $value->addon_tot       = ( $addon_tot);
                    // $value->tot             = ( $service_tot + $addon_tot);
                    // $value->tot             = ($this->fn_add_tax($tot)); 
                    
                    $value->tot             = (($tot) + ($value->delivery_charges) + ($value->vat_charges));
                    $value->hanger          = $this->fn_hanger_required($value->id);
                   
                    $value->wash_name       = $this->find_wash_house($value->hub_id);
                    // 14: content packed
                    $hstry                  = $this->fn_find_tagger($value->id,8);
                    
                    // echo "total: $tot <Br>";
                    // echo "order_amount: $d_charges->order_amount <Br>";
                    if((($value->delivery_charges) == 0) || (($value->delivery_charges) == null)){
                        $value->delivery    = 0;  // delivery charges will not be applied
                    }else{
                        $value->delivery    = 1;  // delivery charges will be applied
                    }
                   
                    // echo  $pickup['weight'];
                    // dd($packing_hstry);
                    $value->pickup_weight   = $pickup['weight'];
                    $value->pickup_qty      = $pickup['qty'];

                    $value->tagger          = $hstry->user_name;
    
                   
                    $ord[$key]              = $value;
                }
                $rec = collect($ord);
                // dd($rec);
                if($orders){
                    $details    = view('taggings.report',
                                      compact('rec'))
                                    ->render();
                    return response()->json(['data'=>$orders,'details'=>$details]);
                }else{
                    return response()->json(['error'=>[0=>'Data not found']]);
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
    {

        $details                = null;
        $histories              = null;
        $selected_items         = null;
        $selected_addons        = null;
        $selected_services      = null;

        $data                   = Order::orderBy('orders.created_at','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->leftjoin('statuses as st', 'st.id', '=', 'orders.status_id')
                                    ->leftjoin('statuses as st2', 'st2.id', '=', 'orders.status_id2')
                                    ->select(
                                                'orders.id',
                                                'orders.ref_order_id',
                                                'customers.id as customer_id',
                                                'customers.name',
                                                'customers.contact_no',
                                                'orders.id as order_id',
                                                'orders.pickup_date',
                                                'customers.permanent_note',
                                                'orders.order_note',
                                                'orders.delivery_date',
                                                // 'st.name as status_name',
                                                // 'st2.name as status_name2',
                                                DB::raw('(CASE 
                                                    WHEN (orders.status_id2 >= 15 AND orders.status_id2 <=18) OR (ISNULL(orders.status_id2)) THEN st.name
                                                    WHEN orders.status_id2 != "NULL" THEN st2.name
                                                    END) AS status_name'),
                                                'orders.pickup_address',
                                                'orders.pickup_timeslot',
                                                'orders.delivery_address',
                                                'orders.delivery_timeslot',
                                                'orders.packed_weight as packed_weight'
                                            )
                                    // ->whereNull('orders.delivery_rider_id')
                                    // ->whereNotNull('orders.pickup_rider_id')
                                    ->findOrFail($id);



            if($data){
           
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
                //                                     // 'users.name as user_name',
                //                                     // 'roles.name as role_name',
                //                                     'order_histories.detail',
                //                                     'order_histories.created_at as created_at'
                //                                     )
                //                             ->get()
                //                             ->all();


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
                                                    'order_histories.created_at as created_at',
                                                    'order_histories.created_by',
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



                $details                = array();
                if(!empty($selected_services)){
                    foreach($selected_services as $key => $value)
                    {

                        $record             = DB::table('order_has_items')
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

                        $details[$key]       = array(
                            'service_id'     => $value->service_id,
                            'service_name'   => $value->service_name,
                            'service_weight' => $value->service_weight,
                            'pickup_qty'     => $record->pickup_qty,
                            'scan_qty'       => $record->scan_qty,
                            'bt_qty'         => $record->bt_qty,
                            'nr_qty'         => $record->nr_qty,
                            'hfq_qty'        => $record->hfq_qty,
                            );

                        $selected_items     = DB::table('order_has_items')
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

                        $selected_addons    = DB::table('order_has_addons')
                                                ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                                ->where('order_has_addons.order_id', $id)
                                                ->select('addons.id as addon_id',
                                                        'addons.name as addon_name',
                                                        'order_has_addons.item_id as item_id',
                                                        'order_has_addons.service_id as service_id')
                                                ->get()
                                                ->all(); 

                        

                        
                    }
                    return view('taggings.show',
                                    compact('data',
                                    'selected_services',
                                    'selected_items',
                                    'selected_addons',
                                    'histories',
                                    'details')
                            );
                }else{
                    return view('taggings.show',
                                    compact('data',
                                    'selected_services',
                                    'selected_items',
                                    'selected_addons',
                                    'histories',
                                    'details')
                            );
                    
                }
            }else{
                return redirect()->route('taggins.index')
                         ->with('permission','No Record Found!!!');
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
        return view('taggings.show_history',
                    compact('data',
                            'detail'
                        )
                    );
    }

    public function edit($id)
    {}


    public function update(Request $request, $id)
    {}

    public function destroy(Request $request)
    {}





}
