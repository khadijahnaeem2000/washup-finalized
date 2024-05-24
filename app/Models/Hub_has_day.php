<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Hub_has_day extends Model
{
    protected $fillable = [
        'hub_id',
        'day_id',
    ];
}
