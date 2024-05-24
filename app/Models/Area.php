<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'name', 
        'latitude',
        'longitude',
        'radius',
        'poly_points',
        'center_points',
        'created_by',
        'updated_by',
    ];


    // protected $attributes = [
    //     'latitude' =>0,
    //     'longitude' =>0,
    // ];

}
