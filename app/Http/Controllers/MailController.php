<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Mail;
use App\Models\Order;
use Exception;
use App\Mail\customMail;
class MailController extends Controller {
    // function __construct()
    // {
    //      $this->middleware('permission:addon_rate_list-list', ['only' => ['index','show']]);
    //      $this->middleware('permission:addon_rate_list-create', ['only' => ['create','store']]);
    //      $this->middleware('permission:addon_rate_list-edit', ['only' => ['edit','update']]);
    //      $this->middleware('permission:addon_rate_list-delete', ['only' => ['destroy']]);
    // }


    public function fn_get_addon_amount($order_id){

        $all_addons             =  DB::table('order_has_addons')
                                    ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                    ->where('order_has_addons.order_id',$order_id)
                                    // ->select(
                                    //             'order_has_addons.order_id',
                                    //             'addons.name',
                                    //             'addons.rate',
                                    //             'order_has_addons.id'
                                    //         )
                                    ->sum('addons.rate');
                                    // ->get();
        return $all_addons;


    }

    public function fn_get_service_amount($order_id){
        $data                   = "";
        $tot                    = 0;
        $all_services           =  DB::table('orders')
                                    ->leftjoin('order_has_services', 'order_has_services.order_id', '=', 'orders.id')
                                    // ->leftjoin('customer_has_services', 'customer_has_services.customer_id', '=', 'orders.customer_id')
                                    ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                    ->where('order_has_services.order_id',$order_id)
                                    // ->where('services.unit_id',2)
                                    ->select(
                                                'orders.id',
                                                'services.unit_id',
                                                'orders.customer_id',
                                                'order_has_services.qty',
                                                'order_has_services.weight',
                                                'order_has_services.service_id',
                                                'order_has_services.cus_service_rate as service_rate',
                                            )
                                    ->get();

                                // dd($all_services);
        foreach ($all_services as $key => $value) {
            if($value->unit_id == 1){
                 // If unit is KG:1 then rate will be based on service weight
                // $data           =  DB::table('customer_has_services')
                //                     ->where('customer_has_services.customer_id',$value->customer_id)
                //                     ->where('customer_has_services.service_id',$value->service_id)
                //                     ->select(
                //                                 'customer_has_services.service_id',
                //                                 'customer_has_services.service_rate',
                //                             )
                //                     ->first();
                $tot            += (($value->weight) * ( $value->service_rate));

                //                 echo "Qty: ". $value->weight. "<br>";
                // echo "<pre>";
                // print_r($data);
                // echo "</pre>";
            }else if($value->unit_id == 2){
                // If unit is item:2 then rate will be based on item rate which will be different for all items

                $items      =  DB::table('order_has_items')
                                    // ->leftjoin('order_has_items', 'order_has_items.service_id', '=', 'order_has_services.service_id')
                                    // ->leftjoin('customer_has_items', 'customer_has_items.item_id', '=', 'order_has_items.item_id')
                                    ->where('order_has_items.order_id',$value->id)
                                    ->where('order_has_items.service_id',$value->service_id)

                                    // ->where('customer_has_items.customer_id',$value->customer_id)

                                    ->select(
                                                'order_has_items.order_id',
                                                'order_has_items.item_id',
                                                'order_has_items.pickup_qty',
                                                'order_has_items.cus_item_rate as item_rate',
                                                'order_has_items.service_id as service_id'
                                            )
                                    ->get();
                                    // dd($items);
                foreach ($items as $item_key => $item_value) {

                    // $rec      =  DB::table('customer_has_items')
                    //                 // ->where('customer_has_items.order_id',$value->id)
                    //                 ->where('customer_has_items.service_id',$item_value->service_id)
                    //                 ->where('customer_has_items.item_id',$item_value->item_id)
                    //                 ->where('customer_has_items.customer_id',$value->customer_id)
                    //                 ->select(
                    //                             // 'customer_has_items.order_id',
                    //                             'customer_has_items.item_id',
                    //                             'customer_has_items.item_rate',
                    //                         )
                    //                 ->first();

                                    // dd($rec);
                    // echo $item_key."pick_up:  ". ($item_value->pickup_qty) . "<br>";
                    $tot   +=   (($item_value->pickup_qty) * ($item_value->item_rate));
                }
                // dd($tot);
            }else if($value->unit_id == 3){
                // If unit is piece:3 then rate will be based on rate which will be same for all items
                // $data           =  DB::table('customer_has_services')
                //                     ->where('customer_has_services.customer_id',$value->customer_id)
                //                     ->where('customer_has_services.service_id',$value->service_id)
                //                     ->select(
                //                                 'customer_has_services.service_id',
                //                                 'customer_has_services.service_rate',
                //                             )
                //                     ->first();
                $tot            += (($value->qty) * ( $value->service_rate));


            }

        }

        return $tot;


    }

    public function fn_get_delivery_charges(){

        $data                   =  DB::table('delivery_charges')
                                    ->select(
                                                'order_amount',
                                                'delivery_charges'
                                            )
                                    ->first();
                                    // echo $data->order_amount;
                                    // dd($data);
        return $data;


    }

    public function get_user($role_id){
        $user                   = DB::table('users')
                                    ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                    ->leftjoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                                    ->where('roles.id', $role_id)
                                    ->select(
                                                'users.email as user_email',
                                                // 'users.id as id'
                                                // 'users.name as user_name'
                                            )->get();
                                            // ->toArray();
                                    // ->pluck('name','id')
                                    
        $cus_users = array();
        
        foreach($user as $key => $value){
          array_push($cus_users,$value->user_email);
        }

        return $cus_users;
    }

    public function send_exception($e)
    {
        // $exception          = array();
        // $exception['line']  = "123";
        // $exception['file']  = "123";
        // $exception['msg']   = "42";

        $exception          = array();
        $exception['line']  = $e->getLine();
        $exception['file']  = $e->getFile();
        $exception['msg']   = $e->getMessage();
     
      
        try {

            $emails = env("DEVELOPER_EMAIL");

            // return view('mails.exception',
            // compact('exception'
            //     )
            // );
            // dd($emails);

            $data       = array(
                "exception"                  => $exception,
            );
            
            Mail::send('mails.exception', $data, function($message) use ($emails) {
                $message->from(env("MAIL_FROM_ADDRESS"),'Washup');
                $message->to($emails)->subject('Exception');
                // var_dump( Mail:: failures());
            });

        }catch (Exception $e) {
            // echo $e;
            return 0;
        }
    }
    
    public function send_cancel_mail($order_id, $reason)
    {
        
        
        // Customer name, Customer Phone number, Rider name, Timeslot, Reason, Order status, Order Number, Date.
        if(!(isset($order_id))){
            return 0;
        }

        $route_record       = DB::table('route_plans')
                                ->leftjoin('orders', 'orders.id', '=', 'route_plans.order_id')
                                ->leftjoin('statuses', 'statuses.id', '=', 'orders.status_id')
                                ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                ->leftjoin('riders', 'riders.id', '=', 'route_plans.rider_id')
                                ->leftjoin('time_slots', 'time_slots.id', '=', 'route_plans.timeslot_id')
                                ->select(
                                            'orders.id as order_id',
                                            DB::raw('DATE_FORMAT(orders.pickup_date, "%d-%m-%Y") as pickup_date'),
                                            DB::raw('DATE_FORMAT(orders.delivery_date, "%d-%m-%Y") as delivery_date'),
                                            'customers.name as customer_name',
                                            'customers.contact_no as contact_no',
                                            'customers.email as customer_email',
                                            'riders.name as rider_name',
                                            'statuses.name as status_name',
                                            DB::raw('DATE_FORMAT(route_plans.updated_at, "%d-%m-%Y") as updated_at'),
                                            DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as time_slot')
                                        )
                                ->where('orders.id', $order_id)
                                ->where('route_plans.complete',0)
                                ->where('route_plans.rider_id',2)
                                ->first();

        if($route_record){
            // if(true){
            $csr_data       = $this->get_user(2);  // role = 2 ; Customer Service Rep
            $rs_data        = $this->get_user(3);  // role = 3 ; Rider's Supervisor
          
          
          
           $emails = array_merge($csr_data,$rs_data);
        //   dd($emails);
            $data           = $route_record;
                // return view('mails.cancel',
                // compact('route_record',
                //         'csr_data'
                //     )
                // );

          

            $data           = array(
                                    "route_record"       => $route_record,
                                    "csr_data"           => $csr_data,
                                    'reason'             => $reason
                                );
            try {

                foreach($emails as $key => $value){
                    //check if
                    if(filter_var($emails[$key], FILTER_VALIDATE_EMAIL) === FALSE) {
                        //throw exception if email is not valid
                        $msg    = $emails[$key] . "is not valid email";
                        throw new Exception($msg);
                    }
                }
                

                // dd($emails);


                Mail::send('mails.cancel', $data, function($message) use ($emails) {
                    $message->from(env("MAIL_FROM_ADDRESS"),'Washup');
                    $message->to($emails)->subject('Order Cannceled');
                    // var_dump( Mail:: failures());
                });
                }catch (Exception $e) {
                    // echo $e;
                    return 0;
                }

            return 1;

        }else{
            return 0;
        }

    }


    public function send_invoice($order_id) {

        // $order_id           = 82;
        if(!(isset($order_id))){
            return 0;
        }
        $orders               = DB::table('orders')
                                ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                ->where('orders.id', $order_id)
                                ->select(
                                            'orders.*',
                                            DB::raw('DATE_FORMAT(orders.pickup_date, "%d-%m-%Y") as pickup_date'),
                                            DB::raw('DATE_FORMAT(orders.delivery_date, "%d-%m-%Y") as delivery_date'),
                                            'customers.name as customer_name',
                                            'customers.contact_no as contact_no',
                                            'customers.email as customer_email',
                                        )
                                ->first();
                                // dd($orders);

        if($orders !=null){

            // $d_charges                  = $this->fn_get_delivery_charges();
            // $service_tot                = $this->fn_get_service_amount($order_id);
            // $addon_tot                  = $this->fn_get_addon_amount($order_id);
            // $tot                        = ( $service_tot + $addon_tot);
            // if($tot < ($d_charges->order_amount)){
            //     $delivery_charges = $d_charges->delivery_charges;   // delivery charges will be applied
            // }else{
            //     $delivery_charges = 0;                              // delivery charges will not be applied
            // }
            // $orders->delivery_charges = $delivery_charges;

            // if($orders->ref_order_id!= NULL){
            //     $order_id = $orders->ref_order_id;
            // }

            $selected_services      = DB::table('order_has_services')
                                        ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                        ->leftjoin('units', 'units.id', '=', 'services.unit_id')
                                        ->where('order_has_services.order_id', $order_id)
                                        ->select(
                                                    'services.id as service_id',
                                                    'units.id as unit_id',
                                                    'services.name as service_name',
                                                    'order_has_services.weight as weight',
                                                    'order_has_services.qty as service_qty',
                                                )
                                                ->orderBy('order_number','ASC')
                                        ->get()
                                        ->all();

                                        // dd($selected_services)  ;

                                            $record = array();
            foreach ($selected_services as $service_key => $service_value) {

                if($service_value->unit_id == 2){

                    // unit id : 2 means item wise rate
                    $selected_items         = DB::table('order_has_items')
                                                ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                                // ->leftjoin('customer_has_items', 'customer_has_items.item_id', '=', 'order_has_items.item_id')
                                                ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                                // ->leftjoin('order_has_services', 'order_has_services.order_id', '=', 'order_has_items.order_id')
                                                ->where('order_has_items.order_id', $order_id)
                                                // ->where('customer_has_items.service_id', $service_value->service_id)
                                                // ->where('customer_has_items.customer_id', $orders->customer_id)
                                                ->where('order_has_items.service_id', $service_value->service_id)
                                                ->select(
                                                            'items.id as item_id',
                                                            'items.short_name as item_name',
                                                            'order_has_items.service_id as service_id',
                                                            'order_has_items.pickup_qty as pickup_qty',
                                                            'services.name as service_name',
                                                            // 'customer_has_items.item_rate as item_rate',
                                                            'order_has_items.cus_item_rate as item_rate',
                                                            'order_has_items.id as ord_itm_id'
                                                        )
                                                ->get()
                                                ->all();

                }else{
                    $selected_items         = DB::table('order_has_items')
                                                ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                                ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                                ->leftjoin('order_has_services', 'order_has_services.service_id', '=', 'order_has_items.service_id')
                                                ->where('order_has_items.order_id', $order_id)
                                                ->where('order_has_services.order_id', $order_id)
                                                ->where('order_has_items.service_id', $service_value->service_id)
                                                ->select(
                                                            'items.id as item_id',
                                                            'items.short_name as item_name',
                                                            'order_has_items.service_id as service_id',
                                                            'order_has_items.pickup_qty as pickup_qty',
                                                            'order_has_services.cus_service_rate as service_rate',
                                                            'services.name as service_name',
                                                            'order_has_items.id as ord_itm_id'
                                                        )
                                                ->get()
                                                ->all();
                                                // dd($selected_items);
                }


                // $addons = array();

                foreach ($selected_items as $item_key => $item_value) {
                    $selected_addons        = DB::table('order_has_addons')
                                                ->leftjoin('addons', 'addons.id', '=', 'order_has_addons.addon_id')
                                                ->where('order_has_addons.order_id', $order_id)
                                                ->where('order_has_addons.service_id', $service_value->service_id)
                                                ->where('order_has_addons.item_id', $item_value->item_id)
                                                ->where('order_has_addons.ord_itm_id', $item_value->ord_itm_id)
                                                ->select('addons.id as addon_id',
                                                        'addons.name as addon_name',
                                                        // 'addons.rate as addon_rate',
                                                        'order_has_addons.cus_addon_rate as addon_rate',
                                                        'order_has_addons.item_id as item_id',
                                                        'order_has_addons.service_id as service_id',
                                                        'order_has_addons.ord_itm_id as ord_itm_id',
                                                        )
                                                ->get()
                                                ->all();

                    $selected_items[$item_key]->addons = $selected_addons;


                }

                $record[$service_value->service_id]         = $service_value;
                $record[$service_value->service_id]->items  = $selected_items;

            }

            //   dd(env("MAIL_FROM_ADDRESS"))                      ;

            // $data = $orders;
            // return view('mails.index',
            // compact('data',
            //         'record'
            //     )
            // );

            // dd("stopped");

            if($record){
                $to_name    = $orders->customer_name;
                $to_email   = $orders->customer_email;
                // $to_email   = 'someone@gmail,,,.com';
                $data       = array(
                                    "data"                  => $orders,
                                    "record"                => $record,
                                );
                // $file       = public_path('mail_assets/terms_and_conditions.pdf');
                try {
                    //check if
                    if(filter_var($to_email, FILTER_VALIDATE_EMAIL) === FALSE) {
                      //throw exception if email is not valid
                      throw new Exception("$to_email is not valid email");
                    }
                   $rec  =  Mail::send('mails.index', $data, function($message) use ($to_name, $to_email) {
                        // $message->attach($file);
                        $message->from(env("MAIL_FROM_ADDRESS"),'Washup');
                        $message->to($to_email, $to_name)->subject('Updated Invoice');
                    });

                    // dd($rec);
                  }catch (Exception $e) {
                    // echo $e;

                    return 0;
                 }

               return 1;

            }else{
               return 0;
            }



        }else{
            return 0;
        }




    }
    
    public function sendMail(){
        $email = "aquib20034@gmail.com";
        // dd($email);
        Mail::to($email)->send(new customMail());
        
        if( count(Mail::failures()) > 0 ){
            dd("There seems to be a problem. Please try again in a while");
            // session::flash('message','There seems to be a problem. Please try again in a while'); 
           return redirect()->back(); 
        }else{            
            dd("Thanks for your message. Please check your mail for more details!");
            // session::flash('message','Thanks for your message. Please check your mail for more details!'); 
            return redirect()->back();  
        }
    }


    public function send_test($email) {

        // $to_name    = $orders->customer_name;
        $to_email   = $email;
        $data       = array("data" => "test---test");

        try {
            if(filter_var($to_email, FILTER_VALIDATE_EMAIL) === FALSE) {
                throw new Exception("$to_email is not valid email");
            }

            Mail::send('mails.test', $data, function($message) use ($to_email) {
                        $message->from(env("MAIL_FROM_ADDRESS"),'Washup');
                        $message->to($to_email, "test-name")->subject('Updated Invoice');
                    });
        }catch (Exception $e) {
            echo $e;
            return 0;
        }

        return 1;
    }
}
