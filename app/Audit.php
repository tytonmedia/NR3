<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $fillable = [
        'user_id', 
        'payment_id',
        'site_url',
        'page_h1_greater',
        'page_h1_less', 
        'long_title', 
        'short_title',
        'url_length',
        'graph_data',
        'links_more_h1',
        'less_code_ratio',
        'short_meta_description',
        'long_meta_description',
        'robot',
        'less_page_words',
        'links_empty_h1',
        'duplicate_h1',
        'page_miss_meta',
        'duplicate_meta_description',
        'page_incomplete_card',
        'page_incomplete_graph',
        'status301',
        'status302',
        'status404',
        'status500',
        'page_miss_title',
        'duplicate_title','twitter',
        'link_302',
        'link_301',
        'link_404',
        'link_500',
        'page_without_canonical',
        'notices',
        'warning',
        'errors',
        'passed_pages',
        'health_score',
        'pages',
        'audit_description'
  
    ];

}
