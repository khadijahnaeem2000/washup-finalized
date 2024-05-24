<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment_ride_history extends Model
{
    protected $fillable = [
        'payment_ride_id',
        'status_id',
        'created_by',
    ];
}
