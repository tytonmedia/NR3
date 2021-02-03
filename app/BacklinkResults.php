<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BacklinkResults extends Model
{
    protected $fillable = [
        'user_id', 
        'payment_id',
        'site_url',
        'domains_num',
        'backlinks_num',
        'historical',
        'updated_at',
        'created_at'
    ];

}
