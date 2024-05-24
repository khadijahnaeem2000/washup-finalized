<?php


namespace App\Http\Controllers;

use DB;
use DataTables;
use App\Models\Addon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\NotificationController;

class AddonController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:addon-list', ['only' => ['index','show']]);
         $this->middleware('permission:addon-create', ['only' => ['create','store']]);
         $this->middleware('permission:addon-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:addon-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
       
        // $order_id   = 1;
        // $result     = (new NotificationController)->complaint_msg($order_id);

        return view('addons.index');
    }

    public function list()
    {
        // DB::statement(DB::raw('set @srno=0'));
        $data = DB::table('addons')
                ->orderBy('addons.created_at','DESC')
                ->select(
                            'addons.*',
                            // DB::raw('@srno  := @srno  + 1 AS srno')
                        )
                ->get();

        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="addons/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="addons/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('addon_delete') .'" data-id="'.$data->id.'">
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
        return view('addons.create');
    }

    public function store(Request $request)
    {
        request()->validate([
            'name'      => 'required|min:3|unique:addons,name',
            'rate'      => 'required|numeric|min:0',
        ]);
        
        $data = Addon::create($request->all());
      
        return redirect()
                ->route('addons.index')
                ->with('success','Addon '.$request['name'] .' added successfully.');
    }

     public function show($id)
    {

        $data   =  DB::table('addons')
                    ->orderBy('addons.created_at','DESC')
                    ->select('addons.*')
                    ->where('addons.id', $id)
                    ->first();


        return view('addons.show',compact('data'));
    }


    public function edit($id)
    {
        $data   = DB::table('addons')
                    ->where('addons.id', $id)
                    ->first();

        return view('addons.edit',compact('data'));
    }


    public function update(Request $request, $id)
    {
        $data   = Addon::findOrFail($id);
        $this->validate($request,[
            'name'      => 'required|min:3|unique:addons,name,'.$id,
            'rate'      => 'required|numeric|min:0',
        ]);

        $upd = $data->update($request->all());

        return redirect()
                ->route('addons.index')
                ->with('success','Addon '.$request['name'] .' updated successfully.');
    }

    public function destroy(Request $request)
    {
        $ids    = $request->ids;
        $data   = DB::table("addons")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>" Addon deleted successfully."]);
    }




}
