<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rider_has_zone extends Model
{
    protected $fillable = [
        'rider_id',
        'zone_id',
        'priority',
    ];
}
