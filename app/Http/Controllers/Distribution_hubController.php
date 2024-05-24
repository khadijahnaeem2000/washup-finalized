<?php
namespace App\Http\Controllers;

use DB;
use DataTables;
use App\Models\Day;
use App\Models\Zone;
use App\Models\Rider;
use App\Models\Time_slot;
use App\Models\Hub_has_day;
use App\Models\Hub_has_user;
use App\Models\Hub_has_zone;
use Illuminate\Http\Request;
use App\Models\Hub_has_rider;
use App\Models\Distribution_hub;
use App\Models\Hub_has_time_slot;
use App\Http\Controllers\Controller;

class Distribution_hubController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:distribution_hub-list', ['only' => ['index','show']]);
         $this->middleware('permission:distribution_hub-create', ['only' => ['create','store']]);
         $this->middleware('permission:distribution_hub-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:distribution_hub-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('distribution_hubs.index');
    }
    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data           = distribution_hub::orderBy('id','DESC')
                            ->select(
                                        'distribution_hubs.id',
                                        'distribution_hubs.name',
                                        DB::raw('@srno  := @srno  + 1 AS srno')
                                    )
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
    public function create()
    {

        $days           = Day::pluck('name','id')
                            ->all();

                            // 3: Rider's Supervisor
                            // 4: Operation Supervisor
                            // 5: Tagger
                            // 6: Packer
                            
        $users          = DB::table('model_has_roles')
                            ->leftjoin('users', 'users.id', '=', 'model_has_roles.model_id')
                            ->whereNotIn('users.id', function($q){
                                $q->select('user_id')->from('hub_has_users');
                            })
                            ->select('users.id as id','users.name as name')
                            // ->where('model_has_roles.role_id',3)
                            ->whereIn('model_has_roles.role_id',[2,3,4,5,6]) 
                            ->pluck('name','id')
                            ->all();

        $zones          = DB::table('zones')
                            ->whereNotIn('id', function($q){
                                $q->select('zone_id')->from('hub_has_zones');
                                })
                            ->select('zones.name','zones.id')
                            ->pluck('name','id')
                            ->all();
        
        $riders         = DB::table('riders')
                            ->whereNotIn('id', function($q){
                                $q->select('rider_id')->from('hub_has_riders');
                                })
                            ->select('riders.name','riders.id')
                            ->pluck('name','id')
                            ->all();

        $time_slots     = DB::table('time_slots')
                            ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                            ->pluck('name','id')
                            ->all();

        return 
            view('distribution_hubs.create',
                compact(
                    'users',
                    'zones',
                    'riders',
                    'days',
                    'time_slots'
                )
            );
    }

    public function store(Request $request)
    {

        $this->validate($request,
            [
                'name'                  => 'required|min:3|unique:distribution_hubs,name',
                'address'               => 'required',
                'zone'                  => 'required',
                'rider'                 => 'required',
                'day'                   => 'required',
                'location'              => 'required|array',
                'location.*'            => 'required|numeric|min:1|not_in:0',
                'time_slot'             => 'required|array',
                'time_slot.*'           => 'required|distinct',
            ],
            [
                'location.*.required'   => 'Please add atleast one location!',
                'location.*.min'        => 'Please use value greater than 0!',
                'location.*.not_in'     => '0 is not allowed!',
                'time_slot.*.required'  => 'Please add atleast one Time slots!',
                'time_slot.*.distinct'  => 'Please use different Time slots!',
            ]
        );

        $data           = Distribution_hub::create($request->all());
        $hub_id         = $data['id'];
        $rider          = $request['rider'];
        $zone           = $request['zone'];
        $day            = $request['day'];
        $user           = $request['user'];
        $time_slot      = $request['time_slot'];
        $location       = $request['location'];

        if($data){
            if($rider){
                foreach($rider as $key => $value){
                    $var                = new Hub_has_rider();
                    $var->rider_id      = $value;
                    $var->hub_id        = $hub_id;
                    $var->save();
                }
            }

            if($user){
                foreach($user as $key => $value){
                    $var                =  new Hub_has_user();
                    $var->user_id       = $value;
                    $var->hub_id = $hub_id;
                    $var->save();
                }
            }

            if($zone){
                foreach($zone as $key => $value){
                    $var                =  new Hub_has_zone();
                    $var->zone_id       = $value;
                    $var->hub_id        = $hub_id;
                    $var->save();
                }
            }

            if($time_slot){
                foreach($time_slot as $key => $value){
                    $var                = new Hub_has_time_slot();
                    $var->time_slot_id  = $value;
                    $var->location      = $location[$key];
                    $var->hub_id        = $hub_id;
                    $var->save();
                }
            }

            if($day){
                foreach($day as $key => $value){
                    $var                = new Hub_has_day();
                    $var->day_id        = $value;
                    $var->hub_id        = $hub_id;
                    $var->save();
                }
            }
        }
       
        return redirect()->route('distribution_hubs.index')
                        ->with('success','Distribution hub '.$request['name']. ' added successfully.');
    }

    public function show($id)
    {
        $data       = DB::table('distribution_hubs')
                        ->select('distribution_hubs.*')
                        ->where('distribution_hubs.id', $id)
                        ->first();

        $zones      = DB::table('hub_has_zones')
                        ->leftjoin('zones', 'zones.id', '=', 'hub_has_zones.zone_id')
                        ->select('zones.id','zones.name','hub_has_zones.hub_id')
                        ->where('hub_has_zones.hub_id', $id)
                        ->get();

        $riders     = DB::table('hub_has_riders')
                        ->leftjoin('riders', 'riders.id', '=', 'hub_has_riders.rider_id')
                        ->select('riders.id','riders.name','hub_has_riders.hub_id')
                        ->where('hub_has_riders.hub_id', $id)
                        ->get();
        
        $days       = DB::table('hub_has_days')
                        ->leftjoin('days', 'days.id', '=', 'hub_has_days.day_id')
                        ->select('days.id','days.name','hub_has_days.hub_id')
                        ->where('hub_has_days.hub_id', $id)
                        ->get();

        $time_slots = DB::table('hub_has_time_slots')
                        ->leftjoin('time_slots', 'time_slots.id', '=', 'hub_has_time_slots.time_slot_id')
                        ->select('time_slots.id','time_slots.name','time_slots.start_time','time_slots.end_time','hub_has_time_slots.hub_id')
                        ->where('hub_has_time_slots.hub_id', $id)
                        ->get();

        $users      = DB::table('hub_has_users')
                        ->leftjoin('users', 'users.id', '=', 'hub_has_users.user_id')
                        ->select('users.id','users.name','hub_has_users.hub_id')
                        ->where('hub_has_users.hub_id', $id)
                        ->get();
        
  
        return view('distribution_hubs.show',
                    compact('data',
                            'zones',
                            'riders',
                            'days',
                            'users',
                            'time_slots'
                        )
                    );
    }
 
    public function edit($id)
    {
        
        $data           = distribution_hub::findOrFail($id);
        $zones          = Zone::pluck('name','id')->all();
        $riders         = Rider::pluck('name','id')->all();
        $days           = Day::pluck('name','id')->all();

        $zones          = DB::table('zones')
                            ->whereNotIn('id', function($q) use ($id){
                                $q->select('zone_id')->from('hub_has_zones')
                                ->where('hub_has_zones.hub_id', '!=' , $id);
                                })
                            ->select('zones.name','zones.id')
                            ->pluck('name','id')
                            ->all();

                            // 3: Rider's Supervisor
                            // 4: Operation Supervisor
                            // 5: Tagger
                            // 6: Packer

        $users          = DB::table('model_has_roles')
                            ->leftjoin('users', 'users.id', '=', 'model_has_roles.model_id')
                            ->whereNotIn('users.id', function($q) use ($id){
                                $q->select('user_id')->from('hub_has_users')
                                ->where('hub_has_users.hub_id', '!=' , $id);
                                })
                            ->select('users.id as id','users.name as name')
                            // ->where('model_has_roles.role_id',3)
                            ->whereIn('model_has_roles.role_id',[2,3,4,5,6])
                            ->pluck('name','id')
                            ->all();


        $riders         = DB::table('riders')
                            ->whereNotIn('id', function($q) use ($id){
                                $q->select('rider_id')->from('hub_has_riders')
                                ->where('hub_has_riders.hub_id', '!=' , $id);
                                })
                            ->select('riders.name','riders.id')
                            ->pluck('name','id')
                            ->all();

        $time_slots     = DB::table('time_slots')
                            ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                            ->pluck('name','id')
                            ->all();

        $selectedUsers  = DB::table('hub_has_users')
                            ->select('user_id')
                            ->where('hub_has_users.hub_id', $id)
                            ->pluck('user_id')->all();


        $selectedZones  = DB::table('hub_has_zones')
                            ->select('zone_id')
                            ->where('hub_has_zones.hub_id', $id)
                            ->pluck('zone_id')
                            ->all();

        $selectedRiders = DB::table('hub_has_riders')
                            ->select('rider_id')
                            ->where('hub_has_riders.hub_id', $id)
                            ->pluck('rider_id')
                            ->all();

        $selectedDays   =  DB::table('hub_has_days')
                            ->select('day_id')
                            ->where('hub_has_days.hub_id', $id)
                            ->pluck('day_id')
                            ->all();

        $selectedTime_slots = DB::table('hub_has_time_slots')
                                ->where('hub_has_time_slots.hub_id', $id)
                                ->get()
                                ->all();

 
        
        return view('distribution_hubs.edit',
                    compact('data',
                            'days',
                            'zones',
                            'users',
                            'riders',
                            'time_slots',
                            'selectedDays',
                            'selectedUsers',
                            'selectedZones',
                            'selectedRiders',
                            'selectedTime_slots'
                        )
                    );
    }

    public function fetch_timeslot(Request $request)
    {
        // dd($request->ids);
        if($request->ajax()){
            // $a = "["
            if(isset($request->selected_id)){
                $state = $request->selected_id;
            }else{
                $state = null;
            }
           if(($request->ids)){
                $time_slots     = DB::table('time_slots')
                                    ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                    ->whereNotIn('time_slots.id', $request->ids)
                                    ->pluck('name','id')
                                    ->all();
               
           }else{
                $time_slots     = DB::table('time_slots')
                                    ->select('id',DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as name'))
                                    ->pluck('name','id')
                                    ->all();
           }

     
            if($time_slots){
                $time_slots = view('distribution_hubs.ajax-timeslots',compact('time_slots','state'))->render();
                return response()->json(['data'=>$time_slots]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
            
        }

    }


    public function update(Request $request, $id)
    {
        
        $data = distribution_hub::findOrFail($id);
        $this->validate($request,
            [
                'name'                  => 'required|min:3|unique:distribution_hubs,name,'.$id,
                'address'               => 'required',
                'zone'                  => 'required',
                'rider'                 => 'required',
                'day'                   => 'required',
                'location'              => 'required|array',
                'location.*'            => 'required|numeric|min:1|not_in:0',
                'time_slot'             => 'required|array',
                'time_slot.*'           => 'required|distinct',
            ],
            [
                'location.*.required'   => 'Please add atleast one location!',
                'location.*.min'        => 'Please use value greater than 0!',
                'location.*.not_in'     => '0 is not allowed!',
                'time_slot.*.required'  => 'Please add atleast one Time slots!',
                'time_slot.*.distinct'  => 'Please use different Time slots!',
            ]
        );

        $inputs     = $request->all();
        if(!(array_key_exists('cus_address',$inputs))){
            $inputs['cus_address'] = 0;
        }

        $upd        = $data->update($inputs);

        $hub_id     = $data['id'];

        $day        = $request['day'];
        $user       = $request['user'];
        $zone       = $request['zone'];
        $rider      = $request['rider'];
        $location   = $request['location'];
        $time_slot  = $request['time_slot'];

        if($data){
            DB::table("hub_has_riders")->where('hub_id', '=', $id)->delete();
            DB::table("hub_has_zones")->where('hub_id', '=', $id)->delete();
            DB::table("hub_has_days")->where('hub_id', '=', $id)->delete();
            DB::table("hub_has_time_slots")->where('hub_id', '=', $id)->delete();
            DB::table("hub_has_users")->where('hub_id', '=', $id)->delete();
            if($rider){
                foreach($rider as $key => $value){
                    $var                =  new Hub_has_rider();
                    $var->rider_id      = $value;
                    $var->hub_id        = $hub_id;
                    $var->save();
                }
            }

            if($user){
                foreach($user as $key => $value){
                    $var                =  new Hub_has_user();
                    $var->user_id       = $value;
                    $var->hub_id        = $hub_id;
                    $var->save();
                }
            }

            if($zone){
                foreach($zone as $key => $value){
                    $var                = new Hub_has_zone();
                    $var->zone_id       = $value;
                    $var->hub_id        = $hub_id;
                    $var->save();
                }
            }

            if($time_slot){
                foreach($time_slot as $key => $value){
                    $var                = new Hub_has_time_slot();
                    $var->time_slot_id  = $value;
                    $var->location      = $location[$key];
                    $var->hub_id        = $hub_id;
                    $var->save();
                }
            }

            if($day){
                foreach($day as $key => $value){
                    $var                = new Hub_has_day();
                    $var->day_id        = $value;
                    $var->hub_id        = $hub_id;
                    $var->save();
                }
            }
        }

        return redirect()->route('distribution_hubs.index')
            ->with('success','Distribution hub '.$request['name']. ' updated successfully');
    }


    public function destroy(Request $request)
    { 
        $ids                = $request->ids;
        // $distribution_hub   = distribution_hub::find($ids);


        $chk        = DB::table('wash_house_has_hubs')
                        ->select('wash_house_has_hubs.id')
                        ->where('wash_house_has_hubs.hub_id',$ids)
                        ->first();

        $chk1       = DB::table('orders')
                        ->select('orders.id')
                        ->where('orders.hub_id',$ids)
                        ->first();

        if( (!(isset($chk->id)))  && (!(isset($chk1->id))) ){

            DB::table("hub_has_riders")->where('hub_id', '=', $ids)->delete();
            DB::table("hub_has_zones")->where('hub_id', '=', $ids)->delete();
            DB::table("hub_has_days")->where('hub_id', '=', $ids)->delete();
            DB::table("hub_has_time_slots")->where('hub_id', '=', $ids)->delete();
            DB::table("hub_has_users")->where('hub_id', '=', $ids)->delete();

            $data = DB::table("distribution_hubs")->whereIn('id',explode(",",$ids))->delete();
            
            return response()->json(['success'=>$data." Distribution hub deleted successfully."]);

        }
        return response()->json(['error'=>"This distribution hub cannot be deleted"]);
    }

      

    
}
