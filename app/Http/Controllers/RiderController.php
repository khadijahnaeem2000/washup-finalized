<?php


namespace App\Http\Controllers;

use DB;
use Hash;
use Auth;
use DataTables;
use App\Models\Zone;
use App\Models\Rider;
use App\Models\Vehicle_type;
use Illuminate\Http\Request;
use App\Models\Rider_has_zone;
use App\Models\Rider_incentives;
use App\Http\Controllers\Controller;

class RiderController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:rider-list', ['only' => ['index','show']]);
         $this->middleware('permission:rider-create', ['only' => ['create','store']]);
         $this->middleware('permission:rider-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:rider-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('riders.index');
    }

    public function list()
    {

        // ->whereNotIn('hub_has_zones.zone_id', function($q)use ($id){
        //     $q->select('zone_id')->from('wash_house_has_zones')
        //     ->where('wash_house_has_zones.wash_house_id', '!=' , $id);
        //     })
        // DB::statement(DB::raw('set @srno=0'));
        $data       = Rider::orderBy('riders.name','ASC')
                        ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                        ->leftjoin('rider_incentives', 'rider_incentives.id', '=', 'riders.rider_incentives')
                        // ->leftjoin('rider_has_zones', 'rider_has_zones.rider_id', '=', 'riders.id')
                        // ->leftjoin('zones', 'zones.id', '=', 'rider_has_zones.zone_id')
                        ->leftJoin('rider_has_zones AS rhz', function($join){
                                $join->on('rhz.rider_id', '=', 'riders.id')
                                ->leftjoin('zones', 'zones.id', '=', 'rhz.zone_id')
                                ->where('rhz.priority', '=', 1);
                        })
                        ->select(
                                    'riders.id',
                                    'riders.name',
                                    'riders.contact_no',
                                    'riders.max_loc',
                                    'riders.max_route',
                                    'riders.max_pick',
                                    'rhz.id as status',
                                    'zones.name as zone_name',
                                    
                                    DB::raw('CONCAT("Active") as status'),
                                    'rider_incentives.name as rider_incentives_name',
                                    // 'zones.name as zone_name',
                                    'vehicle_types.name as vehicle_type_name',
                                    DB::raw(
                                            '(CASE 
                                                WHEN forgot = "0" THEN "No" 
                                                WHEN forgot = "1" THEN "Yes" 
                                             END) AS forget'
                                            ),

                                    // DB::raw(
                                    //     '(CASE 
                                    //         WHEN rhz.priority = 1 THEN "Active" 
                                    //         ELSE "Inactive" 
                                    //         END) AS status'
                                    //     ),

                                    // DB::raw(
                                    //     '(CASE 
                                    //         WHEN rider_has_zones.priority = 1 THEN zones.name
                                    //         ELSE "--" 
                                    //         END) AS zone_name'
                                    //     ),
                                  
                                    DB::raw('CONCAT(riders.max_drop_size,  "  Kg  ") as max_drop_size'),
                                    DB::raw('CONCAT(riders.max_drop_weight,  "  Kg  ") as max_drop_weight'),
                                    // DB::raw('@srno  := @srno  + 1 AS srno')
                                ) 
                            // ->where('rider_has_zones.priority', 1)
                                
                        ->get();
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                        <div class="btn-group btn-group">
                            <a class="btn btn-secondary btn-sm" href="riders/'.$data->id.'">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a class="btn btn-secondary btn-sm" href="riders/'.$data->id.'/edit" id="'.$data->id.'">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <button
                                class="btn btn-danger btn-sm delete_all"
                                data-url="'. url('rider_delete') .'" data-id="'.$data->id.'">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>';
                    })
                ->rawColumns(['','action'])
                ->addIndexColumn()
                ->make(true);
    }

    public function create()
    {
        $zones              = Zone::pluck('name','id')->all();
        $vehicle_types      = Vehicle_type::pluck('name','id')->all();
        $rider_incentives         = Rider_incentives::pluck('name','id')->all();
        return view('riders.create',compact('zones','vehicle_types','rider_incentives'));
    }

    public function store(Request $request)
    {


        $this->validate($request, 
            [
                'name'              => 'required|min:3|regex:/^([^0-9]*)$/',
                'username'          => 'required|unique:riders,username',
                'password'          => 'required',
                'cnic_no'           => 'required|unique:riders,cnic_no',
                'contact_no'        => 'required|unique:riders,contact_no',
                'color_code'        => 'unique:riders,color_code',
                'vehicle_reg_no'    => 'unique:riders,vehicle_reg_no',
                'max_loc'           => 'required|numeric|min:1',
                'max_route'         => 'required|numeric|min:1',
                'max_pick'          => 'required|numeric|min:1',
                'max_drop_weight'   => 'required|min:1',
                'max_drop_size'     => 'required|min:1',
                'vehicle_reg_no'    => 'required',
                'rider_incentives'        => 'required|exists:rider_incentives,id',
                'zone'              => 'required|array',
                'zone.*'            => 'required|distinct',
            ],
            [
                'name.regex'         => 'Special characters and numbers are not allowed.',
                'max_loc.required'   => 'The max location field is required.',
                'zone.required'      => 'Please add atleast one zone.',
                'zone.*.distinct'    => 'Please use different zone.',
            ]
        );
        $zone            = $request['zone'];
        $priority        = $request['priority'];
        
        foreach($zone as $key => $zone_id){
            $data       = DB::table('rider_has_zones')
                            ->leftjoin('zones', 'zones.id', '=', 'rider_has_zones.zone_id')
                            ->select('zones.name')
                            ->where('rider_has_zones.zone_id', $zone_id)
                            ->where('rider_has_zones.priority', 1)      // 1 = Primary && 0 = Secondary
                            ->first();

            if( $data ){
                if($priority[$key] == 1 ){
                    return redirect()
                            ->back()
                            ->withInput($request->input())
                            ->with('permission',$data->name. ' is primary zone of other rider.');
                }
                
            }
        }

        if($request['image']){
            $this->validate($request,['image'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
            $image              = $request->file('image');
            $new_name           = rand().'.'.$image->getClientOriginalExtension();
                                  $image->move(public_path("uploads/riders"),$new_name);

            $input              = $request->all();
            $input['image']     = $new_name;
            $input['password']  = Hash::make($input['password']);
            $rider              = rider::create($input);
        }else{
            $input              = $request->all();
            $input['password']  = Hash::make($input['password']);
            $rider              = rider::create($input);
        }
        $rider_id               = $rider['id'];

        if($rider){
            if($zone){
                foreach($zone as $key => $value){
                    if(!($priority[$key])){
                        $priority[$key]= 0;
                    }
                    $var                = new Rider_has_zone();
                    $var->rider_id      = $rider_id;
                    $var->zone_id       = $value;
                    $var->priority      = $priority[$key];
                    $var->save();
                }
            }
        }


        return redirect()->route('riders.index')
                        ->with('success','rider '.$request['name']. ' added successfully.');
    }

    public function show($id)
    {
        $days       = fetch_days();
        $data       = DB::table('riders')
                            ->leftjoin('vehicle_types', 'vehicle_types.id', '=', 'riders.vehicle_type_id')
                            ->leftjoin('rider_incentives', 'rider_incentives.id', '=', 'riders.rider_incentives')
                            ->select('riders.*',
                                    'vehicle_types.name as vehicle_type_name',
                                    'rider_incentives.name as rider_incentives_name',
                                    )
                            ->where('riders.id', $id)
                            ->first();

        $zones      = DB::table('rider_has_zones')
                            ->leftjoin('zones', 'zones.id', '=', 'rider_has_zones.zone_id')
                            ->select('zones.id',
                                        'zones.name','rider_has_zones.priority')
                            ->where('rider_has_zones.rider_id', $id)
                            ->orderBy('rider_has_zones.priority','DESC')
                            ->orderBy('rider_has_zones.id')
                            ->get()
                            ->all();
                            
      
        return view('riders.show',compact('data','zones'));
    }
 
    public function edit($id)
    {
        $data           = rider::find($id);
        $zones          = Zone::pluck('name','id')->all();
        $vehicle_types  = Vehicle_type::pluck('name','id')->all();
        $selected_zones = DB::table('rider_has_zones')
                            ->where('rider_has_zones.rider_id', $id)
                            ->get()
                            ->all();
         $rider_incentives         = Rider_incentives::pluck('name','id')->all();                    

        return view('riders.edit',compact('data','zones','vehicle_types','selected_zones','rider_incentives'));
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, 
            [
                'name'              => 'required|min:3|regex:/^([^0-9]*)$/',
                'username'          => 'required|unique:riders,username,'.$id,
                'cnic_no'           => 'required|unique:riders,cnic_no,'.$id,
                'contact_no'        => 'required|unique:riders,contact_no,'.$id,
                'color_code'        => 'unique:riders,color_code,'.$id,
                'vehicle_reg_no'    => 'unique:riders,vehicle_reg_no,'.$id,
                'max_loc'           => 'required|numeric|min:1',
                'max_route'         => 'required|numeric|min:1',
                'max_pick'           => 'required|numeric|min:1',
                'max_drop_weight'   => 'required|min:1',
                'vehicle_reg_no'    => 'required',
                'zone'              => 'required|array',
                'zone.*'            => 'required|distinct',
                'rider_incentives'        => 'required|exists:rider_incentives,id',
            ],
            [
                'name.regex'         => 'Special characters and numbers are not allowed.',
                'max_loc.required'   => 'The max location field is required.',
                'zone.required'      => 'Please add atleast one zone.',
                'zone.*.distinct'    => 'Please use different zone.',
            ]
        );
        $zone           = $request['zone'];
        $priority       = $request['priority'];
        foreach($zone as $key => $zn_id){
            $data       = DB::table('rider_has_zones')
                            ->leftjoin('zones', 'zones.id', '=', 'rider_has_zones.zone_id')
                            ->select('zones.name')
                            ->where('rider_has_zones.zone_id', $zn_id)
                            ->where('rider_has_zones.priority', 1)      // 1 = Primary && 0 = Secondary
                            ->where('rider_has_zones.rider_id','!=', $id)
                            ->first();
            if( $data ){
                if($priority[$key] == 1 ){
                    return redirect()
                    ->back()
                    ->withInput($request->input())
                    ->with('permission',$data->name. ' is primary zone of other rider.');
                }
                
            }
        }

        $rider = rider::find($id);
        $input = $request->all();

        if(!(array_key_exists('status',$input))){
            $input['status'] = 0;
        }

        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
            $input['forgot']   = 0;
        }else{
            $input['password'] = $rider['password'];
            $input['forgot']   = $rider['forgot'];

        }
        
        if(!empty($input['image'])){
            $this->validate($request,[
                'image'        =>'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
            
            if($rider['image'] != ""){
                unlink(public_path('uploads/riders/'.$rider['image']));
            }

            $image          = $request->file('image');
            $new_name       = rand().'.'.$image->getClientOriginalExtension();
                              $image->move(public_path("uploads/riders"),$new_name);
            $input['image'] = $new_name;
            $rider->update($input);
        }else{
             $input['image'] = $rider['image'];
             $rider->update($input);
        }

        DB::table("rider_has_zones")->where('rider_id', '=', $id)->delete();
        $rider_id           = $id;
        $zone               = $request['zone'];
        $priority           = $request['priority'];

        if($rider){
            if($zone){
                foreach($zone as $key => $value){
                    if(!($priority[$key])){
                        $priority[$key]=0;
                    }
                    $var                =  new Rider_has_zone();
                    $var->rider_id      = $rider_id;
                    $var->zone_id       = $value;
                    $var->priority      = $priority[$key];
                    $var->save();
                }
            }
        }
        return redirect()->route('riders.index')
            ->with('success','rider '.$request['name']. ' updated successfully');
    }


    // public function destroy(Request $request)
    // { 
    //     $ids        = $request->ids;
    //     $rider      = Rider::find($ids);

    //     $chk        =   DB::table('hub_has_riders')
    //                     ->select('hub_has_riders.id')
    //                     ->where('hub_has_riders.rider_id',$ids)
    //                     ->first();


    //     if(!(isset($chk->id))){
    //         if($rider['image'] != ""){
    //             unlink(public_path('uploads/riders/'.$rider['image']));
    //         }
    //         $data1      = DB::table("rider_has_zones")->whereIn('rider_id',explode(",",$ids))->delete();
    //         $data       = DB::table("riders")->whereIn('id',explode(",",$ids))->delete();
                        
    //         return response()->json(['success'=>$data." rider deleted successfully."]);
    //     }
    //     return response()->json(['error'=>"This rider cannot be deleted"]);

    // }


    public function destroy(Request $request)
    { 
        
        
                            
                            
        $ids        = $request->ids;
        $rec        = DB::table('orders')
                            ->select('orders.id')
                            ->where('orders.pickup_rider_id',$ids)
                            ->orWhere('orders.delivery_rider_id',$ids)
                            ->first();
                            
        $rec1        = DB::table('route_plans')
                            ->select('route_plans.id')
                            ->where('route_plans.rider_id',$ids)
                            ->first();
                            
        if( (isset($rec->id)) ||  (isset($rec1->id)) ){
             return response()->json(['error'=>"This rider cannot be deleted, rides were scheduled on this rider"]);
        }else{
                            
                            
            $rider      = Rider::find($ids);
    
            $chk        =   DB::table('hub_has_riders')
                            ->select('hub_has_riders.id')
                            ->where('hub_has_riders.rider_id',$ids)
                            ->first();
    
    
            if(!(isset($chk->id))){
                if($rider['image'] != ""){
                    unlink(public_path('uploads/riders/'.$rider['image']));
                }
                $data1      = DB::table("rider_has_zones")->whereIn('rider_id',explode(",",$ids))->delete();
                $data       = DB::table("riders")->whereIn('id',explode(",",$ids))->delete();
                            
                return response()->json(['success'=>$data." rider deleted successfully."]);
            }
            return response()->json(['error'=>"This rider cannot be deleted"]);
        }

    }

      

    
}
