<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Hub_has_user extends Model
{
    protected $fillable = [
        'hub_id',
        'user_id',
    ];
}
