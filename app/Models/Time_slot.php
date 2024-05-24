<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time_slot extends Model
{
    protected $fillable = [
        'name',
        'color',
        'start_time',
        'end_time',
        'created_by',
        'updated_by',
        
    ];
}
