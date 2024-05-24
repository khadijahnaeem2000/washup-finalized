<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Time_slot;
use Carbon\Carbon;

use Validator;
use DB;
use DataTables;

class Time_slotController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:time_slot-list', ['only' => ['index','show']]);
         $this->middleware('permission:time_slot-create', ['only' => ['create','store']]);
         $this->middleware('permission:time_slot-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:time_slot-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('time_slots.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data       = DB::table('time_slots')
                        ->orderBy('time_slots.name','ASC')
                        ->select(
                                    // 'time_slots.*',
                                    'time_slots.id',
                                    'time_slots.color',
                                    'time_slots.name',
                                    'time_slots.end_time',
                                    // 'time_slots.start_time',
                                    DB::raw('DATE_FORMAT(time_slots.start_time, "%h:%i:%p") as start_time'),
                                    DB::raw('DATE_FORMAT(time_slots.end_time, "%h:%i:%p") as end_time'),
                                    DB::raw('@srno  := @srno  + 1 AS srno')
                                )
                        ->get();
                        // $.fn.dataTable.moment( 'M/D/YYYY h:mm:ss A');
        return 
            DataTables::of($data)
                
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="time_slots/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="time_slots/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('timeslot_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);
    }

    public function create()
    {
        return view('time_slots.create');
    }

    public function store(Request $request)
    {
        // request()->validate([
        //     'name'          => 'required|min:3|unique:time_slots,name',
        //     'color'         => 'required|unique:time_slots,color',
        //     'start_time'    => 'required|date_format:H:i',
        //     'end_time'      => 'required|date_format:H:i|after:start_time',
        // ]);

        $this->validate($request, 
            [
                'name'          => 'required|min:3|unique:time_slots,name',
                'color'         => 'required|unique:time_slots,color',
                'start_time'    => 'required|date_format:H:i',
                'end_time'      => 'required|date_format:H:i|after:start_time',
            ],
            [
                'end_time.after'       => 'End time must be greater than start time',
            ]
        );


        $check              = true;
        $record             = DB::table('time_slots')->get();
        // echo "<br>start_time: " . ($request->start_time);
        // echo "<br>rec_time: "   .($request->end_time);

        foreach ($record as $key => $value) {
          
            $req_start_time         = (strtotime($request->start_time));
            $req_end_time           = (strtotime($request->end_time));

            $rec_start_time         = (strtotime($value->start_time));
            $rec_end_time           = (strtotime($value->end_time));

            if( ($req_start_time  >= $rec_start_time) &&  ($req_start_time  < $rec_end_time) ){
                // echo "<br> $key";
                // echo "<br><br>start time is in between";
                // echo "<br>start_time: " . ($value->start_time);
                // echo "<br>rec_time: " .($value->end_time);
                $check  = false;
                break;
            }
        }

        if($check){
            $data           = Time_slot::create($request->all());
            return  redirect()
                        ->route('time_slots.index')
                        ->with('success','Timeslot added successfully.');
        }else{
            return redirect()
                        ->back()
                        ->withInput($request->input())
                        ->with('permission','Timeslot(in between/ equal) is already stored.');
        }
    }

     public function show($id)
    {
        $data   =  DB::table('time_slots')
                    ->orderBy('time_slots.created_at','DESC')
                    ->select('time_slots.*')
                    ->where('time_slots.id', $id)
                    ->first();


        return view('time_slots.show',compact('data'));
    }


    public function edit($id)
    {
        $data   = DB::table('time_slots')
                    ->where('time_slots.id', $id)
                    ->first();

        return view('time_slots.edit',compact('data'));
    }


    public function update(Request $request, $id)
    {
        $data       = Time_slot::findOrFail($id);
        $this->validate($request,[
            'name'          => 'required|min:3|unique:time_slots,name,'.$id,
            'color'         => 'required|unique:time_slots,color,'.$id,
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            ],
            [
                'end_time.after'       => 'End time must be greater than start time',
            ]
        );


        $rec                = Time_slot::where('id','!=',$id)
                                ->whereBetween('end_time',[$request->start_time,$request->end_time])
                                ->whereBetween('start_time', [$request->start_time,$request->end_time])
                                ->first();

        if($rec!=null){
            return redirect()
                    ->back()
                    ->withInput($request->input())
                    ->with('permission','Timeslot(in between/ equal) is already stored.');
           
        }else{

            $upd    = $data->update($request->all());

            return redirect()
                    ->route('time_slots.index')
                    ->with('success','Timeslot updated successfully.');
            
        }
        
    }

    public function destroy(Request $request)
    {
        $ids        = $request->ids;
        $data       = DB::table("time_slots")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>$data." Time slot deleted successfully."]);
    }




}
