<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{

    protected $table = "zones";
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];


    public function zone_has_areas()
    {
        return $this->hasMany('App\Models\Zone_has_area');
    }



}
