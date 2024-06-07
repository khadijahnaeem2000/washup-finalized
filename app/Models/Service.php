<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'id',
        'name',
        'hanger',
        'qty',
        'unit_id',
        'rate',
        'description',
        'image',
        'web_image',
        'status',
        'created_by',
        'updated_by',
        'orderNumber',
    ];

    public function unit()
    {
        return $this->hasOne('App\Models\Unit');
    }
        protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->orderNumber = $model->id;
            $model->save();
        });
    }
    
}
