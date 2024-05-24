<?php


namespace App\Http\Controllers;
use App\Models\Message;
use Illuminate\Http\Request;
use DB;
use DataTables;

class MessageController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:message-list', ['only' => ['index','show']]);
         $this->middleware('permission:message-create', ['only' => ['create','store']]);
         $this->middleware('permission:message-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:message-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('messages.index');
    }

    public function list()
    {
        $data = DB::table('messages')
                    ->orderBy('messages.name')
                    ->select('messages.id','messages.name')
                    ->get();
                    
        return DataTables::of($data)
                ->addColumn('action',function($data){
                 return 
                        '<div class="btn-group btn-group">
                          
                            <a class="btn btn-secondary btn-sm" href="messages/'.$data->id.'/edit" id="'.$data->id.'">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                         
                        </div>';
                    })
                ->addColumn('srno','')
                ->rawColumns(['srno','','action'])
                ->make(true);

    }

    public function create()
    {
        return view('messages.create');
    }


    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required|unique:messages,name',
        ]);
        message::create($request->all());
        return redirect()->route('messages.create')
                        ->with('success','message '.$request['name']. ' added successfully.');
    }

     public function show(message $message)
    {
        return view('messages.show',compact('message'));
    }


    public function edit(message $message)
    {
        return view('messages.edit',compact('message'));
    }


    public function update(Request $request,$id)
    {
        $message = message::findOrFail($id);
        request()->validate([
            'name' => 'required|unique:messages,name,'.$id,
        ]);

        $message->update($request->all());
        return redirect()->route('messages.index')
                        ->with('success','message '.$request['name']. ' updated successfully');
    }

    public function destroy(message $message)
    {
        $ids = $request->ids;
        $data = DB::table("messages")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>$data." message deleted successfully."]);
    }

   
}
