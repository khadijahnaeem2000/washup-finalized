<?php
namespace App\Http\Controllers;

use DB;
use DataTables;
use App\Models\Zone;
use App\Models\Area;
use Illuminate\Http\Request;
use App\Models\Zone_has_area;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;

class ZoneController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:zone-list', ['only' => ['index','show']]);
         $this->middleware('permission:zone-create', ['only' => ['create','store']]);
         $this->middleware('permission:zone-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:zone-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('zones.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data = Zone::select(
                                'zones.id',
                                'zones.name',
                                 DB::raw('@srno  := @srno  + 1 AS srno')
                            )
                        ->get();
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="zones/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="zones/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('zone_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);
    }

    public function create()
    {
        $areas         = DB::table('areas')
                            ->whereNotIn('id', function($q){
                                $q->select('area_id')->from('zone_has_areas');
                                })
                            ->select('areas.name','areas.id')
                            ->pluck('name','id')
                            ->all();
               
        return view('zones.create',compact('areas'));
    }

    public function store(Request $request)
    {
        request()->validate([
            'name'      => 'required|min:3|unique:zones,name',
            'area'      => 'required',
        ]);

        $data           = Zone::create($request->all());
        $area           = $request['area'];
        $zone_id        = $data['id'];

        if($data){
            foreach($area as $key => $value){
               $var             =  new Zone_has_area();
               $var->area_id    = $value;
               $var->zone_id    = $zone_id;
               $var->save();
            }
        }
      
        return redirect()
                ->route('zones.index')
                ->with('success','Zone '.$request['name'] .' added successfully.');
    }

     public function show($id)
    {
        $data           = DB::table('zones')
                            ->select('zones.*')
                            ->where('zones.id', $id)
                            ->first();

        $selected_areas = DB::table('zone_has_areas')
                            ->leftjoin('areas', 'areas.id', '=', 'zone_has_areas.area_id')
                            ->select('zone_has_areas.area_id','areas.name','areas.latitude','areas.longitude','areas.radius','areas.poly_points')
                            ->where('zone_has_areas.zone_id', $id)
                            ->get();

        return view('zones.show',compact('data','selected_areas'));
    }


    public function edit($id)
    {
        $data           = DB::table('zones')
                            ->where('zones.id', $id)
                            ->first();
                            $aa = $id ;


        $areas         = DB::table('areas')
                            ->whereNotIn('id', function($q) use ($id) {
                                $q->select('area_id')
                                ->where('zone_has_areas.zone_id', '!=' , $id)
                                ->from('zone_has_areas');
                                
                                })
                            ->select('areas.name','areas.id')
                            ->pluck('name','id')
                            ->all();

        $selected_areas = DB::table('zone_has_areas')
                            ->select('zone_has_areas.area_id')
                            ->where('zone_has_areas.zone_id', $id)
                            ->pluck('area_id')->all();

        return view('zones.edit',compact('data','areas','selected_areas'));
    }


    public function update(Request $request, $id)
    {
      
        $data           = Zone::findOrFail($id);
        $this->validate($request,[
            'name'      => 'required|min:3|unique:zones,name,'.$id,
            'area'      => 'required',
        ]);

        $upd            = $data->update($request->all());
        $zone_id        = $id;
        $area           = $request['area'];

        if($upd){
            DB::table("zone_has_areas")->where('zone_id', '=', $id)->delete();
            foreach($area as $key => $value){
               $var             =  new Zone_has_area();
               $var->area_id    = $value;
               $var->zone_id    = $zone_id;
               $var->save();
            }
        }

        return redirect()
                ->route('zones.index')
                ->with('success','Zone '.$request['name'] .' updated successfully.');
    }

    public function destroy(Request $request)
    {
        $ids        = $request->ids;

        $chk        =   DB::table('hub_has_zones')
                        ->select('hub_has_zones.id')
                        ->where('hub_has_zones.zone_id',$ids)
                        ->first();


        if(!(isset($chk->id))){
                          DB::table("zone_has_areas")->whereIn('zone_id',explode(",",$ids))->delete();
            $data       = DB::table("zones")->whereIn('id',explode(",",$ids))->delete();
            return response()->json(['success'=>$data." Zone deleted successfully."]);
        }
        return response()->json(['error'=>"This zone cannot be deleted"]);
    }




}
