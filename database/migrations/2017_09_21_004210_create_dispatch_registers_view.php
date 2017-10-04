<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispatchRegistersView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE VIEW dispatch_registers AS SELECT rd.id_registro AS id,
                rd.fecha AS date,
                rd.hora AS \"time\",
                rd.id_ruta AS route_id,
                dt.id AS type_of_day,
                rd.n_turno AS turn,
                ((rd.n_vuelta)::integer +1) AS round_trip,
                cv.id_crear_vehiculo AS vehicle_id,
                (rd.id_despacho)::bigint AS dispatch_id,
                rd.h_reg_despachado AS departure_time,
                rd.h_llegada_prog AS arrival_time_scheduled,
                rd.dif_llegada AS arrival_time_difference,
                rd.h_reg_llegada AS arrival_time,
                rd.cancelado AS canceled,
                rd.h_reg_cancelado AS time_canceled,
                rd.observaciones AS status,
                rd.registradora_salida AS start_recorder,
                rd.registradora_llegada AS end_recorder
            FROM
              registrodespacho rd,
              crear_vehiculo cv,
              day_types dt
            WHERE ((cv.placa = (rd.n_placa)::text) AND ((dt.name)::text = (rd.tipo_dia)::text))
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS dispatch_registers");
    }
}
