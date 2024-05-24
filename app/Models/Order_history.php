<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order_history extends Model
{
    protected $fillable = [
        'type',
        'order_id', 
        'status_id',
        'detail',
        'created_by'
    ];
    
}
