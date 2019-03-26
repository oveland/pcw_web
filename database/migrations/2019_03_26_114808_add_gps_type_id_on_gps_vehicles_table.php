<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGpsTypeIdOnGpsVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gps_vehicles', function (Blueprint $table) {
            $table->integer('gps_type_id')->nullable();

            /* table relations */
            $table->foreign('gps_type_id')->references('id')->on('gps_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gps_vehicles', function (Blueprint $table) {
            $table->dropForeign('gps_vehicles_gps_type_id_foreign');
            $table->dropColumn('gps_type_id');
        });
    }
}
