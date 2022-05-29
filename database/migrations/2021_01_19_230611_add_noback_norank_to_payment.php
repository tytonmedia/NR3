<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNobackNorankToPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            //
            $table->string('no_allowed_backlinks')->nullable()->after('no_allowed_audits');
        $table->string('no_allowed_rankings')->nullable()->after('no_allowed_backlinks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            //
            $table->dropColumn(['no_allowed_backlinks', 'no_allowed_rankings']);
        });
    }
}
