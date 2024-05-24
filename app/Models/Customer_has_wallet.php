<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Customer_has_wallet extends Model
{
    protected $fillable = [
       'customer_id',
       'in_amount',
       'out_amount',
       'order_id',
       'ride_id',
       'wallet_reason_id',
       'detail',
       'rider_id',
    ];
}
