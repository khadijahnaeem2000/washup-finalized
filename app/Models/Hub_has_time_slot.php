<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Hub_has_time_slot extends Model
{
    protected $fillable = [
        'hub_id',
        'time_slot_id',
        'location',
    ];
}
