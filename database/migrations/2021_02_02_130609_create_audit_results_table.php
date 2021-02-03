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
            $table->longText('short_title')->nullable();
            $table->string('url_length')->nullable();
            $table->string('less_page_words ')->nullable();
            $table->string('graph_data ')->nullable();
            $table->string('links_more_h1')->nullable();
            $table->string('links_empty_h1')->nullable();
            $table->string('duplicate_h1')->nullable();
            $table->json('less_code_ratio')->nullable();
            $table->string('short_meta_description')->nullable();
            $table->json('long_meta_description')->nullable();
            $table->string('robot')->nullable();
            $table->json('page_miss_meta')->nullable();
            $table->string('duplicate_meta_description')->nullable();
            $table->json('page_incomplete_card')->nullable();
            $table->string('page_incomplete_graph')->nullable();
            $table->json('status_301')->nullable();
            $table->unsignedInteger('status_302')->nullable();
            $table->json('status_404')->nullable();
            $table->unsignedInteger('status_500')->nullable();
            $table->string('page_miss_title ')->nullable();
            $table->string('duplicate_title')->nullable();
            $table->string('twitter')->nullable();
            $table->string('link_302')->nullable();
            $table->json('link_301')->nullable();
            $table->string('link_404')->nullable();
            $table->string('link_500')->nullable();
            $table->json('page_without_canonical')->nullable();
            $table->string('notices')->nullable();
            $table->string('warning')->nullable();
            $table->string('errors')->nullable();
            $table->string('passed_pages')->nullable();
            $table->string('health_score')->nullable();
            $table->longText('pages')->nullable();
            $table->json('audit_description')->nullable(); 
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
