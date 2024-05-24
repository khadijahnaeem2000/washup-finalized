<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer_type;
use DB;
use DataTables;

class Customer_typeController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:customer_type-list', ['only' => ['index','show']]);
         $this->middleware('permission:customer_type-create', ['only' => ['create','store']]);
         $this->middleware('permission:customer_type-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:customer_type-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('customer_types.index');
    }

    public function list()
    {
        $data       = DB::table('customer_types')
                        ->orderBy('created_at','DESC')
                        ->select('customer_types.id',
                                'customer_types.name')
                        ->get();
               
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="customer_types/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="customer_types/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                     
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('customer_type_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->addColumn('srno','')
                ->rawColumns(['srno','','action'])
                ->make(true);
    }

    public function create()
    {
        return view('customer_types.create');
    }

    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required|min:3|unique:customer_types,name',
        ]);
        $data = customer_type::create($request->all());
      
        return redirect()
                ->route('customer_types.index')
                ->with('success','Customer Type '.$request['name'] .' added successfully.');
    }

     public function show($id)
    {
        $data = DB::table('customer_types')
                ->select('customer_types.*')
                ->where('customer_types.id', $id)
                ->first();
        return view('customer_types.show',compact('data'));
    }


    public function edit($id)
    {
        $data= DB::table('customer_types')
                    ->where('customer_types.id', $id)
                    ->first();

        return view('customer_types.edit',compact('data'));
    }


    public function update(Request $request, $id)
    {
      
        $data = customer_type::findOrFail($id);
        $this->validate($request,[
            'name' => 'required|min:3|unique:customer_types,name,'.$id,
        ]);
        
        $upd = $data->update($request->all());

        return redirect()
                ->route('customer_types.index')
                ->with('success','Customer type '.$request['name'] .' updated successfully.');
    }

    public function destroy(Request $request)
    {
        $ids     = $request->ids;

        if($ids == 1 || $ids == 2 ){
            return response()->json(['error'=>"It is primary customer type and cannot be deleted."]);
        }else{
            $data = DB::table("customer_types")->whereIn('id',explode(",",$ids))->delete();
        }
   
        return response()->json(['success'=>$data." Customer Type deleted successfully."]);
    }




}
