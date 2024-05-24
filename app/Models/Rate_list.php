<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate_list extends Model
{
    protected $fillable = [
        'service_id',
        'item_id',
        'wash_house_id',
        'rate',
        'created_by',
        'updated_by',
        
    ];
}
