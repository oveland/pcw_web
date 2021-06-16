<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPrevDateToVehicleBinnaclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_binnacles', function (Blueprint $table) {
            $table->timestamp('prev_date')->nullable();
            $table->boolean('completed')->default(false);
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
            $table->dropColumn('completed');
            $table->dropColumn('prev_date');
        });
    }
}
