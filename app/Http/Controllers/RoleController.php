<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use DataTables;


class RoleController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:role-list', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('roles.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data = DB::table('roles')->orderBy('name','ASC')
                    ->orderBy('roles.id','DESC')
                    ->select(
                                'roles.id',
                                'roles.name',
                                DB::raw('@srno  := @srno  + 1 AS srno')
                            )
                    ->get();

        return DataTables::of($data)
                ->addColumn('action',function($data){
                return 
                    '<div class="btn-group btn-group">
                    
                        <a class="btn btn-secondary btn-sm" href="roles/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        
                     
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);
                // <button
                //             class="btn btn-danger btn-sm delete_all"
                //             data-url="'. url('addon_delete') .'" data-id="'.$data->id.'">
                //             <i class="fas fa-trash-alt"></i>
                //         </button>
    }

    public function create()
    {
        $permission = Permission::get();
        return view('roles.create',compact('permission'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name'          => 'required|unique:roles,name',
            'permission'    => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
                        ->with('success','Role '.$request['name']. ' added successfully.');
    }

    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();

        return view('roles.show',compact('role','rolePermissions'));
    }


    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        return view('roles.edit',compact('role','permission','rolePermissions'));
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name,'.$id,
            'permission' => 'required',
        ]);


        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));
        return redirect()->route('roles.index')
                        ->with('success','Role '.$request['name']. ' updated successfully');
    }
    
    public function destroy(Request $request)
    {
        $ids = $request->ids;
        if($ids==1){
            return response()->json(['error'=> 'This is logged in user role, cannot be deleted']);
        }else{
            $data = DB::table("roles")->whereIn('id',explode(",",$ids))->delete();
            return response()->json(['success'=>$data." Roles deleted successfully."]);
        }
    }
    
    
}
