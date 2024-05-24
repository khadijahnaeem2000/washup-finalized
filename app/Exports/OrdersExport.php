<?php

namespace App\Exports;

use DB;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;
class OrdersExport implements FromCollection,  WithHeadings
{
 
    public $from_date;
    public $to_date;

    public function __construct($fd, $td)
    {
        $this->from_date = $fd;
        $this->to_date = $td;
    }

    public function collection()
    {
        $data   = DB::table('orders')
                    ->leftjoin('customers', 'customers.id', '=', 'orders.customer_id')
                    ->leftjoin('wash_house_has_orders', 'wash_house_has_orders.order_id', '=', 'orders.id')
                    ->leftjoin('wash_houses', 'wash_houses.id', '=', 'wash_house_has_orders.wash_house_id')
                    ->select(
                                'orders.id as order_id',
                                'orders.pickup_date',
                                'customers.name',
                                'customers.contact_no',
                                'wash_houses.name as washhouse_name',
                                DB::raw(
                                            '(CASE 
                                                WHEN ISNULL(orders.vat_charges) THEN "0"
                                                ELSE orders.vat_charges
                                                END
                                            ) AS vat_charges'
                                ),
                                DB::raw(
                                    '(CASE 
                                        WHEN ISNULL(orders.delivery_charges) THEN "0"
                                        ELSE orders.delivery_charges
                                        END
                                    ) AS delivery_charges'
                                ),
                                // 'orders.vat_charges',
                                // 'orders.delivery_charges',
                            )
                    ->whereDate('orders.pickup_date','>=', ($this->from_date))  
                    ->whereDate('orders.pickup_date','<=', ($this->to_date)) 
                    ->get();

        foreach ($data as $key => $value) {
            $rec = $this->calc_invoice($value->order_id);
            // dd($rec);
            if((isset($rec['service_total']))  &&  ($rec['service_total'] > 0) ){
                $data[$key]->service_total = $rec['service_total'];
            }else{
                $data[$key]->service_total = "0";
            }


            if((isset($rec['addon_total']))  &&  ($rec['addon_total'] > 0) ){
                $data[$key]->addon_total = $rec['addon_total'];
            }else{
                $data[$key]->addon_total = "0";
            }

            if((isset($rec['service_total']))  &&  ($rec['service_total'] > 0) ){
                $data[$key]->invoice         = ($data[$key]->service_total) + ($data[$key]->addon_total) + ($value->vat_charges) + ($value->delivery_charges);
            }else{
                $data[$key]->invoice         = 0;
            }

           

            
            
        }

      
       
        return  $data;
    }

    public function headings(): array
    {
        return [
            'Order#',
            'Pickup Date',
            'Customer name',
            'Customer No#',
            'washhouse_name',
            'VAT Charges ',
            'Delivery Charges',
            'Service Amount',
            'Addon Amount',
            'Invoice',
            
        ];
    }

    public function calc_invoice($order_id) {



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
                                    ->get()
                                    ->all(); 


        
        foreach ($selected_services as $service_key => $service_value) {

            if($service_value->unit_id == 2){

                // unit id : 2 means item wise rate
                $selected_items         = DB::table('order_has_items')
                                            ->leftjoin('items', 'items.id', '=', 'order_has_items.item_id')
                                            ->leftjoin('services', 'services.id', '=', 'order_has_items.service_id')
                                            ->where('order_has_items.order_id', $order_id)
                                            ->where('order_has_items.service_id', $service_value->service_id)
                                            ->select(
                                                        'items.id as item_id',
                                                        'order_has_items.service_id as service_id',
                                                        'order_has_items.pickup_qty as pickup_qty',
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


         $mega_qty_tot = 0; $item_count = 0;
         $mega_ser_tot = 0;
         $mega_add_tot = 0; 

        foreach($selected_services as $key =>$value){

            $ser_tot =0;  $qty_tot = 0; $mega_qty_tot += ($value->weight); $add_tot = 0; $add_total = 0;
            foreach($value->items as $item_key => $item_value){

                $qty_tot += ($item_value->pickup_qty); $item_count +=($item_value->pickup_qty);  $add_total = 0;
                foreach($item_value->addons as $addon_key => $addon_value){
                    $add_total += (($addon_value->addon_rate) * ($item_value->pickup_qty));
                }

                $add_tot +=  $add_total;
            
                if($value->unit_id ==2 ){
                    $ser_tot += (($item_value->item_rate) * ($item_value->pickup_qty));  
                }elseif($value->unit_id ==3){
                    $ser_tot += (($item_value->service_rate) * ($item_value->pickup_qty));
                }else{
                    $ser_tot =$item_value->service_rate; 
                }
            }
            if($value->unit_id ==1 ){
                $tot = ($ser_tot * ($value->weight));
            }else{
                $tot = $ser_tot;
            }

            $mega_add_tot += $add_tot;

            $mega_ser_tot +=($tot) ;
        }

        $data = array();
        $data['service_total']  = $mega_ser_tot;
        $data['addon_total']    = $mega_add_tot;


        return $data;
    }

}
