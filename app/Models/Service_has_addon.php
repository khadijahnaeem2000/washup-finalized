<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Service_has_addon extends Model
{
    protected $table = 'service_has_addons';
    protected $primaryKey = 'id';

    protected $fillable = [
        'service_id',
        'item_id',
        'item_addon_id'
    ];

    public function addon()
    {
        return $this->belongsTo('App\Models\Addon', 'addon_id', 'id');
    }
}
