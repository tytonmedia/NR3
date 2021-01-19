<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerpFeatures extends Model
{
    protected $fillable = [
        'id', 
        'name',
        'link_to_domain',
        'description'
    ];

}
