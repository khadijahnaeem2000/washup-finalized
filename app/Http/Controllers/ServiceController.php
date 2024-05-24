<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use DB;
use DataTables;
use App\Models\Item;
use App\Models\Unit;
use App\Models\Addon;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Service_has_item;
use App\Models\Service_has_addon;
use App\Models\Customer_has_item;
use App\Models\Customer_has_service;


class ServiceController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:service-list', ['only' => ['index','show']]);
         $this->middleware('permission:service-create', ['only' => ['create','store']]);
         $this->middleware('permission:service-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:service-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('services.index');
    }

    public function list()
    {
        // DB::statement(DB::raw('set @srno=0'));
        $data = DB::table('services')
                ->orderBy('services.name','ASC')
                // ->orderBy('services.created_at','DESC')
                ->leftjoin('units', 'units.id', '=', 'services.unit_id')
                ->select(
                            'services.*',
                            'units.name as unit_name',
                            // DB::raw('@srno  := @srno  + 1 AS srno')
                        )
                ->get();
               
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="services/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="services/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                     
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('service_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->addIndexColumn()
                ->make(true);
    }
    
    public function updateOrder(Request $request)
    {
            if ($request->ajax()) {
                try {
                    $serviceOrders = $request->serviceOrders;
        
                    // Update order numbers for each service
                    foreach ($serviceOrders as $serviceOrder) {
                        $serviceId = $serviceOrder['serviceId'];
                        $orderNumber = $serviceOrder['orderNumber'];
                        $service = Service::find($serviceId);
                        
                        if (!$service) {
                            return response()->json(['error' => 'Service not found.']);
                        }
        
                        $service->orderNumber = $orderNumber;
                        $service->save();
                    }
        
                    return response()->json(['success' => 'Order numbers updated successfully.']);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to update order numbers.']);
                }
            }
        
            return response()->json(['error' => 'Invalid request.']);
        }


    public function create()
    {
        $units      = Unit::pluck('name','id')->all();

        $addons     = DB::table('addons')
                        ->orderBy('addons.name','ASC')
                        ->select('addons.id as addon_id','addons.name as addon_name')
                        ->get()
                        ->all();

        $items      = DB::table('items')
                        ->orderBy('items.name','ASC')
                        ->select('items.id as item_id','items.name as item_name')
                        ->get()
                        ->all();
                        
        return view('services.create',compact('units','addons','items'));
    }

    public function str_cstmr_srvc($service){

        if(isset($service)){
            
            // get all retail customer only
            $customers      = Customer::where('customers.customer_type_id',1)->pluck('id')->all();
            $srvc_itms      = null;
            
            // if service unit  = items then items will also be stored in customer_has_items table
            // otherwise for unit = (KG, Piece) rate will be manage from customer_has_services table
            if((isset($service->unit_id)) && ($service->unit_id == 2)  ){  // unit_id : 2 = items
                $srvc_itms  = Service_has_item::where('service_has_items.service_id',$service->id)->get();
            }

            if((isset($service->unit_id))) {

                foreach ($customers as $key => $customer_id) {
                    $srvc                =  new Customer_has_service();
                    $srvc->customer_id   = $customer_id;
                    $srvc->service_id    = $service->id;
                    $srvc->service_rate  = $service->rate;
                    $srvc->status        = 1;
                    $srvc->save();
    
                    if( (isset($srvc_itms) ) && ($srvc_itms != null) ){  // unit_id : 2 = items
                        foreach ($srvc_itms as $key => $items) {
                            $itm                = new Customer_has_item();
                            $itm->customer_id   = $customer_id;
                            $itm->service_id    = $items->service_id;
                            $itm->item_id       = $items->item_id;
                            $itm->item_rate     = $items->item_rate;
                            $itm->status        = 1;
                            $itm->save();
                        }
                    }
                }
            }
        }
        return;
    }

    public function store(Request $request)
    {

        request()->validate([
            'name'      => 'required|min:3|unique:services,name',
            'qty'       => 'required|numeric|min:1',
            'rate'      => 'required|numeric|min:0',
        ]);

        
        /// service rate validation
        if($request['unit_id']!=2){
            $this->validate($request,[
                // it is commented because of no service served
                // 'rate'      => 'required|numeric|min:1',
            ]);
        }

      
        /// setting inputs and arrays
        $input          = $request->all();
    
       
        $items          = $request['items'];
        $item_rates     = $request['item_rates'];
        $item_status    = $request['item_status'];
        $item_addons    = $request['item_addons'];

        $check = 0; $k = 0;

        // validating item rates conditions
        if( $items ){
            foreach($items as $key => $value){
                // echo "item rate: ".$item_rates[$key]."<br>";
                if(isset($item_status[$key])){
                    if(($request['unit_id']==2) && ($item_rates[$key] <=0)){
                        // echo "less than equal to";
                        $check = 2; $k = $key; break;   ///unit_id: 2 items
                    }
                    if(!(isset($item_rates[$key])) ){
                        $check = 1; $k = $key;          ///unit_id: 1 KG
                        // echo "isset";
                        break; 
                    }
                    if($item_rates[$key] <0 ){
                        // echo "less than";
                        $check = 1; $k = $key; break; 
                    }
                    
                }
            }
        }

        // validating item rates
        if($check==1){
            $this->validate($request,
                [
                    'item_rates'                    => 'required|array',
                    'item_rates.'.$k                => 'required|numeric|min:0'
                ],
                [
                    'item_rates.'.$k.'.required'    => 'Please enter rate of item '.($k+1),
                    'item_rates.'.$k.'.min'         => 'Please enter rate of item '.($k+1).' greater than 0',
                ]
            );
        }elseif($check==2){
            $this->validate($request,
                [
                    'item_rates'                    => 'required|array',
                    'item_rates.'.$k                =>'required|numeric|min:1'
                ],
                [
                    'item_rates.'.$k.'.required'    => 'Please enter rate of item '.($k+1),
                    'item_rates.'.$k.'.min'         => 'Please enter rate of item '.($k+1).' greater than 1',
                ]
            );
        }
        
        
        $input          = $request->all();
        // validating image for mobile is exist or not
        if($request['image']){
            $this->validate($request,[
                'image'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);
            $image          = $request->file('image');
            $new_name       = rand().'.'.$image->getClientOriginalExtension();
                            $image->move(public_path("uploads/services"),$new_name);
            $input['image'] = $new_name;
            
        }

        // validating web_image for web is exist or not
        if($request['web_image']){
            $this->validate($request,[
                'web_image'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);
            $image              = $request->file('web_image');
            $new_name           = rand().'.'.$image->getClientOriginalExtension();
                                $image->move(public_path("uploads/services"),$new_name);
            $input['web_image'] = $new_name;
            
        }

                
        try {
            // Transaction
            $exception = DB::transaction(function()  use ($request,$input,$items,$item_rates,$item_status,$item_addons) {
        
                $data           = service::create($input);
                $service_id     = $data['id'];

                // inserting service items and its addons
                if($data){
                    if( $items ){
                        foreach($items as $key => $value){
                            if(isset($item_status[$key])){
                                if(!($item_rates[$key])){
                                    $item_rates[$key]=0;
                                }
                                $val             =  new Service_has_item();
                                $val->service_id = $service_id;
                                $val->item_id    = $value;
                                $val->item_rate  = $item_rates[$key];
                                $val->item_status= $item_status[$key];
                                $val->save();
                                

                                // $keys = array();
                                if(isset($item_addons[$key])){
                                    $item_id = $value;
                                    foreach($item_addons[$key] as $addon_id =>$v){
                                        $addon               =  new Service_has_addon();
                                        $addon->service_id   = $service_id;
                                        $addon->item_id      = $item_id;
                                        $addon->addon_id     = $addon_id;
                                        $addon->save();
                                    }
                                }
                                
                            }else{
                                continue;
                            }
                        }

                    }
                }

                // add this service to all retail customer
                $srvc =  $this->str_cstmr_srvc($data);
            });
            
            if(is_null($exception)) {
                return redirect()
                    ->route('services.index')
                    ->with('success','Service '.$request['name'] .' added successfully.');

            } else {
                throw new Exception;
            }
        }catch(\Exception $e) {
            app('App\Http\Controllers\MailController')->send_exception($e);
            return response()->json(['error'=>[0=>"Something went wrong."]]);
        }
    }
    //start khadeeja's edit
      // Define a method to update the status of a service based on the provided request
    public function updateStatus(Request $request)
    {
      // Find the service by its ID
       $service = Service::find($request->id);
    
      // Check if the service is found
      if ($service) {
        // Update the status of the service
         // Save the changes to the database
         Service::where('id',$request->id)->update(['status'=>$request->buttonstatus]);
        
        // Check if the status is 0
        if ($request->buttonstatus == 0) {
          
            // If status is 0, delete corresponding records from customer_has_service table
            Customer_has_service::where('service_id', $service->id)->delete();
        }
        else{
           
                $customerjoin = DB::table('customer_types')
                ->join('customers','customer_type_id','=','customer_types.id')
                ->where('customer_types.name','Retail')
                ->select('customers.id')
                ->get();
             
                    foreach($customerjoin as $data)
                    {
                       
                    $newCustomer = new Customer_has_service;
                    $newCustomer->customer_id = $data->id;
                    $newCustomer->service_id = $service->id;
                    $newCustomer->service_rate = $service->rate;
                    $newCustomer->save();
                    }
            
        }
        // Return a JSON response indicating success
        return response()->json(['success' => true]);
      }   
      else {
        // Return a JSON response indicating failure with a message
        return response()->json(['success' => false, 'message' => 'Service not found.']);
      }
    }
    //end khadeeja's edit
     public function show($id)
    {

        $data               = DB::table('services')
                                ->orderBy('services.created_at','DESC')
                                ->leftjoin('units', 'units.id', '=', 'services.unit_id')
                                ->select('services.*','units.name as unit_name')
                                ->where('services.id', $id)
                                ->first();

        $units              = Unit::pluck('name','id')->all();

        $addons             = DB::table('addons')
                                ->select('addons.id as addon_id','addons.name as addon_name')
                                ->get()
                                ->all();

        $items              = DB::table('items')
                                ->select('items.id as item_id','items.name as item_name')
                                ->get()
                                ->all();

        $selected_items     = DB::table('service_has_items')
                                ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                                ->select('items.id',
                                        'items.name as item_name',
                                        'service_has_items.item_rate as item_rate',
                                        'service_has_items.item_status')
                                ->where('service_has_items.service_id', $id)
                                ->get()
                                ->all();

        $selected_addons    = DB::table('service_has_addons')
                                ->leftjoin('addons', 'addons.id', '=', 'service_has_addons.addon_id')
                                ->select('service_has_addons.item_id',
                                         'service_has_addons.addon_id',
                                         'addons.name as addon_name')
                                ->where('service_has_addons.service_id', $id)
                                ->get()
                                ->all();

                                // dd($selected_addons);
             

        return view('services.show',compact('data','selected_items','selected_addons'));
    }

    public function addonsName($data)
    {
        $val = explode('|',  $data) ; 
        $addonArray = array();
        foreach($val as $key=>$value){
            $addonArray[]=DB::table('addons')
            ->where('addons.id', $value)
            ->pluck('name','id')->first();
        }
        return implode(" | ",$addonArray);
    }

    public function edit($id)
    {
        $data           = DB::table('services')
                            ->where('services.id', $id)
                            ->first();

        $units          = Unit::pluck('name','id')->all();

        $addons         = DB::table('addons')
                            ->orderBy('addons.name','ASC')
                            ->select('addons.id as addon_id','addons.name as addon_name')
                            ->get()
                            ->all();

        $items          = DB::table('items')
                            ->orderBy('items.name','ASC')
                            ->select('items.id as item_id','items.name as item_name')
                            ->get()
                            ->all();
                            

        $selected_items = DB::table('service_has_items')
                            ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                            ->orderBy('items.name','ASC')
                            ->select('items.id as item_id',
                                    'service_has_items.service_id as service_id',
                                    'service_has_items.item_rate as item_rate',
                                    'service_has_items.item_status')
                            ->where('service_has_items.service_id', $id)
                            ->get()
                            ->all();
                        
    
        
        $selected_addons = DB::table('service_has_addons')
                            ->orderBy('addons.name','ASC')
                            ->leftjoin('addons', 'addons.id', '=', 'service_has_addons.addon_id')
                            ->select('service_has_addons.service_id',
                                     'service_has_addons.item_id as item_id',
                                     'service_has_addons.addon_id',
                                     'addons.name as addon_name')
                            ->where('service_has_addons.service_id', $id)
                            ->get();
        $selected_add   = array();

        if(!$selected_addons->isEmpty())
        {
            foreach($selected_addons as $addon)
            {
                $selected_add[$addon->item_id][$addon->addon_id] = array(
                    'addon_id' => $addon->addon_id,
                    'addon_name' => $addon->addon_name
                );
            }
        }                         
                   
        return view('services.edit',compact('data','units','addons','items','selected_items','selected_addons', 'selected_add'));
    }


    public function update(Request $request, $id)
    {
        $data           = service::findOrFail($id);
        $this->validate($request,[
            'name'      => 'required|min:3|unique:services,name,'.$id,
            'qty'       => 'required|numeric|min:1',
            'rate'      => 'required|numeric|min:0',
        ]);

        if($request['unit_id']!=2){
            $this->validate($request,[
                // 'rate'      => 'required|numeric|min:1',
            ]);
        }
        // $upd = $data->update($request->all());

        $input          = $request->all();
        $service_id     = $id;
        $items          = $request['items'];
        $item_rates     = $request['item_rates'];
        $item_status    = $request['item_status'];
        $item_addons    = $request['item_addons'];

        $check = 0; $k = 0;
        if( $items ){
            foreach($items as $key => $value){
                // echo "item rate: ".$item_rates[$key]."<br>";
                if(isset($item_status[$key])){
                    if(($request['unit_id']==2) && ($item_rates[$key] <=0)){
                        // echo "less than equal to";
                        $check = 2; $k = $key; break; 
                    }
                    if(!(isset($item_rates[$key])) ){
                        $check = 1; $k = $key; 
                        // echo "isset";
                        break; 
                    }
                    if($item_rates[$key] <0 ){
                        // echo "less than";
                        $check = 1; $k = $key; break; 
                    }
                    
                }
            }
        }

        if($check==1){
            $this->validate($request,
                [
                    'item_rates'                    => 'required|array',
                    'item_rates.'.$k                => 'required|numeric|min:0'
                ],
                [
                    'item_rates.'.$k.'.required'    => 'Please enter rate of item '.($k+1),
                    'item_rates.'.$k.'.min'         => 'Please enter rate of item '.($k+1).' greater than 0',
                ]
            );
        }elseif($check==2){
            $this->validate($request,
                [
                    'item_rates'                    => 'required|array',
                    'item_rates.'.$k                =>'required|numeric|min:1'
                ],
                [
                    'item_rates.'.$k.'.required'    => 'Please enter rate of item '.($k+1),
                    'item_rates.'.$k.'.min'         => 'Please enter rate of item '.($k+1).' greater than 1',
                ]
            );
        }

        // BEGIN :: update the image for mobile of service
        if(!empty($input['image'])){
            $this->validate($request,[
                'image'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
            
            if($data['image']!=""){
                if(file_exists(public_path('uploads/services/'.$data['image']))){
                    unlink(public_path('uploads/services/'.$data['image']));
                }
                
            }

            $image = $request->file('image');
            $new_name=rand().'.'.$image->getClientOriginalExtension();
            $image->move(public_path("uploads/services"),$new_name);
            $input['image'] = $new_name;
            
        }else{
            $input['image'] = $data['image'];
        }
        // END :: update the image for mobile of service

        // BEGIN :: update the image for web of service
        if(!empty($input['web_image'])){
            $this->validate($request,[
                'web_image'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
            
            if($data['web_image']!=""){
                if(file_exists(public_path('uploads/services/'.$data['web_image']))){
                    unlink(public_path('uploads/services/'.$data['web_image']));
                }
                
            }

            $image = $request->file('web_image');
            $new_name=rand().'.'.$image->getClientOriginalExtension();
            $image->move(public_path("uploads/services"),$new_name);
            $input['web_image'] = $new_name;
            
        }else{
            $input['web_image'] = $data['web_image'];
        }
        // END :: update the image for web of service

        try {
            // Transaction
            $exception = DB::transaction(function()  use ($service_id,$id,$data,$request,$input,$items,$item_rates,$item_status,$item_addons) {

                $upd = $data->update($input);

                //BEGIN :: update the service rate in all retail customer only

            
                    // get all retail customer
                    $customers      = Customer::where('customers.customer_type_id',1)->pluck('id')->all();

                    // update service rate of all retail customer only
                    $cus_services   = Customer_has_service::whereIn('customer_has_services.customer_id',$customers)  // only retailer:1
                                        ->Where('customer_has_services.service_id',$id)
                                        ->update(['service_rate' => $input['rate']]);


                //END   :: update the service rate in all retail customer only

                if($upd){

                    if( $items ){
                        DB::table("service_has_items")->where('service_id', '=', $id)->delete();
                        DB::table("service_has_addons")->where('service_id', '=', $id)->delete();
                    

                        foreach($items as $key => $value){
                            if(isset($item_status[$key])){

                                if(!($item_rates[$key])){
                                    $item_rates[$key]=0;
                                }

                                $val                =  new Service_has_item();
                                $val->service_id    = $service_id;
                                $val->item_id       = $value;
                                $val->item_rate     = $item_rates[$key];
                                $val->item_status   = $item_status[$key];
                                $val->save();
                                

                                // $keys = array();
                                if(isset($item_addons[$key])){
                                    $item_id = $value;
                                    foreach($item_addons[$key] as $addon_id =>$v){
                                        $addon               =  new Service_has_addon();
                                        $addon->service_id   = $service_id;
                                        $addon->item_id      = $item_id;
                                        $addon->addon_id     = $addon_id;
                                        $addon->save();
                                        
                                    }
                                }
                                
                            }else{
                                continue;
                            }
                        }

                        // dlt all entries from customer_has_items table because this table has only those service items whose unit_id=2 = items
                        // while unit_id = 1 and 3 are managed from customer_has_service table
                        DB::table("customer_has_items")
                                ->where('customer_has_items.service_id', '=',$service_id)
                                ->whereIn('customer_has_items.customer_id',$customers)
                                ->delete();



                        // if service unit  = items then items will also be stored in customer_has_items table
                        // otherwise for unit = (KG, Piece) rate will be manage from customer_has_services table
                        if((isset($request['unit_id'])) && ($request['unit_id'] == 2)  ){  // unit_id : 2 = items
                            $srvc_itms  = null;
                            $srvc_itms  = Service_has_item::where('service_has_items.service_id',$service_id)->get();

                            foreach ($customers as $key => $customer_id) {
                                if( (isset($srvc_itms) ) && ($srvc_itms != null) ){  // unit_id : 2 = items
                                    foreach ($srvc_itms as $key => $items) {
                                        $itm                = new Customer_has_item();
                                        $itm->customer_id   = $customer_id;
                                        $itm->service_id    = $items->service_id;
                                        $itm->item_id       = $items->item_id;
                                        $itm->item_rate     = $items->item_rate;
                                        $itm->status        = 1;
                                        $itm->save();
                                    }
                                }
                            }
                        }
                    }


                }
            });
            
            if(is_null($exception)) {
                return redirect()
                    ->route('services.index')
                    ->with('success','Service '.$request['name'] .' updated successfully.');

            } else {
                throw new Exception;
            }
        }catch(\Exception $e) {
            dd($e);
            app('App\Http\Controllers\MailController')->send_exception($e);
            return response()->json(['error'=>[0=>$e]]);
        }

        
    }

    public function destroy(Request $request)
    {
        $ids        = $request->ids;

        $data       = Service::find($ids);

        $chk        = DB::table('order_has_services')
                        ->select('order_has_services.id')
                        ->where('order_has_services.service_id',$ids)
                        ->first();

        $chk1       = DB::table('wash_house_has_services')
                        ->select('wash_house_has_services.id')
                        ->where('wash_house_has_services.service_id',$ids)
                        ->first();

        if( (!(isset($chk->id)))  && (!(isset($chk1->id))) ){

            if(!(isset($chk->id))){
                $data_addon     = DB::table("service_has_addons")->where('service_id', '=', $ids)->delete();
                $data_item      = DB::table("service_has_items")->whereIn('service_id',explode(",",$ids))->delete();
                $data_service   = DB::table("services")->whereIn('id',explode(",",$ids))->delete();
                if($data['image']!=""){
                    if(file_exists(public_path('uploads/services/'.$data['image']))){
                        unlink(public_path('uploads/services/'.$data['image']));
                    }
                }
                return response()->json(['success'=>"deleted successfully."]);
            }
        }
        return response()->json(['error'=>"This service cannot be deleted"]);

    }

}
