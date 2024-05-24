<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wash_house_has_user extends Model
{
    protected $fillable = [
        'wash_house_id',
        'user_id',
    ];
}
