<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Customer_has_item extends Model
{
    protected $fillable = [
       'customer_id',
       'service_id',
       'item_id',
       'item_rate',
       'status',
    ];
}
