<?php
namespace App\Http\Controllers;
use DB;
use DataTables;
use App\Models\Addon;
use App\Models\Wash_house;
use Illuminate\Http\Request;
use App\Models\Addon_rate_list;
use App\Http\Controllers\Controller;


class Addon_rate_listController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:addon_rate_list-list', ['only' => ['index','show']]);
         $this->middleware('permission:addon_rate_list-create', ['only' => ['create','store']]);
         $this->middleware('permission:addon_rate_list-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:addon_rate_list-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $wash_houses        = Wash_house::pluck('name','id')->all();
        
        return view('addon_rate_lists.index',compact('wash_houses'));
    }

    public function list()
    {
        // DB::statement(DB::raw('set @srno=0'));
        $data   = Addon_rate_list::orderBy('addons.name','ASC')
                    ->leftjoin('addons', 'addons.id', '=', 'addon_rate_lists.addon_id')                
                    ->leftjoin('wash_houses', 'wash_houses.id', '=', 'addon_rate_lists.wash_house_id')
                    ->select(
                                'addon_rate_lists.*',
                                'addons.name as addon_name',
                                'wash_houses.name as wash_house_name',
                                // DB::raw('@srno  := @srno  + 1 AS srno')
                            )
                    ->get();

        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="addon_rate_lists/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="addon_rate_lists/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('addon_rate_list_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->addIndexColumn()
                ->make(true);

    }

    public function fetch_addon_lists(Request $request)
    {
        if($request->ajax()){
            $addons   = DB::table('wash_house_has_addons')
                            ->leftjoin('addons', 'addons.id', '=', 'wash_house_has_addons.addon_id')
                            ->where('wash_house_has_addons.wash_house_id',$request->wash_house_id)
                            ->pluck("addons.name","addons.id")
                            ->all();


            $data       = view('addon_rate_lists.ajax-addons',compact('addons'))->render();
            return response()->json(['options'=>$data]);
        }
    }


    public function rep_addon_rate_list(Request $request){
    
        if($request['wash_house_id_1'] == 0 || $request['wash_house_id_2'] ==0 ){
            return redirect()
                    ->back()
                    ->withInput($request->input())
                    ->with('permission','Please select both (different) wash houses.');
        }

        if($request['wash_house_id_1'] == $request['wash_house_id_2']){
            return redirect()
                    ->back()
                    ->withInput($request->input())
                    ->with('permission','Please select different wash houses.');
        }else{
            $wh_rec_1       = DB::table('addon_rate_lists')
                                ->where('addon_rate_lists.wash_house_id', $request['wash_house_id_1'])
                                ->count();

            $wh_rec_2       = DB::table('addon_rate_lists')
                                ->where('addon_rate_lists.wash_house_id', $request['wash_house_id_2'])
                                ->count();

            if( $wh_rec_1 < $wh_rec_2){
                return redirect()
                    ->back()
                    ->withInput($request->input())
                    ->with('permission','Left Wash-house has less addons than right Wash-house 2.');
            }else if( $wh_rec_1 == $wh_rec_2   ){
                return redirect()
                    ->back()
                    ->withInput($request->input())
                    ->with('permission','The number of addons on both Wash-houses are same.');
            }else{
                       
                $data   = DB::table('addon_rate_lists')
                            ->select(
                                        'addon_rate_lists.addon_id',
                                        'addon_rate_lists.wash_house_id',
                                        'addon_rate_lists.rate'
                                    )
                            ->where('addon_rate_lists.wash_house_id', $request['wash_house_id_1'])
                            ->get();
            
                if(!($data->isEmpty())){
                    $input['wash_house_id'] = $request['wash_house_id_2'];
                    $input['created_by']    = $request['created_by'];
                    DB::table("addon_rate_lists")->where('wash_house_id',$request['wash_house_id_2'])->delete();

                    foreach($data as $key => $value){
                        // check the addon is on wash-house, if yes, then replicate the rate - list
                        $wh_has_addons   = DB::table('wash_house_has_addons')
                                            ->where('wash_house_has_addons.wash_house_id', $request['wash_house_id_2'])
                                            ->where('wash_house_has_addons.addon_id', $value->addon_id)
                                            ->get();

                        if(!($wh_has_addons->isEmpty())){
                            $input['addon_id']       = $value->addon_id;
                            $input['rate']           = $value->rate;
                            Addon_rate_list::create($input);
                        }
                    
                    }
                    return redirect()->route('addon_rate_lists.index')
                            ->with('success','Addon rate list replicated successfully.');
                }else{

                    $data   = DB::table('wash_houses')
                                ->select('wash_houses.name as wash_house_name')
                                ->where('wash_houses.id', $request['wash_house_id_1'])
                                ->first();

                    return redirect()
                                ->back()
                                ->withInput($request->input())
                                ->with('permission', $data->wash_house_name.' has no record.');
                }
            }
        }
    }

    public function create()
    {
        $wash_houses        = Wash_house::pluck('name','id')->all();
        
        return view('addon_rate_lists.create',compact('wash_houses'));
    }

    public function store(Request $request)
    {
        request()->validate([
            'addon_id'      => 'required',
            'wash_house_id' => 'required',
            'rate'          => 'required|numeric|min:0',
        ]);

        $data       = DB::table('addon_rate_lists')
                        ->leftjoin('addons', 'addons.id', '=', 'addon_rate_lists.addon_id')
                        ->leftjoin('wash_houses', 'wash_houses.id', '=', 'addon_rate_lists.wash_house_id')
                        ->select(
                                'addons.name as addon_name',
                                'wash_houses.name as wash_house_name'
                               )
                        ->where('addon_rate_lists.addon_id',$request->addon_id)
                        ->where('addon_rate_lists.wash_house_id',$request->wash_house_id)
                        ->first();


        if(!($data)){
            $data   = Addon_rate_list::create($request->all());
                      return redirect()
                            ->route('addon_rate_lists.index')
                            ->with('success','Addon Rate list added successfully.');
        }else{
            return redirect()
                ->back()
                ->withInput($request->input())
                ->with('permission','Addon rate-list is already added <br> '.$data->wash_house_name.' has \"'.$data->addon_name.'\" addon \"');
        }


        
    }

    public function show($id)
    {
        $data       = DB::table('addon_rate_lists')
                        ->leftjoin('addons', 'addons.id', '=', 'addon_rate_lists.addon_id')                
                        ->leftjoin('wash_houses', 'wash_houses.id', '=', 'addon_rate_lists.wash_house_id')
                        ->select('addon_rate_lists.*',
                                 'addons.name as addon_name',
                                 'wash_houses.name as wash_house_name')
                        ->where('addon_rate_lists.id', $id)
                        ->first();

        return view('addon_rate_lists.show',compact('data'));
    }
 
    public function edit($id)
    {
        $data               = Addon_rate_list::find($id);
        $wash_houses        = Wash_house::pluck('name','id')->all();


        $addons   = DB::table('wash_house_has_addons')
                        ->leftjoin('addons', 'addons.id', '=', 'wash_house_has_addons.addon_id')
                        ->where('wash_house_has_addons.wash_house_id',$data->wash_house_id)
                        ->pluck("addons.name","addons.id")
                        ->all();

        
        return view('addon_rate_lists.edit',
                    compact('data',
                            'wash_houses',
                            'addons'
                        )
                    );
    }


    public function update(Request $request, $id)
    {
        $rec                = Addon_rate_list::find($id);
        
        request()->validate([
            'addon_id'      => 'required',
            'wash_house_id' => 'required',
            'rate'          => 'required|numeric|min:0',
        ]);

        
        $data       = DB::table('addon_rate_lists')
                        ->leftjoin('addons', 'addons.id', '=', 'addon_rate_lists.addon_id')                
                        ->leftjoin('wash_houses', 'wash_houses.id', '=', 'addon_rate_lists.wash_house_id')
                        ->select(
                                'addons.name as addon_name',
                                'wash_houses.name as wash_house_name'
                               )
                        ->where('addon_rate_lists.addon_id',$request->addon_id)
                        ->where('addon_rate_lists.wash_house_id',$request->wash_house_id)
                        ->where('addon_rate_lists.id',"!=",$id)
                        ->first();


        if($data){
            return redirect()
                    ->back()
                    ->withInput($request->input())
                    ->with('permission','Addon rate-list is already added <br> '.$data->wash_house_name.' has \"'.$data->addon_name.'\" addon \"');
           
        }else{
            $upd   = $rec->update($request->all());
            return redirect()
                    ->route('addon_rate_lists.index')
                    ->with('success','Addon rate list updated successfully');
           
        }
    }


    public function destroy(Request $request)
    { 
        $id = $request->ids;
        $data = DB::table("addon_rate_lists")->whereIn('id',explode(",",$id))->delete();
        return response()->json(['success'=>"Addon rate list deleted successfully."]);
    }

      

    
}
