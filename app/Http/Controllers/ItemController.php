<?php


namespace App\Http\Controllers;
use DB;
use DataTables;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    function __construct()
    {
       
        $this->middleware('permission:item-list', ['only' => ['index','show']]);
        $this->middleware('permission:item-create', ['only' => ['create','store']]);
        $this->middleware('permission:item-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:item-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('items.index');
    }

    public function list()
    {
        // DB::statement(DB::raw('set @srno=0'));
        $data = DB::table('items')
                ->orderBy('name','ASC')
                ->select(
                            'items.id',
                            'items.name',
                            'items.short_name',
                            // DB::raw('@srno  := @srno  + 1 AS srno')
                        )
                ->get();

        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="items/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="items/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                     
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('item_delete') .'" data-id="'.$data->id.'">
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
        return view('items.create');
    }

    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required|min:3|unique:items,name',
            'short_name' => 'required|min:2|unique:items,short_name',
        ]);

        if($request['image']){
            $this->validate($request,[
                'image'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
            $image = $request->file('image');
            $new_name=rand().'.'.$image->getClientOriginalExtension();
            $image->move(public_path("uploads/items"),$new_name);
            $input = $request->all();
            $input['image'] = $new_name;
            $data = Item::create($input);
        }else{
            $data = Item::create($request->all());
        }

       
      
        return redirect()
                ->route('items.index')
                ->with('success','Item '.$request['name'] .' added successfully.');
    }

     public function show($id)
    {
        $data = DB::table('items')
                    ->where('items.id', $id)
                    ->first();

        return view('items.show',compact('data'));
    }


    public function edit($id)
    {
        $data= DB::table('items')
                    ->where('items.id', $id)
                    ->first();

        return view('items.edit',compact('data'));
    }


    public function update(Request $request, $id)
    {
       
        $this->validate($request,[
            'name' => 'required|min:3|unique:items,name,'.$id,
            'short_name' => 'required|min:2|unique:items,short_name,'.$id
        ]);
        $item = Item::find($id);
        $input = $request->all();

        
        if(!empty($input['image'])){
            $this->validate($request,[
                'image'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
            
            if($item['image']!=""){
                if(file_exists(public_path('uploads/items/'.$item['image']))){
                    unlink(public_path('uploads/items/'.$item['image']));
                }
            }

            $image = $request->file('image');
            $new_name=rand().'.'.$image->getClientOriginalExtension();
            $image->move(public_path("uploads/items"),$new_name);
            $input['image'] = $new_name;
            $item->update($input);
        }else{
             $input['image'] = $item['image'];
             $item->update($input);
        }

        return redirect()
                ->route('items.index')
                ->with('success','Item'.$request['name'] .' updated successfully.');
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $item = item::find($ids);
            if($item['image']!=""){
                if(file_exists(public_path('uploads/items/'.$item['image']))){
                    unlink(public_path('uploads/items/'.$item['image']));
                }
            }

        $chk    = DB::table('service_has_items')
                    ->select('service_has_items.id')
                    ->where('service_has_items.item_id',$ids)
                    ->first();

        $chk1    = DB::table('order_has_items')
                    ->select('order_has_items.id')
                    ->where('order_has_items.item_id',$ids)
                    ->first();

        if( (!(isset($chk->id)))  && (!(isset($chk1->id))) ){
            $data = DB::table("items")->whereIn('id',explode(",",$ids))->delete();
            return response()->json(['success'=>$data." item deleted successfully."]);
        }
        return response()->json(['error'=>"This Item cannot be deleted"]);
    }





}
