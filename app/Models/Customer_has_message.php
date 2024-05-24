<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Customer_has_message extends Model
{
    protected $fillable = [
       'customer_id',
       'message_id',
       'status',
    ];
}
