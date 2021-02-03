<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhiteLabel extends Model
{
    
    protected $fillable = [
        'user_id', 'payment_id','image_path'
    ];
    protected $table = 'white_label';
}
