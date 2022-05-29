<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Backlink extends Model
{
    protected $fillable = [
        'user_id', 
        'payment_id',
        'source_url',
        'target_url', 
        'anchor', 
        'page_ascore', 
        'first_seen',
        'last_seen',
        'updated_at',
        'created_at',
        'external_num',
        'internal_num',
        'nofollow',
    ];

}
