<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    protected $fillable = [
        'id',
        'name',
        'amount',
        'start_date',
    ];

    //relationshops

}
