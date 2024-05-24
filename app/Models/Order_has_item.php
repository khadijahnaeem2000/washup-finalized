<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_has_item extends Model
{
    protected $fillable = [
        'order_id', 
        'service_id',
        'item_id',
        'pickup_qty',
        'scan_qty',
        'bt_qty',
        'nr_qty',
        'hfq_qty',
        'note',
        'reason',
        'reason_id',
        'cus_item_rate',
        'wh_item_rate'
    ];
}
