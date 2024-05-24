<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_has_bag extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'tag_scanned',
    ];
}
