<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wash_house_has_hub extends Model
{
    protected $fillable = [
        'wash_house_id',
        'hub_id',
        'created_by',
        'updated_by',
        
    ];
}
