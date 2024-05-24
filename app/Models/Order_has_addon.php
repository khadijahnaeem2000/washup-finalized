<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_has_addon extends Model
{
    protected $fillable = [
        'order_id', 
        'service_id',
        'item_id',
        'addon_id',
        'ord_itm_id',
        'cus_addon_rate',
        'wh_addon_rate'
    ];
}
