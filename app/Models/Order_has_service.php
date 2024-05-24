<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_has_service extends Model
{
    // protected $table = 'order_has_services';
    // protected $primaryKey = 'id';
    protected $fillable = [
        'order_id', 
        'service_id',
        'weight',
        'order_number',
        'qty',
        'cus_service_rate',
        'wh_service_rate'
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->order_number = $model->service_id;
        });
    }
}
