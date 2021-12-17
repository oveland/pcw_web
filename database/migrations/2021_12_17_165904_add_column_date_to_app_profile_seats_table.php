<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDateToAppProfileSeatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_profile_seats', function (Blueprint $table) {
            $table->date('date')->nullable();
            $table->dropUnique(['vehicle_id', 'camera']);

            $table->unique(['date', 'vehicle_id', 'camera']);
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
            $table->unique(['vehicle_id', 'camera']);
            $table->dropColumn('date');
        });
    }
}
