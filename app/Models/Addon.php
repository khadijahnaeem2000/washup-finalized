<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{

    protected $table = 'addons';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'name',
        'rate',
        'created_by',
        'updated_by',
        
    ];
}
