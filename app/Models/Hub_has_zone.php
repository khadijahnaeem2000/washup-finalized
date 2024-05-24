<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Hub_has_zone extends Model
{
    protected $fillable = [
        'hub_id',
        'zone_id',
    ];
}
