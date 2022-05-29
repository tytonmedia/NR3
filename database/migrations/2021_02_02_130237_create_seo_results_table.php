<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeoResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('payment_id')->nullable();
            $table->string('url');
            $table->string('title');
            $table->string('title_length');
            $table->longText('meta')->nullable();
            $table->string('meta_length')->nullable();
            $table->string('img_alt')->nullable();
            $table->string('img_miss_alt')->nullable();
            $table->unsignedInteger('iframe')->nullable();
            $table->longText('all_img_src')->nullable();
            $table->string('canonical')->nullable();
            $table->json('img_without_alt')->nullable();
            $table->string('url_seo_friendly')->nullable();
            $table->json('h1')->nullable();
            $table->string('h1_tags')->nullable();
            $table->json('h2')->nullable();
            $table->string('h2_tags')->nullable();
            $table->json('h3')->nullable();
            $table->string('h3_tags')->nullable();
            $table->json('word_count')->nullable();
            $table->bigInteger('numWords')->nullable();
            $table->json('external_links')->nullable();
            $table->bigInteger('page_words')->nullable();
            $table->string('page_size')->nullable();
            $table->string('page_text_ratio')->nullable();
            $table->string('page_words_size')->nullable();
            $table->string('http')->nullable();
            $table->unsignedInteger('cache')->nullable();
            $table->string('page_https')->nullable();
            $table->string('status404')->nullable();
            $table->json('internal_link')->nullable();
            $table->string('a_https')->nullable();
            $table->string('link_https')->nullable();
            $table->string('script_https')->nullable();
            $table->string('social_media_link')->nullable();
            $table->string('robot')->nullable();
            $table->string('sitemap')->nullable();
            $table->longText('schema_data')->nullable();
            $table->json('social_schema')->nullable(); 
            $table->bigInteger('passed_score');
            $table->bigInteger('warning_score');
            $table->bigInteger('error_score');
            $table->json('img_data')->nullable();
            $table->string('favicon')->nullable();
            $table->string('mobile_friendly')->nullable();
            $table->string('ssl_certificate')->nullable();
            $table->bigInteger('notice_score');
            $table->binary('image')->nullable();
            $table->string('score_description')->nullable();
            $table->string('word')->nullable();
            $table->string('domains_num');
            $table->string('urls_num');
            $table->json('keyword_list')->nullable(); 
            $table->json('schema_types')->nullable();
            $table->json('semrush_links')->nullable();
            $table->string('performance_score')->nullable();
            $table->string('loadtime')->nullable();
            $table->string('fcp')->nullable();
            $table->string('lcp')->nullable();
            $table->string('cls')->nullable();
            $table->string('responsive_images')->nullable();
            $table->string('css_min')->nullable();
            $table->string('css_min_bytes')->nullable();
            $table->string('js_min')->nullable();
            $table->string('js_min_score')->nullable();
            $table->string('js_min_bytes')->nullable();
            $table->string('gzip_compression')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_results');
    }
}
