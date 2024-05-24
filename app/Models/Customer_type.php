<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer_type extends Model
{
    protected $fillable = [
        'name', 
        'description',
        'created_by',
        'updated_by',
    ];
}
