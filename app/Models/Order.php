<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_no', 
        'customer_id',
        'order_note',
        'rider_note',
        'area_id',
        'hub_id',

        'pickup_date',
        'pickup_address_id',
        'pickup_address',
        'pickup_timeslot_id',
        'pickup_timeslot',
        'delivery_date',

        'delivery_address_id',
        'delivery_address',
        'delivery_timeslot_id',
        'delivery_timeslot',


        'tags_printed',
        'iron_rating',
        'softner_rating',
        'packed_weight',
        'polybags_printed',

        'vat_charges',
        'delivery_charges',
        
        'status_id2',
        'status_id',
        'created_by',
        'updated_by',
        'phase',
        'DW_when',
        'DW_who'
    ];
     protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Set default value for waver_delivery if not already set
            if (is_null($order->waver_delivery)) {
                $order->waver_delivery = 0;
            }
        });
    }
}
