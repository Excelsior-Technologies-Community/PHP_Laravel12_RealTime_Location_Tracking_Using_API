<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'user_name',
        'latitude',
        'longitude',
        'tracked_at',
    ];

    protected $casts = [
        'tracked_at' => 'datetime',
    ];
}