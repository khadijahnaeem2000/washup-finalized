<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Hub_has_rider extends Model
{
    protected $fillable = [
        'hub_id',
        'rider_id',
    ];
}
