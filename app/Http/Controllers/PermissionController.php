<?php


namespace App\Http\Controllers;
use App\Models\Permission;
use Illuminate\Http\Request;
use DB;
use DataTables;

class PermissionController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:permission-list', ['only' => ['index','show']]);
         $this->middleware('permission:permission-create', ['only' => ['create','store']]);
         $this->middleware('permission:permission-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('permissions.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data = DB::table('permissions')
                    ->orderBy('permissions.name')
                    ->select(
                                'permissions.id',
                                'permissions.name',
                                 DB::raw('@srno  := @srno  + 1 AS srno')
                            )
                    ->get();
                    
        return DataTables::of($data)
                ->addColumn('action',function($data){
                 return 
                        '<div class="btn-group btn-group">
                          
                            <a class="btn btn-secondary btn-sm" href="permissions/'.$data->id.'" id="'.$data->id.'">
                                <i class="fa fa-eye"></i>
                            </a>
                         
                        </div>';
                    })
                ->rawColumns(['','action'])
                ->make(true);

    }

    public function create()
    {
        return view('permissions.create');
    }


    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required|unique:permissions,name',
        ]);
        Permission::create($request->all());
        return redirect()->route('permissions.create')
                        ->with('success','Permission '.$request['name']. ' added successfully.');
    }

     public function show($id)
    {
      
        $data   =  DB::table('permissions')
                    ->select('permissions.*')
                    ->where('permissions.id', $id)
                    ->first();


        return view('permissions.show',compact('data'));


        // return view('permissions.show',compact('permission'));
    }


    public function edit(Permission $permission)
    {
        return redirect()
        ->route('permissions.index');

        // return view('permissions.edit',compact('permission'));
    }


    public function update(Request $request,$id)
    {
        $permission = Permission::findOrFail($id);
        request()->validate([
            'name' => 'required|unique:permissions,name,'. $id,
        ]);

        $permission->update($request->all());
        return redirect()->route('permissions.index')
                        ->with('success','Permission '.$request['name']. ' updated successfully');
    }

    public function destroy(Permission $permission)
    {
        $ids = $request->ids;
        $data = DB::table("permissions")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>$data." Permission deleted successfully."]);
    }

   
}
