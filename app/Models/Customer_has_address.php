<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Customer_has_address extends Model
{
    protected $fillable = [
       'customer_id',
       'address',
       'latitude',
       'longitude',
       'status',
    ];
}
