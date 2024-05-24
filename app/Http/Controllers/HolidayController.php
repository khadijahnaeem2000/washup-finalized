<?php


namespace App\Http\Controllers;
use DB;
use DataTables;
use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HolidayController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:holiday-list', ['only' => ['index','show']]);
         $this->middleware('permission:holiday-create', ['only' => ['create','store']]);
         $this->middleware('permission:holiday-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:holiday-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('holidays.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data = DB::table('holidays')
                    ->orderBy('holidays.created_at','DESC')
                    ->select(
                                'holidays.*',
                                DB::raw('@srno  := @srno  + 1 AS srno')
                            )
                    ->get();
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="holidays/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="holidays/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('holiday_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);
    }

    public function create()
    {
        return view('holidays.create');
    }

    public function store(Request $request)
    {
        request()->validate([
            'name'          => 'required|min:3|unique:holidays,name',
            'holiday_date'  => 'required',
        ]);
        
        $data               = Holiday::create($request->all());
        return redirect()
                ->route('holidays.index')
                ->with('success','Holiday '.$request['name'] .' added successfully.');
    }

     public function show($id)
    {

        $data               =  DB::table('holidays')
                                ->orderBy('holidays.created_at','DESC')
                                ->select('holidays.*')
                                ->where('holidays.id', $id)
                                ->first();


        return view('holidays.show',compact('data'));
    }


    public function edit($id)
    {
        $data               = DB::table('holidays')
                                ->where('holidays.id', $id)
                                ->first();

        return view('holidays.edit',compact('data'));
    }


    public function update(Request $request, $id)
    {
        $data               = Holiday::findOrFail($id);
        
        $this->validate($request,[
            'name'          => 'required|min:3|unique:holidays,name,'.$id,
            'holiday_date'  => 'required',
        ]);

        $upd                = $data->update($request->all());

        return redirect()
                ->route('holidays.index')
                ->with('success','Holiday '.$request['name'] .' updated successfully.');
    }

    public function destroy(Request $request)
    {
        $ids                = $request->ids;
        $data               = DB::table("holidays")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>$data." Holiday deleted successfully."]);
    }




}
