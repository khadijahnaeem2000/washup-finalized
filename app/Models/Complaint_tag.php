<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint_tag extends Model
{
    protected $fillable = [
        'name',
        'complaint_nature_id'
    ];
}
