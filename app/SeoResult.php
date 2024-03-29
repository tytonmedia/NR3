<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeoResult extends Model
{
    protected $fillable = [
        'url',
            'title',
            'title_length',
            'meta',
            'meta_length',
            'img_alt',
            'img_miss_alt',
            'iframe',
            'all_img_src',
            'canonical',
            'time',
            'img_without_alt',
            'url_seo_friendly',
            'h1',
            'h1_tags',
            'h2',
            'h2_tags',
            'h3',
            'h3_tags',
            'word_count',
            'numWords',
            'external_links',
            'page_words',
            'page_size',
            'page_text_ratio',
            'page_words_size',
            'http',
            'cache',
            'page_https',
            'status404',
            'internal_link',
            'a_https',
            'link_https',
            'script_https',
            'social_media_link',
            'robot',
            'sitemap',
            'schema_data',
            'social_schema',
            'passed_score',
            'warning_score',
            'error_score',
            'img_data',
            'favicon',
            'mobile_friendly',
            'ssl_certificate',
            'notice_score',
            'image',
            'score_description',
            'word',
            'domains_num',
            'urls_num',
            'keyword_list',
            'schema_types',
            'semrush_links',
             'performance_score',
             'loadtime',
              'fcp ',
              'lcp ',
              'cls',
              'responsive_images',
              'css_min',
              'css_min_bytes',
              'js_min',
             'js_min_score',
             'js_min_bytes',
               'gzip_compression'
    ];

}
