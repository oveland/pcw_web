<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignToRouteTakingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('route_takings', function (Blueprint $table) {
            $table->renameColumn('station_fuel_id', 'fuel_station_id');
            $table->foreign('fuel_station_id')->references('id')->on('fuel_stations');
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
            $table->dropForeign('fuel_station_id');

            $table->renameColumn('fuel_station_id', 'station_fuel_id');
        });
    }
}
