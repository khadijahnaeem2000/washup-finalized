<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wash_house_has_addon extends Model
{
    protected $fillable = [
        'wash_house_id',
        'addon_id',
    ];
}
