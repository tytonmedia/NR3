<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditResults extends Model
{
    protected $fillable = [
        'user_id', 'site_url','payment_id','audit_id'
    ];
}
