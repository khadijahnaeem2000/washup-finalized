<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
       'name',
       'email',
       'contact_no',
       'email_alert',
       'alt_contact_no',
       'permanent_note',
       'customer_type_id',

       'created_by',
       'updated_by',
    ];
}
