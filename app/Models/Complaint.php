<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'complaint_nature_id',
        'complaint_tag_id',
        'status_id',
        'image',
        'complaint_detail',
        'created_by',
        'updated_by',
    ];
}
