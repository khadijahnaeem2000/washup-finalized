<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle_type extends Model
{
    protected $fillable = [
        'name', 
        'hanger',
        'created_by',
        'updated_by',
    ];
}
