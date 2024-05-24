<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon_rate_list extends Model
{
    protected $fillable = [
        'addon_id',
        'wash_house_id',
        'rate',
        'created_by',
        'updated_by',
        
    ];
}
