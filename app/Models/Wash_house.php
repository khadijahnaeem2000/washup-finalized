<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wash_house extends Model
{
    protected $fillable = [
        'name',
        'capacity',
        'created_by',
        'updated_by',
        
    ];
}
