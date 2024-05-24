<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use DB;
use Auth;
use Validator;
use App\Models\Wash_house;
use App\Models\Order;
use App\Models\Distribution_hub;
use App\Models\Wash_house_has_order;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

 
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     
     
    
    // exporting the excel file 
    public function store(Request $request)
    {


        
        request()->validate([
            
            'from_date'       => 'required|date',
            'to_date'         => 'required|date|after_or_equal:from_date',
        ]);

        

        $data = new OrdersExport($request['from_date'], $request['to_date']);
        return Excel::download($data, 'invoices.csv');

    }

    
    public function index()
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
                $hubs           = Distribution_hub::pluck('name','id')->all();
            }else{
                $hubs           = DB::table('distribution_hubs')
                                    ->leftjoin('hub_has_users', 'hub_has_users.hub_id', '=', 'distribution_hubs.id')
                                    ->where('hub_has_users.user_id', $user->user_id)
                                    ->select(
                                        'distribution_hubs.id as id',
                                        'distribution_hubs.name as name'
                                        )
                                    ->pluck('distribution_hubs.name','distribution_hubs.id')
                                    ->all();
            }
        return view('home',
                        compact('hubs')
                    );

        // return view('home');
    }
    public function reverse_orders(Request $request){
        if($request->ajax()){
            $pickup_date        = $request->pickup_date;
            $delivery_date      = $request->delivery_date;
            $timestamp          = $pickup_date . " 18:18:06";
   

            DB::table('orders')->update(['pickup_date' => $pickup_date, 'delivery_date' => $delivery_date]);
            DB::table('orders')->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            DB::table('order_has_addons')->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            DB::table('order_has_items')->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            DB::table('order_has_services')->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            DB::table('order_histories')->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            DB::table('rider_histories')->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            DB::table('route_plans')->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            DB::table('wash_house_has_orders')->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            DB::table('customer_has_wallets')->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            DB::table('payment_rides')->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            DB::table('payment_ride_histories')->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            return response()->json(['success'=>"All orders reversed successfully."]);  
        }else{
            return response()->json(['error'=>$validator->errors()->all()]);
        }
            
    }



    public function remove_orders(Request $request){
        
        if($request->ajax()){
           
            DB::table('orders')->truncate();
            DB::table('order_has_addons')->truncate();
            DB::table('order_has_bags')->truncate();
            DB::table('order_has_items')->truncate();
            DB::table('order_has_services')->truncate();
            DB::table('order_has_tags')->truncate();
            DB::table('order_histories')->truncate();
            DB::table('payment_rides')->truncate();
            DB::table('payment_ride_histories')->truncate();
            DB::table('rider_histories')->truncate();
            DB::table('route_plans')->truncate();
            DB::table('wash_house_has_orders')->truncate();
            DB::table('complaints')->truncate();
            DB::table('personal_access_tokens')->truncate();
            DB::table('customer_has_wallets')->where('detail','!=','AC Open')->delete();
           
            return response()->json(['success'=>"All orders deleted successfully."]);  
        }else{
            return response()->json(['error'=>$validator->errors()->all()]);
        }
    }


    public function fetch_dashboard(Request $request)
    {

        // date range is creating problem 
        // how will we get hub id of customers
        // how will we get hub id by joining hubs table will customer_wallets
        if($request->ajax()){
            $validator = Validator::make($request->all(),
                [
                    'hub_id'                => 'required|min:1',
                    'from_date'             => 'required|date',
                    'to_date'               => 'required|date|after_or_equal:from_date',
                ],
                [
                    'hub_id.required'       =>'Please select at least one hub',
                    'rider_id.required'     =>'Please select rider',
                    'to_date.after'         =>'Please select \" To date\" greater than \"From date\" ',
                ]
            );

            if ($validator->passes()) {
                $to_date                = ($request->to_date);
                $hub_id                 = ($request->hub_id);
                $from_date              = ($request->from_date);

                $to_month               = (int)date("m",strtotime($to_date));
                $from_month             = (int)date("m",strtotime($from_date));


                // if($from_month > $to_month){
                //     $temp       = $to_month;
                //     $to_month   = $from_month;
                //     $from_month = $temp; 
                // }



                $months                 = array();
                $tot_orders             = array();
                $comp_orders            = array();
                $cncl_orders            = array();

                $all_tot_orders         = DB::table('orders')
                                            ->where('orders.hub_id',$hub_id)
                                            ->select(
                                                        DB::raw('count(*) as total'),
                                                        DB::raw("MONTH(pickup_date) as month"),
                                                    )
                                            ->whereBetween(DB::raw("MONTH(pickup_date)"), [$from_month, $to_month]) 
                                            ->groupBy(DB::raw("MONTH(pickup_date)"))
                                            ->get();

                $all_comp_orders        = DB::table('orders')
                                            ->where('orders.hub_id',$hub_id)
                                            ->select(
                                                        DB::raw('count(*) as total'),
                                                        DB::raw("MONTH(pickup_date) as month"),
                                                    )
                                            ->whereBetween(DB::raw("MONTH(pickup_date)"), [$from_month, $to_month]) 
                                            ->where('orders.status_id2','=', 15) 
                                            ->groupBy(DB::raw("MONTH(pickup_date)"))
                                            ->get();

                $all_cncl_orders        = DB::table('orders')
                                            ->where('orders.hub_id',$hub_id)
                                            ->select(
                                                        DB::raw('count(*) as total'),
                                                        DB::raw("MONTH(pickup_date) as month"),
                                                    )
                                            ->whereBetween(DB::raw("MONTH(pickup_date)"), [$from_month, $to_month]) 
                                            ->where('orders.status_id2','=', 16) 
                                            ->groupBy(DB::raw("MONTH(pickup_date)"))
                                            ->get();

                for($i=$from_month; $i<=$to_month; $i++){
                    $monthName          = date('F', mktime(0, 0, 0, $i, 10)); 
                    $months[]           = $monthName;

                    // All Orders
                    foreach ($all_tot_orders as $key => $value) {
                        if($i == $value->month){
                            $tot_orders[] = $value->total;
                        }
                    }

                    // Cancel Orders
                    foreach ($all_cncl_orders as $key => $value) {
                        if($i == $value->month){
                            $cncl_orders[] = $value->total;
                        }
                    }

                    // Complete Orders
                    foreach ($all_comp_orders as $key => $value) {
                        if($i == $value->month){
                            $comp_orders[] = $value->total;
                        }
                    }

                }
           
                $new_customers          = DB::table('customers')
                                            ->whereBetween('customers.created_at', [$from_date, $to_date])  
                                            ->count();

                $new_orders             = DB::table('orders')
                                            ->whereBetween('orders.pickup_date', [$from_date, $to_date])  
                                            // ->Where(function($query) use ($from_date,$to_date){
                                            //     $query->where('orders.pickup_date','>=',$from_date)
                                            //         ->Where('orders.delivery_date','<=',$to_date);
                                            // })
                                            ->where('orders.hub_id',$hub_id)
                                            ->Where(function($query){
                                                $query->where('orders.status_id2','!=', 16) //16:cancel orders
                                                    ->orWhereNull('orders.status_id2')
                                                    ->whereIn('orders.status_id', [1,2,3]);
                                            })
                                            ->count();

                $new_revenues           = DB::table('customer_has_wallets')
                                            ->whereBetween('customer_has_wallets.created_at', [$from_date, $to_date])  
                                            ->sum('in_amount');


                                            
                $all_customers          = DB::table('customers')
                                            // ->whereBetween('orders.created_at', [$from_date, $to_date])  
                                            ->count();

                $all_orders             = DB::table('orders')
                                            ->where('orders.hub_id',$hub_id)
                                            ->Where(function($query){
                                                $query->where('orders.status_id2','!=', 16) //16:cancel orders
                                                    ->orWhereNull('orders.status_id2')
                                                    ->whereIn('orders.status_id', [1,2,3]);
                                            })
                                            ->count();
                $all_revenues           = DB::table('customer_has_wallets')
                                            // ->whereBetween('customer_has_wallets.created_at', [$from_date, $to_date])  
                                            ->sum('in_amount');


                $active_customers       = DB::table('customers')
                                            // ->whereBetween('orders.created_at', [$from_date, $to_date])  
                                            ->count();

                $pending_orders         = DB::table('orders')
                                            ->where('orders.hub_id',$hub_id)
                                            ->Where(function($query){
                                                $query->where('orders.status_id2','!=', 16) //16:cancel orders
                                                    ->orWhereNull('orders.status_id2');
                                                    // ->whereIn('orders.status_id', [1,2,3]);
                                            })
                                            ->count();


                if($all_orders != 0){
                
                    return response()->json([
                                                'new_orders'        => $new_orders,
                                                'new_revenues'      => $new_revenues,
                                                'new_customers'     => $new_customers,

                                                'all_orders'        => $all_orders,
                                                'all_revenues'      => $all_revenues,
                                                'all_customers'     => $all_customers,
                                                
                                                'pending_orders'    => $pending_orders,
                                                'active_customers'  => $active_customers,

                                                'months'            => $months,
                                                'tot_orders'        => $tot_orders,
                                                'cncl_orders'       => $cncl_orders,
                                                'comp_orders'       => $comp_orders

                                            ]);
                }else{
                    return response()->json(['error'=>[0=>"Data not found"]]);
                }
                    
            }else{
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }

    }
}
