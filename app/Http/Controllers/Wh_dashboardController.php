<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use DataTables;
use App\Models\Order;
use App\Models\Status;
use App\Models\Service;
use App\Models\Wash_house;
use App\Models\Order_has_item;
use App\Models\Order_has_addon;
use App\Models\Distribution_hub;
use App\Models\Order_has_service;

class Wh_dashboardController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:wh_dashboard-list', ['only' => ['index','show']]);
         $this->middleware('permission:wh_dashboard-create', ['only' => ['create','store']]);
         $this->middleware('permission:wh_dashboard-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:wh_dashboard-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        $user_id                = Auth::user()->id;
        $user                   = DB::table('users')
                                    ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                    ->leftjoin('hub_has_users', 'hub_has_users.user_id', '=', 'users.id')
                                    ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                                    ->where('users.id', $user_id)
                                    ->select(
                                                'users.id as user_id',
                                                'hub_has_users.user_id as hub_user_id',
                                                'users.name as user_name',
                                                'roles.id as role_id',
                                                'roles.name as role_name',
                                            )
                                    ->first();

            if($user->role_id == 1){
                $wash_houses    = Wash_house::pluck('name','id')->all();
            }else{
                $wash_houses    = DB::table('wash_houses')
                                    ->leftjoin('wash_house_has_users', 'wash_house_has_users.wash_house_id', '=', 'wash_houses.id')
                                    ->where('wash_house_has_users.user_id', $user->user_id)
                                    ->select(
                                                'wash_houses.id as id',
                                                'wash_houses.name as name'
                                            )
                                    ->pluck('wash_houses.name','wash_houses.id')
                                    ->all();
            }
        return view('wh_dashboards.index',
                        compact('wash_houses')
                    );
    }

    public function fn_has_order_addons($order_id){
        $data     = DB::table('order_has_addons')
                        ->select(
                                'order_has_addons.id'
                                )
                        ->where('order_has_addons.order_id',$order_id)
                        ->get();
                        
                        // print_r($data);
        if(!empty($data)){
            return 1;       //if order has addons
        }else{
            return 0;       //if order has no addons
        }
    }

    public function list(Request $request)
    {

        $orders     = DB::table('wash_house_has_orders')
                        ->leftjoin('orders', 'orders.id', '=', 'wash_house_has_orders.order_id')
                        ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                        ->select(
                                'orders.id',
                                'customers.name as customer_name',
                                'orders.pickup_date',
                                'orders.delivery_date',
                                'orders.order_note',
                                'orders.delivery_date'
                                )
                        ->where('wash_house_has_orders.wash_house_id',$request['wash_house_id'])
                        ->get();

        if($orders){
            $ord    = [];
            foreach ($orders as $key => $value) {
                $value->addons          = $this->fn_has_order_addons($value->id);
                $ord[$key]              = $value;
            }
            $rec                        = collect($ord);
            // dd($rec);
            if($orders){
                $details    = view('wh_dashboards.report',
                                    compact('rec'))
                                ->render();
                return response()->json(['data'=>$orders,'details'=>$details]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
        }else{
            return response()->json(['error'=>"Data not found"]);
        }
        return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-primary btn-sm" href="order_verifies/'.$data->id.'">
                            <i class="fa fa-print"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="order_verifies/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a  href="/order_verifies/show_tags/'.$data->id.'" class="btn btn-primary  btn-sm">
                            <i class="fa fa-tags"></i>
                        </a>
                        <a  href="/order_verifies/special_show_tags/'.$data->id.'" class="btn btn-success  btn-sm chk_prm">
                            <i class="fa fa-tags"></i>
                        </a>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->make(true);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }
   
    public function show($id)
    {
        $data                   = Order::orderBy('orders.created_at','DESC')
                                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                    ->leftjoin('statuses', 'statuses.id', '=', 'orders.status_id')
                                    ->select(
                                                'orders.id',
                                                'customers.id as customer_id',
                                                'customers.name',
                                                'customers.contact_no',
                                                'orders.id as order_id',
                                                'orders.pickup_date',
                                                'customers.permanent_note',
                                                'orders.order_note',
                                                'orders.delivery_date',
                                                'statuses.name as status_name',
                                                'orders.ref_order_id'
                                            )
                                    ->whereNotNull('orders.pickup_rider_id')
                                    ->find($id);
                                    // dd($data);

        if($data){
            if($data->ref_order_id!= NULL){
                $id = $data->ref_order_id;
            }

            $selected_services      = DB::table('order_has_services')
                                        ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                        ->where('order_has_services.order_id', $id)
                                        ->select('services.id as service_id',
                                                'services.name as service_name',
                                                'order_has_services.weight as weight',
                                                'order_has_services.qty as service_qty'
                                                )
                                        ->orderBy('order_number','ASC')
                                        ->get()
                                        ->all();   
                                        

            $selected_items         = DB::table('order_has_items')
                                        ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                        ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                        // ->leftjoin('order_has_services', 'order_has_services.order_id', '=', 'order_has_items.order_id')
                                        ->where('order_has_items.order_id', $id)
                                        ->select(
                                                'items.id as item_id',
                                                'items.name as item_name',
                                                'order_has_items.service_id as service_id',
                                                'order_has_items.pickup_qty as pickup_qty',
                                                'order_has_items.note as note',
                                                'order_has_items.item_image as item_image',
                                                'services.name as service_name',
                                                'order_has_items.id as ord_itm_id'
                                                )
                                        ->get()
                                        ->all(); 
                                    

            $selected_addons        = DB::table('order_has_addons')
                                        ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                        ->where('order_has_addons.order_id', $id)
                                        ->select('addons.id as addon_id',
                                                'addons.name as addon_name',
                                                'order_has_addons.item_id as item_id',
                                                'order_has_addons.service_id as service_id',
                                                'order_has_addons.ord_itm_id as ord_itm_id',
                                                )
                                        ->get()
                                        ->all(); 
            
            return view('wh_dashboards.show',
                        compact('data',
                                'selected_services',
                                'selected_items',
                                'selected_addons'
                            )
                        );
        }else{
            return redirect()->route('wh_dashboards.index')
                    ->with('permission','Details not found');
        }
    }


    public function edit($id)
    {}

    public function update(Request $request, $id)
    {}

    public function destroy(Request $request)
    {}

      

    
}
