<?php
namespace App\Http\Controllers;

use DB;
use DataTables;
use App\Models\Zone;
use App\Models\Service;
use App\Models\Wash_house;
use Illuminate\Http\Request;
use App\Models\Distribution_hub;
use App\Models\Wash_house_has_hub;
use App\Models\Wash_house_has_zone;
use App\Models\Wash_house_has_user;
use App\Models\Wash_house_has_addon;
use App\Http\Controllers\Controller;
use App\Models\Wash_house_has_service;

class Wash_houseController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:wash_house-list', ['only' => ['index','show']]);
         $this->middleware('permission:wash_house-create', ['only' => ['create','store']]);
         $this->middleware('permission:wash_house-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:wash_house-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('wash_houses.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data = wash_house::orderBy('id','DESC')
                    ->select('wash_houses.*')
                    ->select('id',
                             'name',
                              DB::raw('CONCAT(wash_houses.capacity,  "  pieces") as capacity'),
                              DB::raw('@srno  := @srno  + 1 AS srno')
                            )
                    ->get();

        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="wash_houses/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="wash_houses/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                     
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('wash_house_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);

    }

    public function fetch_zones(Request $request)
    {
        if($request->ajax()){
            $id =$request->wash_house_id;
            if($id == 0){
                $zones      =   DB::table('hub_has_zones')
                                ->leftjoin('zones', 'zones.id', '=', 'hub_has_zones.zone_id')
                                ->select('zones.name','zones.id')
                                ->where('hub_has_zones.hub_id', $request->hub_id)
                                ->whereNotIn('hub_has_zones.zone_id', function($q){
                                        $q->select('zone_id')->from('wash_house_has_zones');
                                        })
                                ->get()
                                ->all();
                
            }else{
                $zones      =   DB::table('hub_has_zones')
                                ->leftjoin('zones', 'zones.id', '=', 'hub_has_zones.zone_id')
                                ->select('zones.name','zones.id')
                                ->where('hub_has_zones.hub_id',  $request->hub_id)
                                ->whereNotIn('hub_has_zones.zone_id', function($q)use ($id){
                                    $q->select('zone_id')->from('wash_house_has_zones')
                                    ->where('wash_house_has_zones.wash_house_id', '!=' , $id);
                                    })
                                ->get()
                                ->all();
            }
            
            if($zones){
                $data = view('wash_houses.ajax-zone',compact('zones'))->render();
                return response()->json(['data'=>$data]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
        }

    }
    
    public function create()
    {
        $zones          = Zone::pluck('name','id')->all();

        $users          = DB::table('model_has_roles')
                            ->leftjoin('users', 'users.id', '=', 'model_has_roles.model_id')
                            ->whereNotIn('users.id', function($q){
                                $q->select('user_id')->from('wash_house_has_users');
                            })
                            ->select('users.id as id','users.name as name')
                            // ->whereIn('model_has_roles.role_id',[5,6])
                            ->where('model_has_roles.role_id',7)
                            ->pluck('name','id')
                            ->all();


        $services       = DB::table('services')
                            ->orderBy('id')
                            ->select('services.id','services.name')
                            ->get()
                            ->all();


        $services       = DB::table('services')
                            ->orderBy('id')
                            ->select('services.id','services.name')
                            ->get()
                            ->all();

        $addons         = DB::table('addons')
                            ->orderBy('id')
                            ->select('addons.id','addons.name')
                            ->get()
                            ->all();
        $hubs           = Distribution_hub::pluck('name','id')->all();
        
        return view('wash_houses.create',compact('users','zones','services','hubs','addons'));
    }

    public function store(Request $request)
    {
        // request()->validate([
        //     'name'      => 'required|min:3|unique:wash_houses,name',
        //     'capacity'  => 'required',
        //     'zone'      => 'required',
        //     'hub_id'    => 'required',
        //     'service'   => 'required'
        // ]);

        $this->validate($request,
            [
                'name'              => 'required|min:3|unique:wash_houses,name',
                'capacity'          => 'required|numeric|min:1',
                'hub_id'            => 'required|min:1|not_in:0',
                'service'           => 'required',
                'zone'              => 'required|array',
                'zone.*'            => 'required|min:1|numeric',
            ],
            [
                'zone.*.numeric'     => 'No Record found- All zone of this Hub, are already assigned!',
            ]
        );

        $data           = wash_house::create($request->all());
        $wash_house_id  = $data['id'];
        $zone           = $request['zone'];
        $hub_id         = $request['hub_id'];
        $service        = $request['service'];
        $service        = $request['service'];
        $addon          = $request['addon'];
        $user           = $request['user'];

        if($data){
            if($service){
                foreach($service as $key => $value){
                    $var                =  new Wash_house_has_service();
                    $var->service_id    = $value;
                    $var->wash_house_id = $wash_house_id;
                    $var->save();
                }
            }

            if($user){
                foreach($user as $key => $value){
                    $var                =  new Wash_house_has_user();
                    $var->user_id       = $value;
                    $var->wash_house_id = $wash_house_id;
                    $var->save();
                }
            }

            if($addon){
                foreach($addon as $key => $value){
                    $var                =  new Wash_house_has_addon();
                    $var->addon_id      = $value;
                    $var->wash_house_id = $wash_house_id;
                    $var->save();
                }
            }

            if($zone){
                foreach($zone as $key => $value){
                    $var                =  new Wash_house_has_zone();
                    $var->zone_id       = $value;
                    $var->wash_house_id = $wash_house_id;
                    $var->save();
                }
            }

            if($hub_id){
                // foreach($hub as $key => $value){
                    $var =  new Wash_house_has_hub();
                    $var->hub_id        = $hub_id;
                    $var->wash_house_id = $wash_house_id;
                    $var->save();
                // }
            }
        }
       
        return redirect()->route('wash_houses.index')
                        ->with('success','wash_house '.$request['name']. ' added successfully.');
    }

    public function show($id)
    {
        $data       = DB::table('wash_houses')
                        ->select('wash_houses.*')
                        ->where('wash_houses.id', $id)
                        ->first();

        $zones      = DB::table('wash_house_has_zones')
                        ->leftjoin('zones', 'zones.id', '=', 'wash_house_has_zones.zone_id')
                        ->select('zones.id','zones.name','wash_house_has_zones.wash_house_id')
                        ->where('wash_house_has_zones.wash_house_id', $id)
                        ->get();

        $services     = DB::table('wash_house_has_services')
                        ->leftjoin('services', 'services.id', '=', 'wash_house_has_services.service_id')
                        ->select('services.id','services.name','wash_house_has_services.wash_house_id')
                        ->where('wash_house_has_services.wash_house_id', $id)
                        ->get();

        $addons     = DB::table('wash_house_has_addons')
                        ->leftjoin('addons', 'addons.id', '=', 'wash_house_has_addons.addon_id')
                        ->select('addons.id','addons.name','wash_house_has_addons.wash_house_id')
                        ->where('wash_house_has_addons.wash_house_id', $id)
                        ->get();

        $users     = DB::table('wash_house_has_users')
                        ->leftjoin('users', 'users.id', '=', 'wash_house_has_users.user_id')
                        ->select('users.id','users.name','wash_house_has_users.wash_house_id')
                        ->where('wash_house_has_users.wash_house_id', $id)
                        ->get();
        
        $hubs       = DB::table('wash_house_has_hubs')
                        ->leftjoin('distribution_hubs', 'distribution_hubs.id', '=', 'wash_house_has_hubs.hub_id')
                        ->select('distribution_hubs.id','distribution_hubs.name','wash_house_has_hubs.wash_house_id')
                        ->where('wash_house_has_hubs.wash_house_id', $id)
                        ->get();
        
  
        // dd($days);
        return view('wash_houses.show',
                    compact('data',
                            'zones',
                            'services',
                            'hubs',
                            'addons',
                            'users'
                        )
                    );
    }
 
    public function edit($id)
    {
        $data               = wash_house::find($id);
        
        $hubs               = Distribution_hub::pluck('name','id')->all();

        $services           = DB::table('services')
                                ->orderBy('id')
                                ->select('services.id','services.name')
                                ->get()
                                ->all();

        $addons             = DB::table('addons')
                                ->orderBy('id')
                                ->select('addons.id','addons.name')
                                ->get()
                                ->all();

        $users              = DB::table('model_has_roles')
                                ->leftjoin('users', 'users.id', '=', 'model_has_roles.model_id')
                                ->whereNotIn('users.id', function($q) use ($id){
                                    $q->select('user_id')->from('wash_house_has_users')
                                    ->where('wash_house_has_users.wash_house_id', '!=' , $id);
                                    })
                                ->select('users.id as id','users.name as name')
                                // ->where('model_has_roles.role_id',2)
                                ->where('model_has_roles.role_id',7)
                               
                                ->pluck('name','id')
                                ->all();

        $selectedZones      = DB::table('wash_house_has_zones')
                                ->select('zone_id')
                                ->where('wash_house_has_zones.wash_house_id', $id)
                                ->pluck('zone_id')->all();

        $selectedUsers      = DB::table('wash_house_has_users')
                                ->select('user_id')
                                ->where('wash_house_has_users.wash_house_id', $id)
                                ->pluck('user_id')->all();




        $selectedServices   = DB::table('wash_house_has_services')
                                ->select('service_id')
                                ->where('wash_house_has_services.wash_house_id', $id)
                                ->get()
                                ->all();

        $selectedAddons     = DB::table('wash_house_has_addons')
                                ->select('addon_id')
                                ->where('wash_house_has_addons.wash_house_id', $id)
                                ->get()
                                ->all();

        $selectedHubs       =  DB::table('wash_house_has_hubs')
                                ->select('hub_id')
                                ->where('wash_house_has_hubs.wash_house_id', $id)
                                ->pluck('hub_id')->all();

                                // $selectedHubs[0];
        $zones              =   DB::table('hub_has_zones')
                                ->leftjoin('zones', 'zones.id', '=', 'hub_has_zones.zone_id')
                                ->select('zones.name','zones.id')
                                ->where('hub_has_zones.hub_id', $selectedHubs[0])
                                ->whereNotIn('hub_has_zones.zone_id', function($q)use ($id){
                                    $q->select('zone_id')->from('wash_house_has_zones')
                                      ->where('wash_house_has_zones.wash_house_id', '!=' , $id);
                                    })
                                ->pluck('name','id')
                                ->all();
        // $zones              = Zone::pluck('name','id')->all();
        // dd($zones);


 
        
        return view('wash_houses.edit',
                    compact('data',
                            'zones',
                            'services',
                            'hubs',
                            'users',
                            'selectedUsers',
                            'selectedZones',
                            'selectedServices',
                            'selectedHubs',
                            'addons',
                            'selectedAddons'
                        )
                    );
    }


    public function update(Request $request, $id)
    {
        $data = wash_house::find($id);
        $this->validate($request,
            [
                'name'              => 'required|min:3|unique:wash_houses,name,'.$id,
                'capacity'          => 'required|numeric|min:1',
                'hub_id'            => 'required|min:1|not_in:0',
                'service'           => 'required',
                'zone'              => 'required|array',
                'zone.*'            => 'required|min:1|numeric',
            ],
            [
                'zone.*.numeric'     => 'No Record found- All zone of this Hub, are already assigned!',
            ]
        );
      

        $upd = $data->update($request->all());

        $wash_house_id  = $id;
        $zone           = $request['zone'];
        $hub_id         = $request['hub_id'];
        $service        = $request['service'];
        $addon          = $request['addon'];
        $user          = $request['user'];


        if($data){
            DB::table("wash_house_has_zones")->where('wash_house_id', '=', $id)->delete();
            DB::table("wash_house_has_hubs")->where('wash_house_id', '=', $id)->delete();
            DB::table("wash_house_has_services")->where('wash_house_id', '=', $id)->delete();
            DB::table("wash_house_has_addons")->where('wash_house_id', '=', $id)->delete();
            DB::table("wash_house_has_users")->where('wash_house_id', '=', $id)->delete();
            if($service){
                foreach($service as $key => $value){
                    $var            =  new Wash_house_has_service();
                    $var->service_id= $value;
                    $var->wash_house_id= $wash_house_id;
                    $var->save();
                }
            }
            if($user){
                foreach($user as $key => $value){
                    $var                =  new Wash_house_has_user();
                    $var->user_id       = $value;
                    $var->wash_house_id = $wash_house_id;
                    $var->save();
                }
            }

            if($addon){
                foreach($addon as $key => $value){
                    $var                =  new Wash_house_has_addon();
                    $var->addon_id      = $value;
                    $var->wash_house_id = $wash_house_id;
                    $var->save();
                }
            }

            if($zone){
                foreach($zone as $key => $value){
                    $var                =  new Wash_house_has_zone();
                    $var->zone_id       = $value;
                    $var->wash_house_id = $wash_house_id;
                    $var->save();
                }
            }

            if($hub_id){
                // foreach($hub as $key => $value){
                    $var                =  new Wash_house_has_hub();
                    $var->hub_id        = $hub_id;
                    $var->wash_house_id = $wash_house_id;
                    $var->save();
                // }
            }
        }

        return redirect()->route('wash_houses.index')
            ->with('success','Wash House '.$request['name']. ' updated successfully');
    }


    public function destroy(Request $request)
    { 
        $id         = $request->ids;
        $wash_house = wash_house::find($id);
        $chk        = DB::table('rate_lists')
                        ->select('rate_lists.id')
                        ->where('rate_lists.wash_house_id',$id)
                        ->first();

        if(!(isset($chk->id))){

            DB::table("wash_house_has_zones")->where('wash_house_id', '=', $id)->delete();
            DB::table("wash_house_has_hubs")->where('wash_house_id', '=', $id)->delete();
            DB::table("wash_house_has_services")->where('wash_house_id', '=', $id)->delete();
            DB::table("wash_house_has_addons")->where('wash_house_id', '=', $id)->delete();
            DB::table("wash_house_has_users")->where('wash_house_id', '=', $id)->delete();
            $data = DB::table("wash_houses")->whereIn('id',explode(",",$id))->delete();
            return response()->json(['success'=>$data." Wash House deleted successfully."]);
        }
        return response()->json(['error'=>"This wash house cannot be deleted"]);
    }

      

    
}
