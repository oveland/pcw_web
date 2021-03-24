<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyVehicleBinnaclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_binnacles', function (Blueprint $table) {
            $table->integer('mileage')->nullable()->default(0);
            $table->integer('mileageOdometer')->nullable()->default(0);
            $table->integer('mileageRoute')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_binnacles', function (Blueprint $table) {
            $table->dropColumn('mileageRoute');
            $table->dropColumn('mileageOdometer');
            $table->dropColumn('mileage');
        });
    }
}
