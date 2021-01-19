<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeywordResults extends Model
{
    protected $fillable = [
        'user_id', 
        'payment_id',
        'site_url',
        'updated_at',
        'created_at'
    ];

}
