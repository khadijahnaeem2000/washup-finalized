<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment_ride extends Model
{
    protected $fillable = [
        'customer_id', 
        'address_id',
        'bill',
        'ride_date',
        'timeslot_id',
        'time_at_loc',
        'rider_id',
        'status_id',
        'created_by',
        'updated_by',
    ];
}
