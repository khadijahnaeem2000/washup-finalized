<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Delivery_charge extends Model
{
    protected $fillable = [
        'order_amount',
        'delivery_charges',
    ];

}
