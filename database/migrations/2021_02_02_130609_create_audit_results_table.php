<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('payment_id')->nullable();
            $table->string('site_url');
             $table->json('long_title')->nullable();
            $table->json('short_title')->nullable();
            $table->json('url_length')->nullable();
            $table->json('less_page_words')->nullable();
            $table->json('graph_data')->nullable();
            $table->json('links_more_h1')->nullable();
            $table->json('links_empty_h1')->nullable();
            $table->json('duplicate_h1')->nullable();
            $table->json('less_code_ratio')->nullable();
            $table->json('short_meta_description')->nullable();
            $table->json('long_meta_description')->nullable();
            $table->json('robot')->nullable();
            $table->json('page_miss_meta')->nullable();
            $table->json('duplicate_meta_description')->nullable();
            $table->json('page_incomplete_card')->nullable();
            $table->json('page_incomplete_graph')->nullable();
            $table->json('status_301')->nullable();
            $table->json('status_302')->nullable();
            $table->json('status_404')->nullable();
            $table->json('status_500')->nullable();
            $table->json('page_miss_title')->nullable();
            $table->json('duplicate_title')->nullable();
            $table->json('twitter')->nullable();
            $table->json('link_302')->nullable();
            $table->json('link_301')->nullable();
            $table->json('link_404')->nullable();
            $table->json('link_500')->nullable();
            $table->json('page_without_canonical')->nullable();
            $table->bigInteger('notices')->nullable();
            $table->bigInteger('warning')->nullable();
            $table->bigInteger('errors')->nullable();
            $table->bigInteger('passed_pages')->nullable();
            $table->bigInteger('health_score')->nullable();
            $table->bigInteger('pages')->nullable();
            $table->string('audit_description')->nullable(); 
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
        Schema::dropIfExists('audits');
    }
}
