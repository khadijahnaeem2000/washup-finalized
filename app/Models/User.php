<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable  implements MustVerifyEmail
{
    // ,HasApiTokens
    use HasFactory, Notifiable;
      use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'contact_no',
        'description',
        'image',
    ];

   
    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


}
