<?php
namespace App\Http\Controllers;

use DB;
use DataTables;
use App\Models\Area;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AreaController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:area-list', ['only' => ['index','show']]);
         $this->middleware('permission:area-create', ['only' => ['create','store']]);
         $this->middleware('permission:area-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:area-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('areas.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data   = DB::table('areas')
                    ->orderBy('created_at','DESC')
                    ->select('areas.id',
                            'areas.name',
                            'areas.latitude',
                            'areas.longitude',
                            DB::raw('@srno  := @srno  + 1 AS srno')
                            )
                    ->get();
               
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="areas/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="areas/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('area_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);
    }

    public function create()
    {
        $all_points     = DB::table('areas')
                            ->select('areas.id','areas.name','areas.poly_points')
                            ->get();

        return view('areas.create',compact('all_points'));
    }

    public function store(Request $request)
    {
        $this->validate($request, 
            [
                'name'                  => 'required|min:3|unique:areas,name',
                'poly_points'           => 'required|min:3|unique:areas,poly_points',
            ],
            [
                'name.required'         => 'Special characters and numbers are not allowed!',
                'poly_points.required'  => 'Please draw a complete area!',
            ]
        );


        $data   = Area::create($request->all());
      
        return  redirect()
                    ->route('areas.index')
                    ->with('success','Area '.$request['name'] .' added successfully.');
    }

     public function show($id)
    {
        $data           = DB::table('areas')
                            ->select('areas.*')
                            ->where('areas.id', $id)
                            ->first();

        $all_points     = DB::table('areas')
                            ->select('areas.id','areas.name','areas.poly_points')
                            ->where('areas.id','!=', $id)
                            ->get();

        return view('areas.show',compact('data','all_points'));
    }


    public function edit($id)
    {

        $all_points     = DB::table('areas')
                            ->select('areas.id','areas.name','areas.poly_points')
                            ->where('areas.id','!=', $id)
                            ->get();


        $data   = DB::table('areas')
                    ->where('areas.id', $id)
                    ->first();

        return view('areas.edit',compact('data','all_points'));
    }


    public function update(Request $request, $id)
    {
      
        $data   = Area::findOrFail($id);
        $this->validate($request, 
            [
                'name'                  => 'required|min:3|unique:areas,name,'.$id,
                'poly_points'           => 'required|min:3|unique:areas,poly_points,'. $id,
            ],
            [
                'name.required'         => 'Special characters and numbers are not allowed!',
                'poly_points.required'  => 'Please draw a complete area!',
            ]
        );
        
        $upd    = $data->update($request->all());

        return  redirect()
                    ->route('areas.index')
                    ->with('success','Area '.$request['name'] .' updated successfully.');
    }

    public function destroy(Request $request)
    {
        $ids        = $request->ids;
        $data       = DB::table("areas")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>$data." Area deleted successfully."]);
    }




}
