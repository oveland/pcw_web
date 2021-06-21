<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMileageCompletedToVehicleBinnaclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_binnacles', function (Blueprint $table) {
            $table->unsignedBigInteger('mileage_completed')->nullable()->default(0);
            $table->unsignedBigInteger('mileage_odometer_completed')->nullable()->default(0);
            $table->unsignedBigInteger('mileage_route_completed')->nullable()->default(0);

            $table->renameColumn('"mileageOdometer"', 'mileage_odometer');
            $table->renameColumn('"mileageRoute"', 'mileage_route');
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
            $table->renameColumn('mileage_route', '"mileageRoute"');
            $table->renameColumn('mileage_odometer', '"mileageOdometer"');

            $table->dropColumn('mileage_route_completed');
            $table->dropColumn('mileage_odometer_completed');
            $table->dropColumn('mileage_completed');
        });
    }
}
