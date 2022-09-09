<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsManualsToRouteTakingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('route_takings', function (Blueprint $table) {
            $table->integer('manual_total_production')->default(0);
            $table->integer('manual_total_passengers')->default(0);
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
            //
        });
    }
}
