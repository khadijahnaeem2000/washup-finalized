<?php


namespace App\Http\Controllers;

use DB;
use DataTables;
use Validator;
use Illuminate\Http\Request;
use App\Models\Customer_has_wallet;
use App\Http\Controllers\Controller;


class Customer_walletController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:customer_wallet-list', ['only' => ['index','show']]);
         $this->middleware('permission:customer_wallet-create', ['only' => ['create','store']]);
         $this->middleware('permission:customer_wallet-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:customer_wallet-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('customer_wallets.index');
    }

  
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'from_date'             => 'required|date',
                'to_date'               => 'required|date|after:from_date',
            ],
            [
                'to_date.after'         =>'Please select \" To date\" greater than \"From date\" ',
            ]
        );
        if ($validator->passes()) {
            $to             = $request['to_date'];
            $from           = $request['from_date'];

                          DB::statement(DB::raw('set @srno=0'));
            $rec        = DB::table('customer_has_wallets')
                            ->orderBy('customer_has_wallets.created_at','DESC')
                            ->leftjoin('customers', 'customers.id', '=', 'customer_has_wallets.customer_id')
                            ->leftjoin('wallet_reasons', 'wallet_reasons.id', '=', 'customer_has_wallets.wallet_reason_id')
                            ->select(
                                        'customer_has_wallets.*',
                                        DB::raw('DATE_FORMAT(customer_has_wallets.created_at, "%Y-%m-%d") as created_date'),
                                        DB::raw('DATE_FORMAT(customer_has_wallets.created_at, "%h:%i:%p") as created_time'),
                                        DB::raw('DATE_FORMAT(customer_has_wallets.created_at, "%M") as created_month'),
                                        'customers.name as customer_name',
                                        
                                        'customers.contact_no as contact_no',
                                        'wallet_reasons.name as reason_name',
                                        DB::raw('@srno  := @srno  + 1 AS srno')
                                    )
                            ->where(function ($query) use ($from, $to){
                                        $query->whereDate('customer_has_wallets.created_at', '>=', $from)
                                              ->whereDate('customer_has_wallets.created_at', '<=', $to);
                                            })
                            // ->whereBetween('customer_has_wallets.created_at', [$from, $to])  
                            ->get();

                            // dd($rec);
            if(!($rec->isEmpty())){
                $details    = view('customer_wallets.report_table',
                                    compact('rec'))
                                    ->render();
                return response()->json(['details'=>$details]);
            }else{
                return response()->json(['error'=>[0=>"Data not found"  ]]);
            }
        }else{
            return response()->json(['error'=>$validator->errors()->all()]);
        }
    
    }


    public function list_onload(Request $request)
    {
       
            $to             = $request['to_date'];
            $from           = $request['from_date'];

                          DB::statement(DB::raw('set @srno=0'));
            $rec        = DB::table('customer_has_wallets')
                            ->orderBy('customer_has_wallets.created_at','DESC')
                            ->leftjoin('customers', 'customers.id', '=', 'customer_has_wallets.customer_id')
                            ->leftjoin('wallet_reasons', 'wallet_reasons.id', '=', 'customer_has_wallets.wallet_reason_id')
                            ->select(
                                        'customer_has_wallets.*',
                                        DB::raw('DATE_FORMAT(customer_has_wallets.created_at, "%Y-%m-%d") as created_date'),
                                        DB::raw('DATE_FORMAT(customer_has_wallets.created_at, "%h:%i:%p") as created_time'),
                                        DB::raw('DATE_FORMAT(customer_has_wallets.created_at, "%M") as created_month'),
                                        'customers.name as customer_name',
                                        
                                        'customers.contact_no as contact_no',
                                        'wallet_reasons.name as reason_name',
                                        DB::raw('@srno  := @srno  + 1 AS srno')
                                    )
                            // ->where(function ($query) use ($from, $to){
                            //             $query->whereDate('customer_has_wallets.created_at', '>=', $from)
                            //                   ->whereDate('customer_has_wallets.created_at', '<=', $to);
                            //                 })
                            // ->whereBetween('customer_has_wallets.created_at', [$from, $to])  
                            ->get();

                            // dd($rec);
            if(!($rec->isEmpty())){
                $details    = view('customer_wallets.report_table',
                                    compact('rec'))
                                    ->render();
                return response()->json(['details'=>$details]);
            }else{
                return response()->json(['error'=>[0=>"Data not found"  ]]);
            }
       
    
    }

    public function fetch_customer_detail(Request $request)
    {
        if($request->ajax()){
            $customer           = DB::table('customers')
                                        ->select('customers.id','customers.name','customers.permanent_note')
                                        ->where('customers.contact_no',$request->contact_no)
                                        ->first();

                                        
            if($customer){
                $customer_id    = $customer->id;
                // $addresses      = DB::table('customer_has_addresses')
                //                         ->select('id','status', 'address as name')
                //                         ->where('customer_has_addresses.customer_id',$customer_id)
                //                         ->get();

                $in_amount      = DB::table('customer_has_wallets')
                                        ->where('customer_has_wallets.customer_id',$customer_id)
                                        ->sum('in_amount');
                $out_amount     = DB::table('customer_has_wallets')
                                        ->where('customer_has_wallets.customer_id',$customer_id)
                                        ->sum('out_amount');

                $current_bal    = $in_amount  - $out_amount;



                // $customer_address = view('customer_wallets.ajax-address',compact('addresses'))->render();
                // return response()->json(['data'=>$customer,'customer_address'=>$customer_address,'current_bal'=>$current_bal]);
                return response()->json(['data'=>$customer,'current_bal'=>$current_bal]);
            }else{
                return response()->json(['error'=>"Data not found"]);
            }
            
        }

    }
     //start khadeeja's edit
    public function fetch_email_detail(Request $request) 
    {
      if ($request->ajax()) {
            $customer = DB::table('customers')
            ->select('customers.id')
            ->where('customers.contact_no', $request->contact_no)
            ->first();
            $customerId =$customer->id;
            $customer_email_verified =  DB::table('customers')
            ->select('customers.id')
            ->where('customers.id', $customerId) // <-- Corrected line
            ->where('customers.email_alert', 1)
            ->exists();
        
            if ($customer_email_verified) 
            {
           
              return response()->json(['data' => $customerId]); // Changed response message
            
            }else 
            {
                return response()->json(['error' =>$customerId]);
            }
       }
    }
    //end khadeeja's edit

    public function create()
    {
        $reasons                  = DB::table('wallet_reasons')
                                    ->where('id','!=',1)
                                    ->pluck('name','id')
                                    ->all();

        
        return view('customer_wallets.create',
                        compact('reasons' ));
        return view('customer_wallets.create');
    }

    public function store(Request $request)
    {
        request()->validate([
            'customer_id'           => 'required',
            'wallet_reason_id'      => 'required|numeric|min:0',
            'in_amount'             => 'required|numeric|min:0',
        ]);
        
        $data       = Customer_has_wallet::create($request->all());
        
        $payment    = DB::table('payment_rides')
                        ->where('payment_rides.status_id',6)
                        ->where('payment_rides.customer_id',$request->customer_id)
                        ->where('payment_rides.bill_paid',0)
                        // ->whereDate('payment_rides.updated_at',$this->today)
                        ->update([
                                    'bill_paid'      => $request->in_amount
                                ]);
                                
                                
      
        return redirect()
                ->route('customer_wallets.index')
                ->with('success','Transaction added successfully.');
    }

     public function show($id)
    {

        $data       =  DB::table('customer_has_wallets')
                        ->orderBy('customer_has_wallets.created_at','DESC')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_has_wallets.customer_id')
                        ->leftjoin('riders', 'riders.id', '=', 'customer_has_wallets.rider_id')
                        ->leftjoin('wallet_reasons', 'wallet_reasons.id', '=', 'customer_has_wallets.wallet_reason_id')
                        ->select(
                                    'customer_has_wallets.*',
                                    'customers.name as customer_name',
                                    'customers.id as cus_id',
                                    'customers.contact_no as contact_no',
                                    'wallet_reasons.name as reason_name',
                                    'riders.name as rider_name'
                                )
                    ->where('customer_has_wallets.id', $id)
                    ->first();

        $customer_id  = $data->cus_id;
        // $wallet_transaction = DB::table('customer_has_wallets')
        //             // ->select('customer_has_wallets.customer_id',
        //             //         DB::raw('SUM((customer_has_wallets.in_amount)-(customer_has_wallets.out_amount)) AS net_amount')
        //             //         )
        //             ->select('customer_has_wallets.*')
        //             // ->groupBy('customer_has_wallets.customer_id')
        //             ->where('customer_has_wallets.customer_id', $customer_id)
        //             ->get()
        //             ->all();


        

        $in_amount      = DB::table('customer_has_wallets')
                                    ->where('customer_has_wallets.customer_id',$customer_id)
                                    ->sum('in_amount');
        $out_amount     = DB::table('customer_has_wallets')
                                ->where('customer_has_wallets.customer_id',$customer_id)
                                ->sum('out_amount');

        $current_bal    = $in_amount  - $out_amount;


        return view('customer_wallets.show',compact('data','current_bal'));
    }


    public function edit($id)
    {
        $data   =  DB::table('customer_has_wallets')
                    ->orderBy('customer_has_wallets.created_at','DESC')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_has_wallets.customer_id')
                    ->leftjoin('wallet_reasons', 'wallet_reasons.id', '=', 'customer_has_wallets.wallet_reason_id')
                    ->select(
                                'customer_has_wallets.*',
                                'customer_has_wallets.in_amount as v_amount',
                                'customers.name as customer_name',
                                
                                'customers.contact_no as contact_no',
                                'wallet_reasons.name as reason_name'
                            )
                ->where('customer_has_wallets.id', $id)
                ->first();

        $reasons    = DB::table('wallet_reasons')
                        ->where('id','!=',1)
                        ->pluck('name','id')
                        ->all();

        return view('customer_wallets.edit',compact('data','reasons'));
    }


    public function update(Request $request, $id)
    {
        $data           = Customer_has_wallet::findOrFail($id);
                            $this->validate($request,[
                                'customer_id'           => 'required',
                                'wallet_reason_id'      => 'required|numeric|min:0',
                                'in_amount'             => 'required|numeric|min:0',
                            ]);

        $lst_in_amount = $data->in_amount;
        // dd($lst_in_amount);

        $upd            = $data->update($request->all());

        $payment        = DB::table('payment_rides')
                            ->where('payment_rides.status_id',6)
                            ->where('payment_rides.customer_id',$request->customer_id)
                            ->where('payment_rides.bill_paid',$lst_in_amount)
                            // ->whereDate('payment_rides.updated_at',$this->today)
                            ->update([
                                        'bill_paid'      => $request->in_amount
                                    ]);

        return redirect()
                ->route('customer_wallets.index')
                ->with('success','Transaction updated successfully.');
    }

    public function destroy(Request $request)
    {
        $ids    = $request->ids;
        $data   = DB::table("customer_wallets")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>" Transaction deleted successfully."]);
    }




}
