<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrafficTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traffic', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id'); 
            $table->unsignedInteger('payment_id');
            $table->string('domain', 255);
            $table->longText('description')->nullable();
            $table->bigInteger('global_rank')->nullable();
            $table->bigInteger('country_rank')->nullable();
            $table->bigInteger('cat_rank')->nullable();
            $table->string('cat', 255)->nullable();
            $table->bigInteger('visits')->nullable();
            $table->string('avg_time_site')->nullable();
            $table->float('avg_page_views')->nullable();
            $table->float('bounce_rate')->nullable();
            $table->bigInteger('traffic_value')->nullable();
            $table->bigInteger('direct_value')->nullable();
            $table->float('direct_percent')->nullable();
            $table->bigInteger('organic_value')->nullable();
            $table->float('organic_percent')->nullable();
            $table->bigInteger('search_value')->nullable();
            $table->float('search_percent')->nullable();
            $table->bigInteger('referring_value')->nullable();
            $table->float('referring_percent')->nullable();
            $table->bigInteger('social_value')->nullable();
            $table->float('social_percent')->nullable();
            $table->json('countries')->nullable();
            $table->json('estimated')->nullable();
            $table->json('similar')->nullable();
            $table->json('keywords')->nullable();
            $table->json('destinations')->nullable();
            $table->json('ad_keywords')->nullable();
            $table->json('top_socials')->nullable();
            $table->float('average_growth')->nullable();
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
        Schema::dropIfExists('traffic');
    }
}
