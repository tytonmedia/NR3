<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    protected $fillable = [
        'user_id', 
        'payment_id',
        'site_url',
        'domain', 
        'common_keywords', 
        'organic_keywords', 
        'organic_traffic',
        'cost',
        'adwords_keywords',
    ];

}
