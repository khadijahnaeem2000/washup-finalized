<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_has_tag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 
        'service_id', 
        'item_id', 
        'ord_itm_id',
        'tag_scanned',
    ];
}
