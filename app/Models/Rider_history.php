<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Rider_history extends Model
{
    protected $fillable = [
        'rider_id',
        'plan_date',
        'start_reading',
        'end_reading',
        'start_img',
        'end_img',
        'old_start_reading',
        'old_end_reading'
    ];

}
