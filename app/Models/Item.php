<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Item extends Model
{

    protected $table = 'items';
    protected $primaryKey = 'id';
    

    protected $fillable = [
       'name',
       'short_name',
       'description',
       'image',
       'created_by',
       'updated_by',
    ];


    public function addon()
    {
        return $this->belongsTo('App\Models\Addon');
    }
}
