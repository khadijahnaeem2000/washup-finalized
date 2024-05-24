<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rider_incentives extends Model
{
    use HasFactory;
     protected $fillable = [
           'name',
           'pickup_rate',
           'pickdrop_rate',
           'kilometer',
           'status',
    ];
}
