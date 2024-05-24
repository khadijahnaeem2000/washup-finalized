<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OauthAccessToken extends Model
{
    //
    protected $primaryKey = 'id';
    protected $table = 'oauth_access_tokens';
    
}
