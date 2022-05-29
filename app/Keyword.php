<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = [
        'user_id', 
        'payment_id',
        'site_url',
        'keyword', 
        'position',
        'kd', 
        'volume', 
        'cpc',
        'competition',
        'traffic_per',
        'traffic_cost',
        'results',
        'trends',
        'features',
    ];

}
