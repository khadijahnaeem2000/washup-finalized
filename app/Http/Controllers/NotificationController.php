<?php


namespace App\Http\Controllers;

use DB;
use App\Models\Addon;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{

    public function createAcronym($string) {
        $output = null;
        $token  = strtok($string, ' ');
        while ($token !== false) {
            $output .= $token[0];
            $token = strtok(' ');
        }
        return $output;
    }

    public function fn_get_order_history($order_id, $status_id){

        if( (isset($order_id)) && (isset($status_id)) ){
            $record     =  DB::table('order_histories')
                            ->where('order_histories.order_id',$order_id)
                            ->where('order_histories.status_id',$status_id)
                            ->select(
                                        'order_histories.detail',
                                        'order_histories.order_id',
                                        'order_histories.status_id',
                                    )
                            ->latest()
                            ->first();

            if(isset($record)){
               return $record->detail;
            }else{
                return ;
            }

        }else{
            return ;
        }
    }

    public function send_modification($order_id, $state){
        // dd($msg);
        $msg_id             = 8;   // 8:content verified
        $msg                = "";
        $tot_items          = 0;
        $tot_amount         = 0;

        $data             = DB::table('order_has_services')
                                ->leftjoin('orders', 'orders.id', '=', 'order_has_services.order_id')
                                ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                ->select(
                                            'orders.id as order_id',
                                            'customers.contact_no',
                                            'order_has_services.qty',
                                            'order_has_services.weight',
                                            'customers.id as customer_id',
                                            'order_has_services.service_id',
                                            'services.name as service_name',
                                            'customers.name',
                                        )
                                ->where('order_has_services.order_id',$order_id)
                                ->orderBy('order_number','ASC')
                                ->get();

        if(!($data->isEmpty())){
            foreach ($data as $key => $value) {
                $data[$key]->acroynm    = $this->createAcronym($value->service_name);
                $msg                    = $msg." - ".($value->acroynm . ": " . $value->qty . "pcs, ". "W: ". $value->weight. "Kg\r\n");
                $tot_items             += $value->qty;
            }

            $service_tot                = $this->fn_get_service_amount($data[0]->order_id);
            $addon_tot                  = $this->fn_get_addon_amount($data[0]->order_id);
            
            $tax_tot                    = ($this->fn_add_tax($data[0]->order_id)); 
            $tot_amount                 = ($service_tot + $addon_tot + $tax_tot ); 

            // $tot_amount                 = ($this->fn_add_tax($service_tot + $addon_tot)); 

            $txt  = '';
            if($state == 'both'){
                $txt = "qty & weight";
            }elseif($state == 'weight'){
                $txt = "weight";
            }elseif($state == 'qty'){
                $txt = "qty";
            }elseif($state == 'service'){
                $txt = "service";
            }
            $contact_no     = $data[0]->contact_no;
            $sender         = "WASHUP";
            $msg            = "Order ". $order_id. " Details (revised):\r\n".$msg."Total: ".$tot_items."Pcs, Rs. ". $tot_amount;
            
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );

            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            echo "something went wrong";
        }
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
                                                'order_has_items.service_id as service_id',
                                                'order_has_items.cus_item_rate as item_rate',
                                            )
                                    ->get();
                                    // dd($items);
                foreach ($items as $item_key => $item_value) {
               
                    $tot   +=   (($item_value->pickup_qty) * ($item_value->item_rate));
                }
                // dd($tot);
            }else if($value->unit_id == 3){
              // If unit is piece:3 then rate will be based on rate which will be same for all items
              $tot            += (($value->qty) * ( $value->service_rate));
            }
           
        }

        return $tot;
                           

    }

    public function fn_get_addon_amount($order_id){
        $amount                 = 0;
        $all_addons             =  DB::table('order_has_addons')
                                    ->leftjoin('order_has_items', 'order_has_items.id', '=', 'order_has_addons.ord_itm_id')
                                    ->where('order_has_addons.order_id',$order_id)
                                    ->select(
                                                'order_has_addons.id',
                                                'order_has_addons.order_id',
                                                'order_has_items.pickup_qty',
                                                'order_has_addons.cus_addon_rate as rate',
                                            )
                                    ->get();

        foreach ($all_addons as $key => $value) {
            for ($i=0; $i < $value->pickup_qty; $i++) { 
                $amount += $value->rate;
            }
        }
        return $amount;
                           

    }

    public function fn_add_tax($order_id){
        $delivery_charges       = 0;  
        $vat_charges            = 0; 
        
        if(!(isset($order_id))){
            return 0;
        }
        $record                 = Order::select('vat_charges','delivery_charges')->find($order_id);
        if($record){
            if( (isset($record->vat_charges)) && (($record->vat_charges) != null)){
                $vat_charges =  $record->vat_charges;
            } 
            if( (isset($record->delivery_charges)) && (($record->delivery_charges) != null)){
                $delivery_charges =  $record->delivery_charges;
            }
            return ($vat_charges + $delivery_charges);
        }else{
            return 0;
        }

    }

    public function get_order_detail($order_id,$msg_id){
        $data           = DB::table('orders')
                            ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                            ->select(
                                        'customers.name',
                                        'customers.contact_no',
                                        'orders.customer_id',
                                        'orders.pickup_date',
                                        'orders.pickup_timeslot',
                                        'orders.delivery_date',
                                        'orders.delivery_timeslot'
                                    )
                            ->where('orders.id',$order_id)
                            ->first();
               
        if(isset($data)){
            $rec        = DB::table('customer_has_messages')
                            ->select(
                                        'customer_has_messages.message_id',
                                        'customer_has_messages.customer_id',
                                    )
                            ->where('customer_has_messages.customer_id',$data->customer_id)
                            ->where('customer_has_messages.message_id',$msg_id)
                            ->first();
            if($rec){
                return $data;
            }else{
                return null;
            }
        }else{
            return null;
        }

    }

    // Dummy msg for testing purpose
    public function send_sms(){
        $phone_no       = "03139120034";
        $sender         = "WASHUP";
        $msg            = "It is just a msg!!!!!!";
        $url            = "https://Bsms.its.com.pk/api.php?key=9f77fe75fea7771ae3b311a64b840c66";

        $dataArray      = array(
            "receiver"  => $phone_no,
            "msgdata"   => $msg,
            "sender"    => $sender
        );


        $ch             = curl_init();
        $data           = http_build_query($dataArray);
        
        $getUrl         = $url."&".$data;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);

        $response       = curl_exec($ch);

        if(curl_error($ch)){
            echo 'Request Error:' . curl_error($ch);
        }else{
            echo $response;
        }
        curl_close($ch);
    }

    // connection and setting up curl to send msg
    public function send_msg($dataArray){
      
        if(isset($dataArray)){
            $ch             = curl_init();
            $data           = http_build_query($dataArray);
            $url            = "https://Bsms.its.com.pk/api.php?key=9f77fe75fea7771ae3b311a64b840c66";
            $getUrl         = $url."&".$data;
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $getUrl);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            $response       = curl_exec($ch);

            if(curl_error($ch)){
                $res  = ('Request Error:' . curl_error($ch));
            }else{
                $res  =  $response;
            }
            
            curl_close($ch);
            if($res){
                return $res;
            }else{
                return true;
            }
            
        }else{
            return false;
        }
    }
    
    // Pickup Confirmation (Trigger – when order is booked by CSR on Dashboard)
    public function pickup_msg($order_id){
        $msg_id             = 1;
        $data               = ($this->get_order_detail($order_id,$msg_id));
        if(isset($data)){
            $dt             = date_create($data->pickup_date);
            $contact_no     = $data->contact_no;
            $sender         = "WASHUP";
            // $msg            = "Hi ". ucwords($data->name). ",your laundry will be collected on ".(date_format( $dt ,"d F, Y"))." between ".($data->pickup_timeslot)." – (Name, Date, Timeslot)\r\nFor any changes Call/WhatsApp: 03175286379 ";
            $msg            = "Hi ". ucwords($data->name). ", we will pick your laundry on ".(date_format( $dt ,"d F, Y"))." between ".($data->pickup_timeslot)."\r\nCall/WhatsApp: 03175286379 ";
            
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );

            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            return "sorry! record not found.";
        }
    }

    // Drop off Confirmation (Trigger – when dropoff is booked by CSR on Dashboard)
    public function drop_msg($order_id){
        $msg_id             = 2;
        $data               = ($this->get_order_detail($order_id,$msg_id));
        if(isset($data)){
            $dt             = date_create($data->delivery_date);
            $contact_no     = $data->contact_no;
            $sender         = "WASHUP";
            // $msg            = "Hi ". ucwords($data->name). ",your laundry will be delivered on ".(date_format( $dt ,"d F, Y"))." between ".($data->delivery_timeslot)." – (Name, Date, Timeslot)\r\nFor any changes Call/WhatsApp: 03175286379 ";
            $msg            = "Hi ". ucwords($data->name). ", we will drop your laundry on ".(date_format( $dt ,"d F, Y"))." between ".($data->delivery_timeslot)."\r\nCall/WhatsApp: 03175286379 ";
            
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );

            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            return "sorry! record not found.";
        }
    }

    // Pickup & Delivery Confirmation (Trigger – when pickup & dropoff is booked by CSR on Dashboard)
    public function pick_n_drop_msg($order_id){
        $msg_id             = 3;
        $data               = ($this->get_order_detail($order_id,$msg_id));
        if(isset($data)){
            $dt             = date_create($data->delivery_date);
            $contact_no     = $data->contact_no;
            $sender         = "WASHUP";
            // $msg            = "Hi ". ucwords($data->name). ",your laundry will be delivered on ".(date_format( $dt ,"d F, Y"))." between ".($data->delivery_timeslot)." – (Name, Date, Timeslot)\r\nFor any changes Call/WhatsApp: 03175286379 ";
            $msg            = "Hi ". ucwords($data->name). ", we will pick & drop your laundry on ".(date_format( $dt ,"d F, Y"))." between ".($data->delivery_timeslot)." \r\nCall/WhatsApp: 03175286379 ";
            
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );

            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            return "sorry! record not found.";
        }
    }

    // Complain (Trigger – when a complaint is added by the CSR in the dashboard)
    public function complaint_msg($order_id){
        $msg_id             = 7;

        $data               = DB::table('complaints')
                                ->leftjoin('customers', 'customers.id', '=', 'complaints.customer_id')
                                ->leftjoin('complaint_natures', 'complaint_natures.id', '=', 'complaints.complaint_nature_id')
                                ->select(
                                            'customers.name',
                                            'customers.contact_no',
                                            'complaints.customer_id',
                                            'complaints.order_id',
                                            'complaint_natures.name as complaint_name',
                                        )
                                ->where('complaints.order_id',$order_id)
                                ->first();
        if(isset($data)){
            $rec            = DB::table('customer_has_messages')
                                ->select(
                                            'customer_has_messages.message_id',
                                            'customer_has_messages.customer_id',
                                        )
                                ->where('customer_has_messages.customer_id',$data->customer_id)
                                ->where('customer_has_messages.message_id',$msg_id)
                                ->first();
            if($rec){
                $complaint      = $data->complaint_name;
                $contact_no     = $data->contact_no;
                $sender         = "WASHUP";
                // $msg            = "Hi ". ucwords($data->name). ", Thank you for your valuable feedback. Your complaint for '".$complaint."' has been lodged. The management is looking into the matter & will get back to you shortly (Name, Complaint type)\r\n For any further info Call/WhatsApp: 03175286379";
                $msg            = "Hi ". ucwords($data->name). ", Thank you for your valuable feedback. Your '".$complaint."' complaint has been lodged. We are looking into the matter & will get back to you shortly\r\nCall/WhatsApp: 03175286379";
                $dataArray      = array(
                    "receiver"  => $contact_no,
                    "msgdata"   => $msg,
                    "sender"    => $sender
                );
    
                $rec =  ($this->send_msg($dataArray));
                return $rec;
            }
        }else{
            return null;
        }
    }

    // Pickup Details – (Trigger When final order is pressed by rider for existing location)
    public function pickup_detail($order_id){
        $msg_id             = 2;
        $msg                = "";
        $tot_items          = 0;
        $tot_amount         = 0;

        $data               = DB::table('order_has_services')
                                ->leftjoin('orders', 'orders.id', '=', 'order_has_services.order_id')
                                ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                                ->leftjoin('services', 'services.id', '=', 'order_has_services.service_id')
                                ->select(
                                            'orders.id as order_id',
                                            'customers.contact_no',
                                            'order_has_services.qty',
                                            'order_has_services.weight',
                                            'customers.id as customer_id',
                                            'order_has_services.service_id',
                                            'services.name as service_name',
                                            'customers.name',
                                        )
                                ->where('order_has_services.order_id',$order_id)
                                ->orderBy('order_number','ASC')
                                ->get();

        if(!($data->isEmpty())){
            foreach ($data as $key => $value) {
                $data[$key]->acroynm    = $this->createAcronym($value->service_name);
                $msg                    = $msg." - ".($value->acroynm . ":" . $value->qty . " pcs, ". "W:". $value->weight. "Kg\r\n");
                $tot_items             += $value->qty;
            }

            $service_tot                = $this->fn_get_service_amount($data[0]->order_id);
            $addon_tot                  = $this->fn_get_addon_amount($data[0]->order_id);
            $tax_tot                    = ($this->fn_add_tax($data[0]->order_id)); 
            $tot_amount                 = ($service_tot + $addon_tot + $tax_tot ); 



            $contact_no     = $data[0]->contact_no;
            $sender         = "WASHUP";
            // $msg            = "Hi ". ucwords($data[0]->name). ",your order details are as follows: ".$msg." Total: ".$tot_items."Items, Total Bill: Rs. ". $tot_amount ." - if there are any errors you will receive another SMS shortly. (Name, services, items, weight, bill) For any further info Call/WhatsApp: 03175286379 ";
            $msg            = "Order ". $order_id. " Details:\r\n".$msg."Total: ".$tot_items."Pcs, Rs. ". $tot_amount ."\r\nCall/WhatsApp: 03175286379 ";
            
            
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );

            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            echo "something went wrong";
        }
    }

    // Order modification (Qty Changed) – (Trigger when the complete order has been verified by the tagger – SMS only to be sent if there is a change in Qty – if no change then NO SMS REQUIRED)
    // Order modification (Weight Changed) – (Trigger when the complete order has been verified by the tagger – SMS only to be sent if there is a change in WEIGHT – if no change as the rider NO SMS REQUIRED
    // Order modification (Qty & Weight Changed) – (Trigger when the complete order has been verified by the tagger – SMS only to be sent if there is a change in Qty & WEIGHT – if no change as the rider NO SMS REQUIRED)
    public function order_modified($order_id){
        $qty_change         = false; 
        $weight_change      = false; 
        $service_change     = false;

        $status_id          = 4 ; //4: picked up by rider
        $order_pickup       = $this->fn_get_order_history($order_id, $status_id);

        $status_id          = 8 ; //8: content verified by tagger
        $order_verify       = $this->fn_get_order_history($order_id, $status_id);
        

        
        if( (isset($order_pickup)) && (isset($order_verify)) ){
            $order_pickup       = json_decode($order_pickup);
            $order_verify       = json_decode($order_verify);

            // checking : is qty changed
            if(($order_pickup->tot_qty)  != ($order_verify->tot_qty) ){
                $qty_change     = true;
            }

            // checking : is weight changed
            if(($order_pickup->tot_weight)  != ($order_verify->tot_weight) ){
                $weight_change  = true;
            }

            if(isset($order_pickup->services)){
                $op_length  = (array) $order_pickup->services;
            }else{
                $op_length = array();
            }
            // $op_length  = (array) $order_pickup->services;
            $ov_length  = (array) $order_verify->services;

            // checking : is service changed | setting length of arrays 
            if( (count($op_length)) == (count($ov_length)) ){
                $ov_ids = array();
                $op_ids = array();

                foreach ($order_verify->services as $ov_key => $ov_value) {
                    $ov_ids[]= $ov_value->service_id;
                }
                foreach ($order_pickup->services as $op_key => $op_value) {
                    $op_ids[]= $op_value->service_id;
                }

            

                // checking ids exist in another array
                foreach ($op_ids as $value) {
                    if(!(in_array($value, $ov_ids))){
                        $service_change = true;
                    }
                }
            }
            if(($weight_change) && ($qty_change)){
                // echo "both changed";
                $this->send_modification($order_id, "both");
            }elseif($weight_change){
                // echo "weight changed";
                $this->send_modification($order_id, "weight");
            }elseif($qty_change){
                // echo "qty changed";
                $this->send_modification($order_id, "qty");
            }elseif($service_change){
                // echo "service changed";
                $this->send_modification($order_id, "service");
            }else{
                return "no modification occured";
            }
        }else{
           return "sorry! something went wrong.";
        }
    }

    // Payment SMS - (Trigger when the rider presses payment):
    public function payment_msg($transaction_id){
        $msg_id             = 9;
        $net_amount         = 0;
        $data               = DB::table('customer_has_wallets')
                                ->leftjoin('customers', 'customers.id', '=', 'customer_has_wallets.customer_id')
                                ->select(
                                            'customers.name',
                                            'customers.contact_no',
                                            'customer_has_wallets.customer_id',
                                            'customer_has_wallets.in_amount'
                                        )
                                ->where('customer_has_wallets.id',$transaction_id)
                                ->first();

        $wallet             = DB::table('customer_has_wallets')
                                ->select(
                                            DB::raw('(sum(in_amount)-sum(out_amount)) AS bill')
                                        )
                                ->where('customer_has_wallets.customer_id',$data->customer_id)
                                ->first();
        if(isset($wallet->bill)){
            $net_amount = $wallet->bill;
        }
        

        if(isset($data)){
            $contact_no     = $data->contact_no;
            $sender         = "WASHUP";
            $msg            = "Hi ". ucwords($data->name). ",Thank you! we received Rs. ".$data->in_amount.",\r\nWallet: Rs.".$net_amount."\r\nCall/WhatsApp: 03175286379 ";
            
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );

            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            return "sorry! record not found.";
        }
    }

    // call this function when rides complete previous ride and the column named "complete" in route_plans table.
    public function last_mile($last_route_plan_id){
        $last_rec                   = DB::table('route_plans')
                                        ->select(
                                                    'route_plans.id',
                                                    'route_plans.seq',
                                                    'route_plans.rider_id',
                                                    'route_plans.created_at',
                                                )
                                        ->where('route_plans.id',$last_route_plan_id)
                                        ->first();
                                     

        if(isset($last_rec)){
            $next_seq               = (($last_rec->seq)+1);
            
            $rec                    = DB::table('route_plans')
                                        ->select(
                                                    'route_plans.id',
                                                    'route_plans.seq',
                                                    'route_plans.order_id',
                                                    'route_plans.rider_id',
                                                    'route_plans.status_id',
                                                    'route_plans.created_at',
                                                )
                                        ->where('route_plans.seq',$next_seq)
                                        ->where('route_plans.rider_id',$last_rec->rider_id)
                                        ->where('route_plans.created_at',$last_rec->created_at)
                                        ->first();
            if(isset($rec)){
                if($rec->status_id == 1){
                    // echo "call: last_mile_pickup";
                    // dd($rec->status_id);
                    $this->last_mile_pickup($rec);
                    
                }elseif($rec->status_id == 2){
                    // echo "call: last_mile_drop";
                    // dd($rec->status_id);
                    $this->last_mile_drop($rec);

                }elseif($rec->status_id == 3){
                    // echo "call: last_mile_pick_n_drop";
                    $this->last_mile_pick_n_drop($rec);

                }else{
                    return "sorry! record not found.";
                }
            }else{
                return "sorry! record not found.";
            }
        }else{
            return "sorry! record not found.";
        }

    }
    
    // Last Mile Pickup (Trigger – when the rider has completed an order and is on the way to the customer’s house): 
    // this function will be called from "last_mile function
    public function last_mile_pickup($rec){
        if(isset($rec)){
            $msg_id             = 10; // 10: last mile notification
            $data               = ($this->get_order_detail($rec->order_id,$msg_id));

            if(isset($data)){
                $contact_no     = $data->contact_no;
                $sender         = "WASHUP";
                // $msg            = "Hi ". ucwords($data->name). ",our rider will reach you in the next 10-15mins, please keep all the laundry ready by your doorstep. (Name)\r\nFor any changes Call/WhatsApp: 03175286379 ";
                $msg            = "Hi ". ucwords($data->name). ",our rider will be there in the next 10-15mins, please keep all the laundry ready by your doorstep\r\nCall/WhatsApp: 03175286379 ";
                
                $dataArray      = array(
                    "receiver"  => $contact_no,
                    "msgdata"   => $msg,
                    "sender"    => $sender
                );
    
                $rec            =  ($this->send_msg($dataArray));
                return $rec;
            }else{
                return "sorry! record not found.";
            }
        }else{
            return "sorry! record not found.";
        }
    }

    // Last Mile Delivery (Trigger – when the rider has completed an order and is on the way to the customer’s house)
    // this function will be called from "last_mile function
    public function last_mile_drop($rec){
        if(isset($rec->order_id)){
            $msg_id             = 10; // 10: last mile notification
            $data               = ($this->get_order_detail($rec->order_id,$msg_id));
            $polybags           = DB::table('order_has_bags')
                                    ->where('order_has_bags.order_id', $rec->order_id)
                                    ->count();

            $service_tot        = $this->fn_get_service_amount($rec->order_id);
            $addon_tot          = $this->fn_get_addon_amount($rec->order_id);
            $tax_tot            = ($this->fn_add_tax($rec->order_id)); 
            $tot_amount         = ($tax_tot + $service_tot + $addon_tot); 

            if(isset($data)){
                $contact_no     = $data->contact_no;
                $sender         = "WASHUP";
                // $msg            = "Hi ". ucwords($data->name). ", our rider will be there in the next 10-15mins, Order Details:\r\nBill: Rs.".$tot_amount."\r\n".$polybags."Poly bags\r\nFor any further info Call/WhatsApp: 03175286379";
                $msg            = "Hi ". ucwords($data->name). ", our rider will be there in the next 10-15mins, Order Details:\r\nBill: Rs.".$tot_amount."\r\n".$polybags."Poly bags\r\nCall/WhatsApp: 03175286379";
                
                $dataArray      = array(
                    "receiver"  => $contact_no,
                    "msgdata"   => $msg,
                    "sender"    => $sender
                );
    
                $rec            =  ($this->send_msg($dataArray));
                return $rec;
            }else{
                return "sorry! record not found.";
            }
        }else{
            return "sorry! record not found.";
        }
    }

    // Last Mile Pick & Drop (Trigger – when the rider has completed an order and is on the way to the customer’s house): 
    // this function will be called from "last_mile function
    public function last_mile_pick_n_drop($rec){
        if(isset($rec)){
            $msg_id             = 10; // 10: last mile notification
            $data               = ($this->get_order_detail($rec->order_id,$msg_id));
            $polybags           = DB::table('order_has_bags')
                                    ->where('order_has_bags.order_id', $rec->order_id)
                                    ->count();

            $service_tot        = $this->fn_get_service_amount($rec->order_id);
            $addon_tot          = $this->fn_get_addon_amount($rec->order_id);
            $tax_tot            = ($this->fn_add_tax($rec->order_id)); 
            $tot_amount         = ($service_tot + $addon_tot + $tax_tot ); 

            // $tot_amount         = ($this->fn_add_tax($service_tot + $addon_tot)); 

            if(isset($data)){
                $contact_no     = $data->contact_no;
                $sender         = "WASHUP";
                // $msg            = "Hi ". ucwords($data->name). ",our rider will reach you in the next 10-15mins, please keep your dirty laundry & payment ready. There are a total of ".$polybags." Poly bags in your order & the total bill is Rs. ".$tot_amount." (Name, Poly bags & bill)\r\nFor any further info Call/WhatsApp: 03175286379";
                $msg            = "Hi ". ucwords($data->name). ", our rider will reach you in the next 10-15mins, Order Details:\r\nBill Rs. ".$tot_amount."\r\n".$polybags." Poly bags\r\nCall/WhatsApp: 03175286379";
                
                $dataArray      = array(
                    "receiver"  => $contact_no,
                    "msgdata"   => $msg,
                    "sender"    => $sender
                );
    
                $rec            =  ($this->send_msg($dataArray));
                return $rec;
            }else{
                return "sorry! record not found.";
            }
        }else{
            return "sorry! record not found.";
        }
    }

    // Customer Cancellation by CSR (Pickup) - (Trigger when CSR cancels an order on dashboard)
    public function csr_cancel_pickup($order_id){
        $msg_id             = 11; // 11: cancel message notification
      
        $data               = ($this->get_order_detail($order_id,$msg_id));
        if(isset($data)){
            $dt             = date_create($data->pickup_date);
            $contact_no     = $data->contact_no;
            $sender         = "WASHUP";
            $msg            = "Hi ". ucwords($data->name). ",your laundry pickup request for ".(date_format( $dt ,"d F, Y"))." between ".($data->pickup_timeslot)." has been cancelled \r\nCall/WhatsApp: 03175286379";
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );
            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            return "sorry! record not found.";
        }

    }
    

    public function cancel_order($order_id){
        $rec        = DB::table('orders')
                        ->select(
                                    'orders.id as order_id',
                                    'orders.status_id'
                                )
                        ->where('orders.id',$order_id)
                        ->first();
        // dd($rec->order_id)     ;  
        if( (isset($rec->order_id)) && (isset($rec->status_id))){
            if($rec->status_id == 1){
                // echo "call: csr_cancel_pickup";
                // dd($rec->status_id);
                $this->csr_cancel_pickup($order_id);
                
            }elseif($rec->status_id == 2){
                // echo "call: csr_cancel_drop";
                // dd($rec->status_id);
                $this->csr_cancel_drop($order_id);

            }elseif($rec->status_id == 3){
                // echo "call: csr_cancel_pick_n_drop";
                $this->csr_cancel_pick_n_drop($order_id);

            }else{
                return false;
                // return "sorry! record not found.";
            }
        }else{
            return false;
            // return "sorry! record not found.";
        }
    }

    // Customer Cancellation by CSR (Dropoff) - (Trigger when CSR cancels an order on dashboard)
    public function csr_cancel_drop($order_id){
        $msg_id             = 11; // 11: cancel message notification
        $data               = ($this->get_order_detail($order_id,$msg_id));
        if(isset($data)){
            $dt             = date_create($data->delivery_date);
            $contact_no     = $data->contact_no;
            $sender         = "WASHUP";
            $msg            = "Hi ". ucwords($data->name). ", your laundry dropoff for ".(date_format( $dt ,"d F, Y"))." between ".($data->delivery_timeslot)." has been cancelled\r\nCall/WhatsApp: 03175286379";
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );

            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            return "sorry! record not found.";
        }
        
    }

    // Customer Cancellation by CSR (Pickup & Dropoff) - (Trigger when CSR cancels an order on dashboard)
    public function csr_cancel_pick_n_drop($order_id){
        $msg_id             = 11; // 11: cancel message notification
        $data               = ($this->get_order_detail($order_id,$msg_id));
        if(isset($data)){
            $dt             = date_create($data->delivery_date);
            $contact_no     = $data->contact_no;
            $sender         = "WASHUP";
            $msg            = "Hi ". ucwords($data->name). ", your laundry pickup & delivery for ".(date_format( $dt ,"d F, Y"))." between ".($data->delivery_timeslot)." has been cancelled\r\nCall/WhatsApp: 03175286379";
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );

            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            return "sorry! record not found.";
        }
        
    }

    // Rider Cancellation (Pickup) – (Trigger when Rider cancels pickup with No show option):
    public function rider_cancel_pick($order_id){
        $msg_id             = 11; // 11: cancel message notification
        $data               = ($this->get_order_detail($order_id,$msg_id));
        if(isset($data)){
            $contact_no     = $data->contact_no;
            $sender         = "WASHUP";
            $msg            = "Hi ". ucwords($data->name). ", our rider mentioned he came to your doorstep but didn’t receive the laundry as no one was available to give the laundry\r\nCall/WhatsApp: 03175286379";
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );

            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            return "sorry! record not found.";
        }
    }

    // Rider Cancellation (Pickup) – (Trigger when Rider cancels pickup with too expensive option):
    public function rider_cancel_pickup($order_id){
        $msg_id             = 11; // 11: cancel message notification
        $data               = ($this->get_order_detail($order_id,$msg_id));
        if(isset($data)){
            $contact_no     = $data->contact_no;
            $sender         = "WASHUP";
            $msg            = "Hi ". ucwords($data->name). ", our rider mentioned he came to your doorstep but didn’t receive the laundry as you weren’t satisfied with the charges\r\nCall/WhatsApp: 03175286379";
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );

            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            return "sorry! record not found.";
        }
    }

     // Rider Cancellation (Pickup) – (Trigger when Rider cancels pickup with other option):
     public function rider_cancel_other($order_id){
        $msg_id             = 11; // 11: cancel message notification
        $data               = ($this->get_order_detail($order_id,$msg_id));
        if(isset($data)){
            $contact_no     = $data->contact_no;
            $sender         = "WASHUP";
            $msg            = "Hi ". ucwords($data->name). ", your order has been cancelled \r\nCall/WhatsApp: 03175286379";
            $dataArray      = array(
                "receiver"  => $contact_no,
                "msgdata"   => $msg,
                "sender"    => $sender
            );

            $rec =  ($this->send_msg($dataArray));
            return $rec;
        }else{
            return "sorry! record not found.";
        }
    }

    // HFQ SMS – (Trigger when HFQ has been created for an order by the packer on dashboard)
    public function fn_hfq($order_id){
        // because, in new SMS doc, this sms was removed.
        return true;
        $msg_id             = 12; // 12: HFQ message notification
        $items              = "";
        $data               = ($this->get_order_detail($order_id,$msg_id));
        $rec                = DB::table('order_has_items')
                                ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                ->select(
                                            'items.id',
                                            'items.short_name',
                                            'order_has_items.hfq_qty',
                                        )
                                ->where('order_has_items.order_id',$order_id)
                                ->where('order_has_items.hfq_qty','!=',0)
                                ->get();

        if(!($rec->isEmpty())){
            foreach ($rec as $key => $value) {
                $items = $items.($value->hfq_qty)." ".($value->short_name).", ";
            }
            if(isset($data)){
                $contact_no     = $data->contact_no;
                $sender         = "WASHUP";
                $msg            = "Hi ". ucwords($data->name). ",our quality department has held back ".$items." from your delivery. They will be delivered shortly after your regular delivery (Name, Items & Qty)\r\nFor any further info Call/WhatsApp: 03175286379";
                $dataArray      = array(
                    "receiver"  => $contact_no,
                    "msgdata"   => $msg,
                    "sender"    => $sender
                );
    
                $rec =  ($this->send_msg($dataArray));
                return $rec;
            }else{
                return "sorry! record not found.";
            }
        }else{
            return "sorry! record not found.";
        }
    }

    
}
