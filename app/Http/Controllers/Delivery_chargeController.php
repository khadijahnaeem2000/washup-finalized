<?php


namespace App\Http\Controllers;

use DB;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Delivery_charge;
use App\Http\Controllers\Controller;


class Delivery_chargeController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:delivery_charge-list', ['only' => ['index','show']]);
         $this->middleware('permission:delivery_charge-create', ['only' => ['create','store']]);
         $this->middleware('permission:delivery_charge-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:delivery_charge-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('delivery_charges.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data = DB::table('delivery_charges')
                ->orderBy('delivery_charges.created_at','DESC')
                ->select(
                            'delivery_charges.*',
                            DB::raw('@srno  := @srno  + 1 AS srno')
                        )
                ->get();

        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="delivery_charges/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="delivery_charges/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);

    }

    public function create()
    {
        return view('delivery_charges.create');
    }

    public function store(Request $request)
    {
        request()->validate([
            'order_amount'          => 'required|numeric|min:0',
            'delivery_charges'      => 'required|numeric|min:0',
        ]);
        
        $data = Delivery_charge::create($request->all());
      
        return redirect()
                ->route('delivery_charges.index')
                ->with('success','Delivery charges added successfully.');
    }

     public function show($id)
    {

        $data   =  DB::table('delivery_charges')
                    ->orderBy('delivery_charges.created_at','DESC')
                    ->select('delivery_charges.*')
                    ->where('delivery_charges.id', $id)
                    ->first();


        return view('delivery_charges.show',compact('data'));
    }


    public function edit($id)
    {
        $data   = DB::table('delivery_charges')
                    ->where('delivery_charges.id', $id)
                    ->first();

        return view('delivery_charges.edit',compact('data'));
    }


    public function update(Request $request, $id)
    {
        $data   = Delivery_charge::findOrFail($id);
        $this->validate($request,[
            'order_amount'          => 'required|numeric|min:0',
            'delivery_charges'      => 'required|numeric|min:0',
        ]);

        $upd = $data->update($request->all());

        return redirect()
                ->route('delivery_charges.index')
                ->with('success','Delivery charges updated successfully.');
    }

    public function destroy(Request $request)
    {
        $ids    = $request->ids;
        $data   = DB::table("delivery_charges")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>" Addon deleted successfully."]);
    }




}
