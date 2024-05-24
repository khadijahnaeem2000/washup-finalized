<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Customer_has_retainer_day extends Model
{
    protected $fillable = [
       'customer_id',
       'day_id',
       'time_slot_id',
       'note',
    ];
}
