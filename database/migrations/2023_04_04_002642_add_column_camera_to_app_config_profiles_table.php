<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCameraToAppConfigProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::delete("DELETE FROM app_config_profiles WHERE TRUE");

        Schema::table('app_config_profiles', function (Blueprint $table) {
            $table->date('date');
            $table->string('camera', 32);

            $table->unique(['date', 'camera', 'vehicle_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_config_profiles', function (Blueprint $table) {
            $table->dropUnique(['date', 'camera', 'vehicle_id']);
            $table->dropColumn('camera');
            $table->dropColumn('date');
        });
    }
}
