<?php

namespace App\Http\Controllers;
use DB;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Vehicle_type;
use App\Http\Controllers\Controller;

class Vehicle_typeController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:vehicle_type-list', ['only' => ['index','show']]);
         $this->middleware('permission:vehicle_type-create', ['only' => ['create','store']]);
         $this->middleware('permission:vehicle_type-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:vehicle_type-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('vehicle_types.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data = DB::table('vehicle_types')
                ->orderBy('created_at','DESC')
                ->select('vehicle_types.id',
                         'vehicle_types.name',
                         DB::raw('CASE WHEN vehicle_types.hanger = 1 THEN  "Yes"  ELSE "No" END AS hanger'),
                         DB::raw('@srno  := @srno  + 1 AS srno')
                        )
                ->get();
               
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="vehicle_types/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="vehicle_types/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('vehicle_type_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);

    }

    public function create()
    {
        return view('vehicle_types.create');
    }

    public function store(Request $request)
    {
        request()->validate([
            'name'      => 'required|min:3|unique:vehicle_types,name',
        ]);
        $data           = Vehicle_type::create($request->all());
      
        return redirect()
                ->route('vehicle_types.index')
                ->with('success','Vehicle Type '.$request['name'] .' added successfully.');
    }

     public function show($id)
    {
        $data           = DB::table('vehicle_types')
                            ->select('vehicle_types.id',
                                    'vehicle_types.name',
                                     DB::raw('CASE WHEN vehicle_types.hanger = 1 THEN  "Yes"  ELSE "No" END AS hanger')
                                    )
                            ->where('vehicle_types.id', $id)
                            ->first();

        return view('vehicle_types.show',compact('data'));
    }


    public function edit($id)
    {
        $data       = DB::table('vehicle_types')
                        ->where('vehicle_types.id', $id)
                        ->first();

        return view('vehicle_types.edit',compact('data'));
    }


    public function update(Request $request, $id)
    {
      
        $data       = Vehicle_type::findOrFail($id);
        $this->validate($request,[
            'name'  => 'required|min:3|unique:vehicle_types,name,'.$id,
        ]);
        $inputs     = $request->all();
        
        if(!(array_key_exists('hanger',$inputs))){
            $inputs['hanger'] = 0;
        }
        
        $upd        = $data->update($inputs);

        return redirect()
                ->route('vehicle_types.index')
                ->with('success','Vehicle type '.$request['name'] .' updated successfully.');
    }

    public function destroy(Request $request)
    {
        $ids        = $request->ids;
        $data       = DB::table("vehicle_types")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>$data." Vehicle type deleted successfully."]);
    }




}
