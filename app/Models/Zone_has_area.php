<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Zone_has_area extends Model
{
    protected $table = "zone_has_areas";

    protected $primaryKey = 'id';

    protected $fillable = [
        'zone_id',
        'area_id',
        'created_by',
        'updated_by',
    ];

    public function areas()
    {
        return $this->hasMany('App\Models\Area', 'id', 'zone_id');
    }

}
