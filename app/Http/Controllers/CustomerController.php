<?php
namespace App\Http\Controllers;

use DB;
use DataTables;
use Exception;
use App\Models\Day;
use App\Models\Zone;
use App\Models\Service;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Customer_type;
use App\Models\Customer_has_item;
use App\Models\Customer_has_wallet;
use App\Models\Customer_has_service;
use App\Models\Customer_has_message;
use App\Models\Customer_has_address;
use App\Http\Controllers\Controller;
use App\Models\Customer_has_retainer_day;

class CustomerController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:customer-list', ['only' => ['index','show']]);
         $this->middleware('permission:customer-create', ['only' => ['create','store']]);
         $this->middleware('permission:customer-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:customer-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('customers.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        // $data       = customer::orderBy('customers.id')
        $data       = DB::table('customers')->orderBy('customers.name')
                        ->leftjoin('customer_types', 'customer_types.id', '=', 'customers.customer_type_id')
                        ->select(
                                'customers.id',
                                'customers.name',
                                'customers.contact_no',
                                'customer_types.name as customer_type_name',
                                //  DB::raw('@srno  := @srno  + 1 AS srno')
                                )

                        ->get();

        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="customers/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="customers/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('customer_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->addIndexColumn()
                ->make(true);

    }
    public function getServices(Request $request) {
        // Get the status from the request
        $status = $request->input('status');
    
        // Fetch services from the database where status is 1
        $services = Service::where('status', 1)->get();
    
        // Return the service IDs as JSON response
        return response()->json($services->pluck('id'));
    }
    
    
    public function create()
    {
        $days                   = Day::pluck('name','id')->all();
        $statuses               = fetch_status();
        $customer_types         = Customer_type::pluck('name','id')->all();

        $services               = DB::table('services')
                                    ->orderBy('orderNumber')
                                    ->select(
                                            'services.id',
                                            'services.name',
                                            'services.rate'
                                            )
                                    ->get()
                                    ->all();

        $servicesRetail             = DB::table('services')
                                ->orderBy('orderNumber')
                                ->where('status','1')
                                ->select(
                                        'services.id',
                                        'services.name',
                                        'services.rate'
                                        )
                                ->get()
                                ->all();

        $all_special_services   = DB::table('services')
                                   ->orderBy('orderNumber')
                                    ->where('unit_id','2')
                                    ->get()
                                    ->all();

        $all_special_servicesRetail   = DB::table('services')
                                        ->orderBy('orderNumber')
                                        ->where('status','1')
                                        ->where('unit_id','2')
                                        ->get()
                                        ->all();


        $special_services       = DB::table('services')
                                    ->orderBy('orderNumber')
                                    ->leftjoin('service_has_items', 'service_has_items.service_id', '=', 'services.id')
                                    ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                                    ->select(
                                            'services.id',
                                            'services.name',
                                            'services.rate',
                                            'service_has_items.item_rate',
                                            'service_has_items.item_id',
                                            'items.name as item_name'
                                            )
                                    ->where('unit_id','2')
                                    ->get()
                                    ->all();

        $special_servicesRetail       = DB::table('services')
                                ->orderBy('orderNumber')
                                ->leftjoin('service_has_items', 'service_has_items.service_id', '=', 'services.id')
                                ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                                ->select(
                                        'services.id',
                                        'services.name',
                                        'services.rate',
                                        'service_has_items.item_rate',
                                        'service_has_items.item_id',
                                        'items.name as item_name'
                                        )
                                ->where('unit_id','2')
                                ->where('status','1')
                                ->get()
                                ->all();


        $messages               = DB::table('messages')
                                    ->orderBy('id')
                                    ->select('messages.id','messages.name')
                                    ->get()
                                    ->all();

        
        $time_slots             = DB::table('time_slots')
                                    ->select(
                                            'id',
                                             DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name')
                                            )
                                    ->pluck('name','id')
                                    ->all();

        $areas                  = DB::table('areas')
                                    ->pluck('center_points','id')
                                    ->all();

        
        return view('customers.create',
                        compact('days',
                                'customer_types',
                                'messages',
                                'services',
                                'special_services',
                                'time_slots',
                                'statuses',
                                'all_special_services',
                                'areas',
                                'all_special_servicesRetail',
                                'special_servicesRetail',
                                'servicesRetail',
                               

                            ));
    }


    public function store(Request $request)
    {
        // BEGIN::setting 'in_amount' = 0, if it is not set 
        if( (!(isset($request['in_amount'])) ) ||($request['in_amount'] == null) ){
            $request['in_amount'] = 0;
        }
        // END::setting 'in_amount' = 0, if it is not set 
        $this->validate($request, 
            [
                'name'                  => 'required|min:3|unique:customers,name|regex:/^([^0-9]*)$/',
                'contact_no'            => 'required|unique:customers,contact_no|digits:11|numeric',
                'address'               => 'required|array',
                'address.*'             => 'required|string|distinct|min:2',
                'in_amount'             => 'required|numeric|min:0|max:10000000',
                // 'email'                 => 'unique:customers,email',
            ],
            [
                'name.regex'            => 'Special characters and numbers are not allowed!',
                'address.required'      => 'Please add atleast one address!',
                'address.*.distinct'    => 'Please use different address!',
                'address.*.required'    => 'Please enter address!',
            ]
        );

        try{
         

           

            if((!(isset($request['email']))) || ($request['email'] == null)){
                    $request['email_alert'] = 0;
            }
            $data           = customer::create($request->all());
            $customer_id    = $data['id'];

            // Retainer Days //
            $day            = $request['day'];
            $note           = $request['note'];
            $time_slot      = $request['time_slot'];

            // Addresses //
            $address        = $request['address'];
            $latitude       = $request['latitude'];
            $longitude      = $request['longitude'];
            $status         = $request['status'];

            // Services //
            $service_rate   = $request['service_rate'];
            $service_status = $request['service_status'];

            // Service has items //
            $item_rate      = $request['item_rate'];
            $item_service_id= $request['item_service_id'];
            $item_status    = $request['item_status'];

            // Wallet Transaction //
            $in_amount      = $request['in_amount'];

            // Message alerts //
            $message        = $request['message'];



            if($data){
                if(!(empty($day))){
                    foreach($day as $key => $value){
                        $var                =  new Customer_has_retainer_day();
                        $var->customer_id   = $customer_id;
                        $var->day_id        = $value;
                        $var->time_slot_id  = $time_slot[$key];
                        $var->note          = $note[$key];
                        $var->save();
                    }
                }

                if(!(empty($address))){
                    foreach($address as $key => $value){
                        $var                =  new Customer_has_address();
                        $var->customer_id   = $customer_id;
                        $var->address       = $value;
                        $var->latitude      = $latitude[$key];
                        $var->longitude     = $longitude[$key];
                        $var->status        = $status[$key];
                        $var->save();
                    }
                }

                if(!(empty($service_rate))){
                
                    foreach($service_rate as $sr_key => $sr_value){
                        if(isset($service_status[$sr_key])){
                            $var                =  new Customer_has_service();
                            $var->customer_id   = $customer_id;
                            $var->service_id    = $sr_key;
                            $var->service_rate  = $sr_value;
                            $var->status        = $service_status[$sr_key];
                            
                            $var->save();
                            if(!(empty($item_rate))){
                                foreach($item_rate as $key => $value){
                                    // echo "service_id: ".$key;
                                    // echo "<br>======================<br>";
                                    if($key == $sr_key){
                                        foreach($value as $k => $v){
                                        
                                            if(!(empty($item_status[$key][$k]))){
                                                // $item_status[$key][$k]=0;
                                                $var                =  new Customer_has_item();
                                                $var->customer_id   = $customer_id;
                                                $var->service_id    = $key;
                                                $var->item_id       = $k;
                                                $var->item_rate     = $v;
                                                $var->status        = $item_status[$key][$k];
                                                $var->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        
                    }
                }


                if(isset($in_amount)){
                    $var                    =  new Customer_has_wallet();
                    $var->customer_id       = $customer_id;
                    $var->in_amount         = $in_amount;
                    $var->wallet_reason_id  = 1;
                    $var->detail            = "AC Open";
                    $var->save();
                }

                if(!(empty($message))){
                    foreach($message as $key => $value){
                        $var                =  new Customer_has_message();
                        $var->customer_id   = $customer_id;
                        $var->message_id    = $value;
                        $var->save();
                    }
                }
            }

            return redirect()->route('customers.index')
                            ->with('success','customer '.$request['name']. ' added successfully.');
        }catch(Exception $e){
            return redirect()
                        ->back()
                        ->withInput($request->input())
                        ->with('permission','Something went wrong!!!');
        }
    }

    public function show($id)
    {
        $data               = DB::table('customers')
                                ->leftjoin('customer_types', 'customer_types.id', '=', 'customers.customer_type_id')
                                ->select('customers.*','customer_types.name as customer_type_name')
                                ->where('customers.id', $id)
                                ->first();
                                
        $selected_wallets   = DB::table('customer_has_wallets')
                                ->select('customer_has_wallets.customer_id',
                                        DB::raw('SUM((customer_has_wallets.in_amount)-(customer_has_wallets.out_amount)) AS net_amount')
                                        )
                                ->groupBy('customer_has_wallets.customer_id')
                                ->where('customer_has_wallets.customer_id', $id)
                                ->get()
                                ->first();

        $wallet_transaction = DB::table('customer_has_wallets')
                                // ->select('customer_has_wallets.customer_id',
                                //         DB::raw('SUM((customer_has_wallets.in_amount)-(customer_has_wallets.out_amount)) AS net_amount')
                                //         )
                                ->select('customer_has_wallets.*')
                                // ->groupBy('customer_has_wallets.customer_id')
                                ->where('customer_has_wallets.customer_id', $id)
                                ->get()
                                ->all();


        $selected_messages  = DB::table('customer_has_messages')
                                ->leftjoin('messages', 'messages.id', '=', 'customer_has_messages.message_id')
                                ->select('messages.id as message_id',
                                        'messages.name as message_name')
                                ->where('customer_has_messages.customer_id', $id)
                                ->get()
                                ->all();

        $selected_days      = DB::table('customer_has_retainer_days')
                                ->leftjoin('days', 'days.id', '=', 'customer_has_retainer_days.day_id')
                                ->leftjoin('time_slots', 'time_slots.id', '=', 'customer_has_retainer_days.time_slot_id')
                                ->select('customer_has_retainer_days.id',
                                         'days.name as day_name',
                                         'customer_has_retainer_days.note as day_note',
                                         DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as time_slot_name')
                                         )
                                ->where('customer_has_retainer_days.customer_id', $id)
                                ->get()
                                ->all();

        $selected_addresses = DB::table('customer_has_addresses')
                                ->select('customer_has_addresses.*',
                                        DB::raw('(CASE 
                                            WHEN customer_has_addresses.status = "0" THEN "Primary" 
                                            ELSE "Secondary" 
                                            END) AS status')
                                        )
                                ->where('customer_has_addresses.customer_id', $id)
                                ->get()
                                ->all();

        $selected_services  = DB::table('customer_has_services')
                                ->leftjoin('services', 'services.id', '=', 'customer_has_services.service_id')
                                ->select('services.id as service_id',
                                        'services.name as service_name',
                                        'customer_has_services.service_rate',
                                        'customer_has_services.status')
                                ->where('customer_has_services.customer_id', $id)
                                // ->where('customer_has_services.status', 1)
                                ->orderBy('customer_has_services.order_number','ASC')

                                ->get()
                                ->all();

        $selected_items     = DB::table('customer_has_items')
                                ->leftjoin('items', 'items.id', '=', 'customer_has_items.item_id')
                                ->leftjoin('services', 'services.id', '=', 'customer_has_items.service_id')
                                ->select('services.id as service_id',
                                        'items.id as item_id',
                                        'items.name as item_name',
                                        'customer_has_items.item_rate',
                                        'services.name as service_name')
                                ->where('customer_has_items.customer_id', $id)
                                // ->where('customer_has_items.status', 1)
                                ->get()
                                ->all();

        return view('customers.show',
                    compact('data',
                            'selected_wallets',
                            'selected_messages',
                            'selected_days',
                            'selected_addresses',
                            'selected_services',
                            'selected_items',
                            'wallet_transaction'
                        )
                    );
    }
 
    public function edit($id)
    {
        $data                   = customer::findOrFail($id);  
        
        $days                   = Day::pluck('name','id')->all();
        $statuses               = fetch_status();
        $customer_types         = Customer_type::pluck('name','id')->all();
        if($data->customer_type_id==1)
        {
        $services               = DB::table('services')
                                   ->orderBy('orderNumber')
                                    ->where("status",1)
                                    ->select('services.id','services.name','services.rate')
                                    ->get()
                                    ->all();
        }
        else{
        $services               = DB::table('services')
                                ->orderBy('orderNumber')
                                ->select('services.id','services.name','services.rate')
                                ->get()
                                ->all();

        }
        if($data->customer_type_id==1)
        {
        $all_special_services   = DB::table('services')
                                  ->orderBy('orderNumber')
                                    ->where("status",1)
                                    ->where('unit_id','2')
                                    ->get()
                                    ->all();
        }
        else{
            $all_special_services   = DB::table('services')
                                   ->orderBy('orderNumber')
                                    ->where('unit_id','2')
                                    ->get()
                                    ->all();

        }
        if($data->customer_type_id==1)
        {
        $special_services       = DB::table('services')
                                    ->orderBy('orderNumber')
                                    ->where("status",1)
                                    ->leftjoin('service_has_items', 'service_has_items.service_id', '=', 'services.id')
                                    ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                                    ->select('services.id',
                                            'services.name',
                                            'services.rate',
                                            'service_has_items.item_rate',
                                            'service_has_items.item_id',
                                            'items.name as item_name')
                                    ->orwhere('unit_id','2')
                                    ->get()
                                    ->all();
        }
        else{
        $special_services       = DB::table('services')
                                        ->orderBy('orderNumber')
                                        ->leftjoin('service_has_items', 'service_has_items.service_id', '=', 'services.id')
                                        ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                                        ->select('services.id',
                                                'services.name',
                                                'services.rate',
                                                'service_has_items.item_rate',
                                                'service_has_items.item_id',
                                                'items.name as item_name')
                                        ->orwhere('unit_id','2')
                                        ->get()
                                        ->all();
            

        }
       
       
        $selected_special_services = DB::table('customer_has_items')
                                        ->where('customer_has_items.customer_id', $id)
                                        ->get()
                                        ->all();
                                        
        
        $selected_services      = DB::table('customer_has_services')
                                     ->orderBy('order_number')
                                    ->where('customer_has_services.customer_id', $id)
                                    ->get()
                                    ->all();

        $messages               = DB::table('messages')
                                    ->orderBy('id')
                                    ->select('messages.id','messages.name')
                                    ->get()
                                    ->all();

        
        $time_slots             = DB::table('time_slots')
                                    ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                    ->pluck('name','id')
                                    ->all();

        $selected_messages      = DB::table('customer_has_messages')
                                    ->leftjoin('messages', 'messages.id', '=', 'customer_has_messages.message_id')
                                    ->select('messages.id as message_id',
                                            'messages.name as message_name',
                                            'customer_has_messages.status')
                                    ->where('customer_has_messages.customer_id', $id)
                                    ->get()
                                    ->all();

        $selected_addresses     = DB::table('customer_has_addresses')
                                    ->where('customer_has_addresses.customer_id', $id)
                                    ->get()
                                    ->all();

        $selected_days          = DB::table('customer_has_retainer_days')
                                    ->where('customer_has_retainer_days.customer_id', $id)
                                    ->get()
                                    ->all();

        $selected_wallets       = DB::table('customer_has_wallets')
                                    ->select('customer_has_wallets.customer_id',
                                            DB::raw('SUM((customer_has_wallets.in_amount)-(customer_has_wallets.out_amount)) AS net_amount')
                                            )
                                    ->groupBy('customer_has_wallets.customer_id')
                                    ->where('customer_has_wallets.customer_id', $id)
                                    ->get()
                                    ->first();
                              
        $customer_items         = array();
                                    foreach($selected_special_services as $cust_items)
                                    {
                                        $customer_items[$cust_items->service_id][$cust_items->item_id] = array(
                                            'item_rate' => $cust_items->item_rate,
                                            'status'    => $cust_items->status,
                                            'item_id'    => $cust_items->item_id,
                                        );
                                    }

        return 
            view('customers.edit',
            compact('data',
                    'selected_messages',
                    'selected_addresses',
                    'selected_days',
                    'selected_services',
                    'selected_special_services',
                    'selected_wallets',
                    'days',
                    'customer_types',
                    'messages',
                    'services',
                    'special_services',
                    'time_slots',
                    'statuses',
                    'all_special_services',
                    'customer_items'
                )
            );
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,
            [
                
                'name'                  => 'required|min:3||regex:/^([^0-9]*)$/|unique:customers,name,'.$id,
                'contact_no'            => 'required|unique:customers,contact_no,'.$id,
                'address'               => 'required|array',
                'address.*'             => 'required|string|distinct|min:2',
                // 'in_amount'             => 'required|numeric|min:0|max:10000000',
                'in_amount'             => 'required|numeric',
                // 'email'                 => 'unique:customers,email,'.$id,
            ],
            [
                'name.regex'            => 'Special characters and numbers are not allowed!',
                'address.required'      => 'Please add atleast one address!',
                'address.*.distinct'    => 'Please use different address!',
                'address.*.required'    => 'Please enter address!',
            ]
        );
        
        try{
            $data               = customer::find($id);
           
            if((!(isset($request['email_alert'])))) {
                $request['email_alert'] = 0;
            }

            if((!(isset($request['email']))) || ($request['email'] == null)){
                $request['email_alert'] = 0;
            }
        

            $upd             = $data->update($request->all());
            $customer_id     = $id;

            // Retainer Days //
            $day             = $request['day'];
            $note            = $request['note'];
            $time_slot       = $request['time_slot'];

            // Addresses //
            $address        = $request['address'];
            $latitude       = $request['latitude'];
            $longitude      = $request['longitude'];
            $status         = $request['status'];

            // Services //
            $service_rate   = $request['service_rate'];
            $service_status = $request['service_status'];

            // Service has items //
            $item_rate       = $request['item_rate'];
            $item_status     = $request['item_status'];
            $item_service_id = $request['item_service_id'];

            // wallet can not be updated
            // Wallet Transaction //
            // $in_amount      = $request['in_amount'];

            // Message alerts //
            $message        = $request['message'];
        
        
            if($data){
                DB::table("customer_has_retainer_days")->where('customer_id', '=', $id)->delete();
                // DB::table("customer_has_addresses")->where('customer_id', '=', $id)->delete();
                DB::table("customer_has_services")->where('customer_id', '=', $id)->delete();
                DB::table("customer_has_items")->where('customer_id', '=', $id)->delete();
                DB::table("customer_has_messages")->where('customer_id', '=', $id)->delete();

                // wallet can not be updated
                // DB::table("customer_has_wallets")->where('customer_id', '=', $id)->delete();
                if(!(empty($day))){
                    foreach($day as $key => $value){
                        $var                =  new Customer_has_retainer_day();
                        $var->customer_id   = $customer_id;
                        $var->day_id        = $value;
                        $var->time_slot_id  = $time_slot[$key];
                        $var->note          = $note[$key];
                        $var->save();
                    }
                }

                $ids = array();
                if(!(empty($address))){
                    $cus_add            = Customer_has_address::where('customer_id',$id)->get()->all();
                    foreach ($request['address'] as $key => $value) {
                        if(isset($cus_add[$key]->customer_id)){
                            // if this key exits then update new address on the same key.
                            $chk            = Customer_has_address::where('customer_id',$customer_id)->limit(1)->whereNotIn('id',  $ids)
                                                ->update([
            
                                                    'address'       => $value,
                                                    'latitude'      => $latitude[$key],
                                                    'longitude'     => $longitude[$key],  
                                                    'status'        => $status[$key]
                                                ]);

                            // push ids in array to not to delete these ids
                            array_push($ids,($cus_add[$key]->id));
                        }else{
                            // if this key not exits then create new address on the new key.
                            $var                =  new Customer_has_address();
                            $var->customer_id   = $customer_id;
                            $var->address       = $value;
                            $var->latitude      = $latitude[$key];
                            $var->longitude     = $longitude[$key];
                            $var->status        = $status[$key];
                            $var->save();

                            // push ids in array to not to delete these ids
                            array_push($ids,($var->id));
                        }
                    }
                    // dd( $ids);
                    // ->where('status', '=', 1)
                    // Delete all secondary address; If we have more addresses then newly entered addresses of this customer
                    DB::table("customer_has_addresses")->where('customer_id', '=', $id)->whereNotIn('id',  $ids)->delete();
                }

                if(!(empty($service_rate))){
                
                    foreach($service_rate as $sr_key => $sr_value){
                        if(isset($service_status[$sr_key])){
                            $var                =  new Customer_has_service();
                            $var->customer_id   = $customer_id;
                            $var->service_id    = $sr_key;
                            $var->service_rate  = $sr_value;
                            $var->status        = $service_status[$sr_key];
                            
                            $var->save();
                            if(!(empty($item_rate))){
                                foreach($item_rate as $key => $value){
                                    // echo "service_id: ".$key;
                                    // echo "<br>======================<br>";
                                    if($key == $sr_key){
                                        foreach($value as $k => $v){
                                        
                                            if(!(empty($item_status[$key][$k]))){
                                                // $item_status[$key][$k]=0;
                                                $var                =  new Customer_has_item();
                                                $var->customer_id   = $customer_id;
                                                $var->service_id    = $key;
                                                $var->item_id       = $k;
                                                $var->item_rate     = $v;
                                                $var->status        = $item_status[$key][$k];
                                                $var->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        
                    }
                }

                // wallet can not be updated
                // if($in_amount){
                //     $var                    =  new Customer_has_wallet();
                //     $var->customer_id       = $customer_id;
                //     $var->in_amount         = $in_amount;
                //     $var->wallet_reason_id  = 1;
                //     $var->detail            = "AC Open";
                //     $var->save();
                // }

                if(!(empty($message))){
                    foreach($message as $key => $value){
                        $var                =  new Customer_has_message();
                        $var->customer_id   = $customer_id;
                        $var->message_id    = $value;
                        $var->save();
                    }
                }
            }

            return redirect()->route('customers.index')
                ->with('success','Customer '.$request['name']. ' updated successfully');
        }catch(Exception $e){
            return redirect()
                        ->back()
                        ->withInput($request->input())
                        ->with('permission','Something went wrong!!!');
        }
    }


    public function destroy(Request $request)
    { 
        $id         =   $request->ids;
        $customer   =   customer::find($id);

        $chk        =   DB::table('orders')
                            ->select('orders.id')
                            ->where('orders.customer_id',$id)
                            ->first();


        if(!(isset($chk->id))){
            DB::table("customer_has_retainer_days")->where('customer_id', '=', $id)->delete();
            DB::table("customer_has_addresses")->where('customer_id', '=', $id)->delete();
            DB::table("customer_has_services")->where('customer_id', '=', $id)->delete();
            DB::table("customer_has_items")->where('customer_id', '=', $id)->delete();
            DB::table("customer_has_wallets")->where('customer_id', '=', $id)->delete();
            DB::table("customer_has_messages")->where('customer_id', '=', $id)->delete();

            $data    =  DB::table("customers")->whereIn('id',explode(",",$id))->delete();
            return response()->json(['success'=>$data." Customer deleted successfully."]);
        }
        return response()->json(['error'=>"This customer cannot be deleted"]);
        
        
    }

      

    
}
