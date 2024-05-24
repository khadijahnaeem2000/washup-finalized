<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Rider extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'name', 
        'username',
        'password',
        'cnic_no',
        'contact_no',
        'max_loc',
        'max_route',
        'max_pick',
        'max_drop_weight',
        'max_drop_size',
        'color_code',
        'vehicle_type_id',
        'vehicle_reg_no',
        'address',
        'forgot',
        'image',
        'status',
        'created_by',
        'updated_by',
        'rider_incentives'
    ];
    
}



