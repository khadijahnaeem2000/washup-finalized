<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wash_house_has_zone extends Model
{
    protected $fillable = [
        'wash_house_id',
        'zone_id',
        'created_by',
        'updated_by',
        
    ];
}
