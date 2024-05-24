<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution_hub extends Model
{
    protected $fillable = [
        'name',
        'address',
        'lat',
        'lng',
        'cus_address',
        'created_by',
        'updated_by',
        
    ];
}
