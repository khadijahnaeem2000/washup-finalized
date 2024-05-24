<?php
namespace App\Http\Controllers;
use DB;
use DataTables;
use App\Models\Item;
use App\Models\Service;
use App\Models\Rate_list;
use App\Models\Wash_house;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class Rate_listController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:rate_list-list', ['only' => ['index','show']]);
         $this->middleware('permission:rate_list-create', ['only' => ['create','store']]);
         $this->middleware('permission:rate_list-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:rate_list-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        // for($s = 2; $s<7; $s++){
        //     for($a = 1; $a<50; $a++){
        //         $req['item_id']         = $a;
        //         $req['service_id']      = $s;
        //         $req['wash_house_id']   = 1;
        //         $req['rate']            = (rand(10,60));
        //         $data   = Rate_list::create($req);
        //     }
        // }
        // dd("adf");
        $wash_houses        = Wash_house::orderBy('name','ASC')
                                ->pluck('name','id')
                                ->all();
        
        return view('rate_lists.index',compact('wash_houses'));
    }

    public function list()
    {
        // DB::statement(DB::raw('set @srno=0'));
        $data   = Rate_list::orderBy('items.name','ASC')
                    ->orderBy('services.name','ASC')
                    // ->orderBy('wash_houses.name','ASC')
                    ->leftjoin('items', 'items.id', '=', 'rate_lists.item_id')                
                    ->leftjoin('services', 'services.id', '=', 'rate_lists.service_id')
                    ->leftjoin('wash_houses', 'wash_houses.id', '=', 'rate_lists.wash_house_id')
                    ->select(
                                'rate_lists.*',
                                'items.name as item_name',
                                'services.name as service_name',
                                'wash_houses.name as wash_house_name'
                                // DB::raw('@srno  := @srno  + 1 AS srno')
                            )
                    ->get();

        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="rate_lists/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="rate_lists/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('rate_list_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->addIndexColumn()
                ->make(true);

    }

    public function fetch_services(Request $request)
    {
        if($request->ajax()){
            $services   = DB::table('wash_house_has_services')
                            ->orderBy('services.name','ASC')
                            ->leftjoin('services', 'services.id', '=', 'wash_house_has_services.service_id')
                            ->where('wash_house_has_services.wash_house_id',$request->wash_house_id)
                            ->pluck("services.name","services.id")
                            ->all();

            $data       = view('rate_lists.ajax-services',compact('services'))->render();
            return response()->json(['options'=>$data]);
        }
    }

    public function fetch_items(Request $request)
    {
        if($request->ajax()){
           
            $items      = DB::table('service_has_items')
                            ->orderBy('items.name','ASC')
                            ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                            ->where('service_has_items.service_id',$request->service_id)
                            ->pluck("items.name","items.id")
                            ->all();
                          

            $data       = view('rate_lists.ajax-items',compact('items'))->render();

            return response()->json(['options'=>$data]);
        }
    }

    public function rep_service_rate_list(Request $request){
    
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
            $wh_rec_1       = DB::table('rate_lists')
                                ->where('rate_lists.wash_house_id', $request['wash_house_id_1'])
                                ->count();

            $wh_rec_2       = DB::table('rate_lists')
                                ->where('rate_lists.wash_house_id', $request['wash_house_id_2'])
                                ->count();

            if( $wh_rec_1 < $wh_rec_2){
                return redirect()
                    ->back()
                    ->withInput($request->input())
                    ->with('permission','Left Wash-house has less services than right Wash-house 2.');
            }else if( $wh_rec_1 == $wh_rec_2   ){
                return redirect()
                    ->back()
                    ->withInput($request->input())
                    ->with('permission','The number of service on both Wash-houses are same.');
            }else{
                       
                $data   = DB::table('rate_lists')
                            ->select('rate_lists.item_id',
                                        'rate_lists.service_id',
                                        'rate_lists.wash_house_id',
                                        'rate_lists.rate'
                                    )
                            ->where('rate_lists.wash_house_id', $request['wash_house_id_1'])
                            ->get();
            
                if(!($data->isEmpty())){
                    $input['wash_house_id'] = $request['wash_house_id_2'];
                    $input['created_by']    = $request['created_by'];
                    DB::table("rate_lists")->where('wash_house_id',$request['wash_house_id_2'])->delete();

                    foreach($data as $key => $value){
                        // check the service is on wash-house, if yes, then replicate the rate - list
                        $wh_has_srvs   = DB::table('wash_house_has_services')
                                            ->where('wash_house_has_services.wash_house_id', $request['wash_house_id_2'])
                                            ->where('wash_house_has_services.service_id', $value->service_id)
                                            ->get();

                        if(!($wh_has_srvs->isEmpty())){
                            $input['item_id']       = $value->item_id;
                            $input['service_id']    = $value->service_id;
                            $input['rate']          = $value->rate;
                            Rate_list::create($input);
                        }
                    
                    }
                    return redirect()->route('rate_lists.index')
                            ->with('success','Rate list replicated successfully.');
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
        $wash_houses        = Wash_house::orderBy('name','ASC')->pluck('name','id')->all();
        $services           = Service::orderBy('name','ASC')->pluck('name','id')->all();
        $items              = Item::orderBy('name','ASC')->pluck('name','id')->all();
        
        return view('rate_lists.create',compact('wash_houses','services','items'));
    }

    public function store(Request $request)
    {

      
        request()->validate([
            'item_id'       => 'required',
            'service_id'    => 'required',
            'wash_house_id' => 'required',
            // 'rate'          => 'required|numeric|min:1',
            'rate'          => 'required|numeric|min:0',
        ]);

        

        $data       = DB::table('rate_lists')
                        ->leftjoin('items', 'items.id', '=', 'rate_lists.item_id')                
                        ->leftjoin('services', 'services.id', '=', 'rate_lists.service_id')
                        ->leftjoin('wash_houses', 'wash_houses.id', '=', 'rate_lists.wash_house_id')
                        ->select(
                                'items.name as item_name',
                                'services.name as service_name',
                                'wash_houses.name as wash_house_name'
                               )
                        ->where('rate_lists.item_id',$request->item_id)
                        ->where('rate_lists.service_id',$request->service_id)
                        ->where('rate_lists.wash_house_id',$request->wash_house_id)
                        ->first();


        if(!($data)){
            $data   = Rate_list::create($request->all());
                      return redirect()
                            ->route('rate_lists.index')
                            ->with('success','Rate list added successfully.');
        }else{
            return redirect()
                ->back()
                ->withInput($request->input())
                ->with('permission','Rate-list is already added <br> '.$data->wash_house_name.' has \"'.$data->service_name.'\" service and \"'.$data->item_name.'\" item');
        }


        
    }

    public function show($id)
    {
        $data       = DB::table('rate_lists')
                        ->leftjoin('items', 'items.id', '=', 'rate_lists.item_id')                
                        ->leftjoin('services', 'services.id', '=', 'rate_lists.service_id')
                        ->leftjoin('wash_houses', 'wash_houses.id', '=', 'rate_lists.wash_house_id')
                        ->select('rate_lists.*',
                                 'items.name as item_name',
                                 'services.name as service_name',
                                 'wash_houses.name as wash_house_name')
                        ->where('rate_lists.id', $id)
                        ->first();

        return view('rate_lists.show',compact('data'));
    }
 
    public function edit($id)
    {
        $data               = rate_list::find($id);
        $wash_houses        = Wash_house::orderBy('name','ASC')->pluck('name','id')->all();

        $services           = DB::table('wash_house_has_services')
                                ->orderBy('services.name','ASC')
                                ->leftjoin('services', 'services.id', '=', 'wash_house_has_services.service_id')
                                ->where('wash_house_has_services.wash_house_id',$data->wash_house_id)
                                ->pluck("services.name","services.id")
                                ->all();

        $items              = DB::table('service_has_items')
                                ->orderBy('items.name','ASC')    
                                ->leftjoin('items', 'items.id', '=', 'service_has_items.item_id')
                                ->where('service_has_items.service_id',$data->service_id)
                                ->pluck("items.name","items.id")
                                ->all();


        // $items              = Item::pluck('name','id')->all();
        
        return view('rate_lists.edit',
                    compact('data',
                            'items',
                            'wash_houses',
                            'services'
                        )
                    );
    }


    public function update(Request $request, $id)
    {
        $rec                = rate_list::find($id);
        
        request()->validate([
            'service_id'    => 'required',
            'item_id'       => 'required',
            'wash_house_id' => 'required',
            // 'rate'          => 'required|numeric|min:1',
            'rate'          => 'required|numeric|min:0',
        ]);

        
        $data       = DB::table('rate_lists')
                        ->leftjoin('items', 'items.id', '=', 'rate_lists.item_id')                
                        ->leftjoin('services', 'services.id', '=', 'rate_lists.service_id')
                        ->leftjoin('wash_houses', 'wash_houses.id', '=', 'rate_lists.wash_house_id')
                        ->select(
                                'items.name as item_name',
                                'services.name as service_name',
                                'wash_houses.name as wash_house_name'
                               )
                        ->where('rate_lists.item_id',$request->item_id)
                        ->where('rate_lists.service_id',$request->service_id)
                        ->where('rate_lists.wash_house_id',$request->wash_house_id)
                        ->where('rate_lists.id',"!=",$id)
                        ->first();


        if($data){
            return redirect()
                    ->back()
                    ->withInput($request->input())
                    ->with('permission','Rate-list is already added <br> '.$data->wash_house_name.' has \"'.$data->service_name.'\" service and \"'.$data->item_name.'\" item');
           
        }else{
            $upd   = $rec->update($request->all());
            return redirect()
                    ->route('rate_lists.index')
                    ->with('success','Rate list updated successfully');
           
        }
    }


    public function destroy(Request $request)
    { 
        $id             = $request->ids;
        $rate_list      = rate_list::find($id);
        $data           = DB::table("rate_lists")->whereIn('id',explode(",",$id))->delete();
        return response()->json(['success'=>" Rate list deleted successfully."]);
    }

      

    
}
