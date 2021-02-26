<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdvanceAndBalanceColumnsToRouteTakingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('route_takings', function (Blueprint $table) {
            $table->integer('advance')->default(0)->nullable();
            $table->integer('balance')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('route_takings', function (Blueprint $table) {
            $table->dropColumn('balance');
            $table->dropColumn('advance');
        });
    }
}
