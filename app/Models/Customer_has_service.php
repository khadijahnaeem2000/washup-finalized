<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Customer_has_service extends Model
{
    protected $fillable = [
       'customer_id',
       'service_id',
       'order_number',
       'service_rate',
       'status',
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->order_number = $model->service_id;
        });
    }
}
