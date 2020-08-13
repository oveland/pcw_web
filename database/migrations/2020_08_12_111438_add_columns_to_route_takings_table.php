<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToRouteTakingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tarifas_rutas', function (Blueprint $table) {
            $table->double('fuel_tariff')->default(0);
        });

        Schema::table('route_takings', function (Blueprint $table) {
            $table->double('passenger_tariff')->default(0);
            $table->integer('bonus')->nullable();
            $table->double('fuel_tariff')->default(0);
            $table->double('fuel_gallons')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
        });
//        $tariffValue = collect(DB::select(""))->first();
//        DB::statement("UPDATE route_takings SET passenger_tariff = (SELECT tarifa FROM tarifas_rutas WHERE id_ruta = (SELECT route_id FROM dispatch_registers WHERE id = dispatch_register_id) LIMIT 1 ) WHERE TRUE");

        DB::statement("alter table route_takings alter column fuel type double precision using fuel::double precision");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('route_takings', function (Blueprint $table) {
//            $table->dropColumn('user_id');
            $table->dropColumn('fuel_gallons');
            $table->dropColumn('fuel_tariff');
            $table->dropColumn('bonus');
            $table->dropColumn('passenger_tariff');
        });

        Schema::table('tarifas_rutas', function (Blueprint $table) {
            $table->dropColumn('fuel_tariff');
        });
    }
}
