<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTariffColumnToPassengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('passengers', function (Blueprint $table) {
            $table->integer('counted')->default(0);
            $table->integer('tariff')->default(0);
            $table->integer('charge')->default(0);
            $table->integer('total_charge')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('passengers', function (Blueprint $table) {
            $table->dropColumn('total_charge');
            $table->dropColumn('charge');
            $table->dropColumn('tariff');
            $table->dropColumn('counted');
        });
    }
}
