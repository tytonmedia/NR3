<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Traffic extends Model
{
    protected $fillable = [
    'user_id', 
    'payment_id',
    'domain',
    'global_rank',
    'cat_rank',
    'cat',
    'visits',
    'avg_time_site',
    'avg_page_views ',
    'bounce_rate',
    'traffic_value',
    'direct_value   ',
    'direct_percent ',
    'search_value   ',
    'search_percent ',
    'referring_value',
    'referring_percent',
    'social_value',
    'social_percent',
    'countries',
    'estimated',
    'similar',
    'keywords',
    'destination',
    'average_growth'
    ];

}
