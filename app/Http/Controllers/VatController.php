<?php


namespace App\Http\Controllers;

use DB;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Vat;
use App\Models\Delivery_charge;
use App\Http\Controllers\Controller;


class VatController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:vat-list', ['only' => ['index','show']]);
         $this->middleware('permission:vat-create', ['only' => ['create','store']]);
         $this->middleware('permission:vat-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:vat-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('delivery_charges.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data = DB::table('vats')
                ->orderBy('vats.created_at','DESC')
                ->select(
                            'vats.id',
                            DB::raw('CONCAT(vats.vat,  "%  ") as vat'),
                            DB::raw('@srno  := @srno  + 1 AS srno')
                        )
                ->get();

        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="vats/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="vats/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);

    }

    public function create()
    {
        return view('vats.create');
    }

    public function store(Request $request)
    {
        request()->validate([
            'vat'          => 'required|numeric|min:0',
        ]);
        
        $data = Vat::create($request->all());
      
        return redirect()
                ->route('delivery_charges.index')
                ->with('success','VAT added successfully.');
    }

     public function show($id)
    {

        $data   =  DB::table('vats')
                    ->select('vats.*')
                    ->where('vats.id', $id)
                    ->first();


        return view('vats.show',compact('data'));
    }


    public function edit($id)
    {
        $data   = DB::table('vats')
                    ->where('vats.id', $id)
                    ->first();

        return view('vats.edit',compact('data'));
    }


    public function update(Request $request, $id)
    {
        $data   = Vat::findOrFail($id);
        $this->validate($request,[
            'vat'          => 'required|numeric|min:0',
        ]);

        $upd = $data->update($request->all());

        return redirect()
                ->route('delivery_charges.index')
                ->with('success','VAT updated successfully.');
    }

    public function destroy(Request $request)
    {
        $ids    = $request->ids;
        $data   = DB::table("vats")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>" VAT deleted successfully."]);
    }




}
