<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToVehicleBinnacleNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_binnacle_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('mileage')->nullable()->default(0);
            $table->unsignedBigInteger('mileage_expiration')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_binnacle_notifications', function (Blueprint $table) {
            $table->dropColumn('mileage');
            $table->dropColumn('mileage_expiration');
        });
    }
}
