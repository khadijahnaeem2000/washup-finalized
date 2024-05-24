<?php
namespace App\Http\Controllers;

use DB;
use DataTables;
use App\Models\Order;
use App\Models\Status;
use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Models\Complaint_nature;
use App\Http\Controllers\Controller;

class ComplaintController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:complaint-list', ['only' => ['index','show']]);
         $this->middleware('permission:complaint-create', ['only' => ['create','store']]);
         $this->middleware('permission:complaint-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:complaint-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('complaints.index');
    }

    public function list(Request $request)
    {
        // complaint list
        DB::statement(DB::raw('set @srno=0'));
        $rec       = Complaint::orderBy('complaints.created_at','DESC')
                        ->leftjoin('customers', 'customers.id', '=', 'complaints.customer_id')
                        ->leftjoin('complaint_natures', 'complaint_natures.id', '=', 'complaints.complaint_nature_id')
                        ->leftjoin('orders', 'orders.id', '=', 'complaints.order_id')
                        ->select(
                                    'complaints.id',
                                    DB::raw('(CASE 
                                        WHEN complaints.status_id = "1" THEN "Resolved" 
                                        ELSE "Not Resolved" 
                                        END) AS complaint_status'
                                    ),
                                    DB::raw('DATE_FORMAT(complaints.created_at, "%Y-%m-%d") as complaint_date'),
                                    //  'complaints.status_id as complaint_status',
                                    'customers.name',
                                    'customers.contact_no',
                                    'orders.id as order_id',
                                    'orders.pickup_date',
                                    // 'orders.delivery_date',
                                    // 'complaints.created_at as complaint_date',
                                    'complaint_natures.name as cmp_ntr_name',
                                    DB::raw('@srno  := @srno  + 1 AS srno'))
                                // ->where('complaints.status_id',1)
                        ->get();
            if(!($rec->isEmpty())){
                $details    = view('complaints.complaint_table',
                                    compact('rec'))
                                    ->render();
                return response()->json(['details'=>$details]);
            }

            return response()->json(['error'=>[0=>"No Complaint found!!!"]]);
       

    }

    public function trail_complaint($id){

        $data               = Complaint::orderBy('complaints.created_at','DESC')
                                ->leftjoin('orders', 'orders.id', '=', 'complaints.order_id')
                                ->leftjoin('customers', 'customers.id', '=', 'complaints.customer_id')
                                ->select(
                                        'complaints.id',
                                        'orders.id as order_id',
                                        'customers.id as customer_id',
                                        'customers.name',
                                        'customers.contact_no',
                                        'orders.pickup_date',
                                        'customers.permanent_note',
                                        'orders.order_note',
                                        'orders.delivery_date'
                                    )
                                ->find($id);

        if($data){

            // $histories      = DB::table('order_histories')
            //                     ->leftjoin('statuses', 'statuses.id', '=', 'order_histories.status_id')
            //                     ->leftjoin('users', 'users.id', '=', 'order_histories.created_by')
            //                     ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            //                     ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            //                     ->where('order_histories.order_id',$data['order_id'])
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
                                        ->where('order_histories.order_id', $data['order_id'])
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

            return view('complaints.trail',
                        compact('data',
                        'histories',)
                );
        }else{
            return redirect()->route('complaints.index')
                         ->with('permission','No Record Found!!!');
        }
      
    }
    public function order_list()
    {

        DB::statement(DB::raw('set @srno=0'));
        $data       = Order::orderBy('orders.id','DESC')
                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                        ->select('orders.id',
                                'customers.name',
                                'customers.contact_no',
                                'orders.pickup_date',
                                'orders.delivery_date',
                                DB::raw('@srno  := @srno  + 1 AS srno'))
                        ->get();

        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a  href="/complaints/complaint_add/'.$data->id.'" class="btn btn-primary btn-sm">
                            <i class="la la-plus"></i>
                        </a>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);
    }


    public function fetch_complaint_tags(Request $request)
    {
        if($request->ajax()){
           
                $tags      = DB::table('complaint_tags')
                                        ->select('id','name')
                                        ->where('complaint_tags.complaint_nature_id',$request->id)
                                        ->pluck("name","id")
                                        ->all();
                if ($tags){
                    $data = view('complaints.ajax-tag',compact('tags'))->render();
                    return response()->json(['data'=>$data]);
                }else{
                    return response()->json(['error'=>"Data not found"]);
                }

                
            
            
        }

    }
    public function create()
    {

        $data              = Order::orderBy('orders.id','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->select('orders.id',
                                             'orders.id as order_id',
                                             'orders.pickup_date',
                                             'orders.delivery_date',
                                             'customers.id as customer_id',
                                             'customers.name',
                                             'customers.contact_no'
                                            )
                                    ->find($id);
        $complaint_natures      = Complaint_nature::pluck('name','id')->all();
        $statuses               = DB::table('statuses')
                                    ->where('statuses.id', 1)
                                    // ->orWhere('statuses.id', 3)
                                    ->pluck('name','id')
                                    ->all();
                                    // ->take(1)
                                
        $time_slots             = DB::table('time_slots')
                                    ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                    ->pluck('name','id')
                                    ->all();
                
        return view('complaints.create',compact('statuses','data','complaint_natures'));
    }

    public function store(Request $request)
    {
        
        request()->validate([
            'customer_id'           => 'required',
            // 'order_id'              => 'required|unique:complaints,order_id',
            'complaint_nature_id'   => 'required',
            'complaint_tag_id'      => 'required',
        ]);
        if($request['image']){
            $input=$request->all();
            $images=array();
            if($files=$request->file('image')){
                $this->validate($request,[
                    'image' => 'required',
                    'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                    ]);
                foreach($files as $file){
                    $name=rand().'.'.$file->getClientOriginalExtension();
                    $file->move(public_path("uploads/complaints"),$name);
                    $images[]=$name;
                }
            }
    
            // $all_images = implode("|",$images);
            $input['image'] = implode("|",$images);
            $data = Complaint::create($input);
        }else{
            $data = Complaint::create($request->all());
        }

        if(isset($request->order_id)){
            (new NotificationController)->complaint_msg($request->order_id);
        }
       
        return redirect()->route('complaints.index')
                        ->with('success','complaint added successfully.');
    }

    public function show($id)
    {

        $data   = Complaint::orderBy('complaints.id','DESC')
                    ->leftjoin('customers', 'customers.id', '=', 'complaints.customer_id')
                    ->leftjoin('orders', 'orders.id', '=', 'complaints.order_id')
                    ->leftjoin('complaint_natures', 'complaint_natures.id', '=', 'complaints.complaint_nature_id')
                    ->leftjoin('complaint_tags', 'complaint_tags.id', '=', 'complaints.complaint_tag_id')
                    ->leftjoin('ratings as i_rating', 'i_rating.id', '=', 'orders.iron_rating')
                    ->leftjoin('ratings as s_rating', 's_rating.id', '=', 'orders.softner_rating')
                    ->where('complaints.id', $id)
                    ->select('complaints.*',
                                'orders.id as order_id',
                                'orders.pickup_date',
                                'orders.delivery_date',
                                'customers.id as customer_id',
                                'customers.name',
                                'customers.contact_no',
                                'i_rating.name as iron_rating',
                                's_rating.name as softner_rating',
                                'complaint_tags.name as tag_name',
                                'complaint_natures.name as nature_name',
                                DB::raw('(CASE 
                                WHEN complaints.status_id = "1" THEN "Resolved" 
                                ELSE "Not Resolved" 
                                END) AS complaint_status'),
                                DB::raw('DATE_FORMAT(complaints.created_at, "%Y-%m-%d") as complaint_date'),
                            )
                    ->first();
       

        return  view('complaints.show',
                    compact('data')
                   );
    }
 
    public function edit($id)
    {
        $data                   = Complaint::orderBy('complaints.id','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'complaints.customer_id')
                                    ->leftjoin('orders', 'orders.id', '=', 'complaints.order_id')
                                    ->leftjoin('ratings as i_rating', 'i_rating.id', '=', 'orders.iron_rating')
                                    ->leftjoin('ratings as s_rating', 's_rating.id', '=', 'orders.softner_rating')
                                    ->select('complaints.*',
                                             'orders.id as order_id',
                                             'orders.delivery_date',
                                             'customers.id as customer_id',
                                             'customers.name',
                                             'i_rating.name as iron_rating',
                                             's_rating.name as softner_rating',
                                             'customers.contact_no'
                                            )
                                    ->find($id);
                                   
        
        $complaint_natures      = Complaint_nature::pluck('name','id')->all();
        
      
        
        return view('complaints.edit',
                    compact('data',
                            'complaint_natures'
                        )
                    );
    }
    public function complaint_add($id)
    {
        $data              = Order::orderBy('orders.id','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->leftjoin('ratings as i_rating', 'i_rating.id', '=', 'orders.iron_rating')
                                    ->leftjoin('ratings as s_rating', 's_rating.id', '=', 'orders.softner_rating')
                                    ->select('orders.id',
                                             'orders.id as order_id',
                                             'orders.pickup_date',
                                             'orders.delivery_date',
                                             'customers.id as customer_id',
                                             'customers.name',
                                             'i_rating.name as iron_rating',
                                             's_rating.name as softner_rating',
                                             'customers.contact_no'
                                            )
                                    ->find($id);

                                    
                                   
        
        $complaint_natures      = Complaint_nature::pluck('name','id')->all();
      
        
        return view('complaints.create',
                    compact('data',
                            'complaint_natures'
                        )
                    );
    }
    public function resolve_complaint($id){
        $data = Complaint::find($id);
        // dd()
        if($data->status_id==1){
            
            return redirect()
                ->route('complaints.index')
                ->with('permission','Complaint already resovled .');
        }else{
            $input['status_id'] = 1;
            $data->update($input);
            return redirect()
                ->route('complaints.index')
                ->with('success','Complaint resovled successfully.');
        }
        
    }

    public function update(Request $request, $id)
    {
        $data = Complaint::find($id);
        request()->validate([
            'customer_id' => 'required',
            // 'order_id' => 'required|unique:complaints,order_id,'.$id,
            'complaint_nature_id' => 'required',
            'complaint_tag_id' => 'required',
        ]);

        $input = $request->all();

        if(!empty($input['image'])){
            $images =array();
            if($files=$request->file('image')){
                $this->validate($request,[
                    'image' => 'required',
                    'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                    ]);
                foreach($files as $file){
                    $name=rand().'.'.$file->getClientOriginalExtension();
                    $file->move(public_path("uploads/complaints"),$name);
                    $images[]=$name;
                }
            }

            // deleting old images
            if($data['image']!=""){
                $old_images     =  explode('|',$data['image']);
                foreach ($old_images as $key => $value) {
                    unlink(public_path('uploads/complaints/'.$value));
                }
            }
    
            $input['image'] =implode("|",$images);
            $data->update($input);
        }else{
             $input['image'] = $data['image'];
             $data->update($input);
        }

        return redirect()
                ->route('complaints.index')
                ->with('success','Data updated successfully.');
    }


 

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $complaint          = Complaint::find($ids);
        if($complaint['image']!=""){
            $old_images     =  explode('|',$complaint['image']);
            foreach ($old_images as $key => $value) {
                unlink(public_path('uploads/complaints/'.$value));
            }
        }
        $data               = DB::table("complaints")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>$data." complaint deleted successfully."]);
    }

      

    
}
