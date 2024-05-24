<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Service_has_item extends Model
{
    protected $fillable = [
        'service_id',
        'item_id',
        'item_rate',
        'item_addon',
        'item_status'
    ];
}
