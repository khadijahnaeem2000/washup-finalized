<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route_plan extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
        'order_id',  
        'area_id',
        'zone_id',
        'hub_id', 
        'hanger',
        'weight',
        'travel_time',
        'time_at_loc',
        'req_dist',
        'cov_dist',
        'status_id',
        'timeslot_id',
        'address_id',
        'rider_id',
        'route',
        'seq',
        'schedule',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
