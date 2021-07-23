<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCameraToAppProfileSeatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_profile_seats', function (Blueprint $table) {
            $table->dropUnique(['vehicle_id']);

            $table->string('camera')->default('all');

            $table->unique(['vehicle_id', 'camera']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_profile_seats', function (Blueprint $table) {
            $table->dropUnique(['vehicle_id', 'camera']);
            $table->unique(['vehicle_id']);

            $table->dropColumn('camera');
        });
    }
}
