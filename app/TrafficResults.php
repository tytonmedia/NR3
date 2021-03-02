<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrafficResults extends Model
{
    protected $fillable = [
        'user_id', 
        'payment_id',
        'traffic_id',
        'domain',
        'traffic',
        'updated_at',
        'created_at'
    ];

}
