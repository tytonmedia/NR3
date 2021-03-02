<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrafficResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traffic_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id'); 
            $table->unsignedInteger('payment_id');
            $table->unsignedInteger('traffic_id');
            $table->string('domain', 255);
            $table->BigInteger('traffic');
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
        Schema::dropIfExists('traffic_results');
    }
}
