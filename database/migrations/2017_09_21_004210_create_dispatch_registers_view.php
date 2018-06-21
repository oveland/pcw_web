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
        $this->down();

        DB::statement("
            CREATE OR REPLACE VIEW dispatch_registers AS
              SELECT rd.id_registro AS id,
                rd.fecha AS date,
                rd.hora AS \"time\",
                rd.id_ruta AS route_id,
                dt.id AS type_of_day,
                rd.n_turno AS turn,
                    CASE
                        WHEN (rd.fecha <= '2017-10-13'::date) THEN ((rd.n_vuelta)::integer + 1)
                        ELSE (rd.n_vuelta)::integer
                    END AS round_trip,
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
                rd.registradora_llegada AS end_recorder,
                    CASE
                        WHEN (rd.fecha < '2018-03-10'::date) THEN ( SELECT rdd.codigo_interno_conductor
                           FROM registrodespacho rdd
                          WHERE ((rdd.fecha = rd.fecha) AND ((rdd.n_placa)::text = (rd.n_placa)::text) AND ((rdd.codigo_interno_conductor)::text <> ''::text) AND (rdd.id_registro <= rd.id_registro))
                          ORDER BY rdd.id_registro DESC
                         LIMIT 1)
                        ELSE rd.codigo_interno_conductor
                    END AS driver_code,
                rd.departure_fringe_id,
                rd.arrival_fringe_id,
                rd.available_vehicles,
                rd.user_id
               FROM registrodespacho rd,
                crear_vehiculo cv,
                day_types dt
              WHERE ((cv.placa = (rd.n_placa)::text) AND ((dt.name)::text = (rd.tipo_dia)::text))
        ");

        DB::statement("
            CREATE OR REPLACE VIEW passengers_dispatch_registers AS
              SELECT
                rd.id_registro                 AS id,
                rd.fecha                       AS \"date\",
                rd.hora                        AS \"time\",
                rd.id_ruta                     AS route_id,
                dt.id                          AS type_of_day,
                rd.n_turno                     AS turn,
                rd.n_vuelta                    AS round_trip,
                cv.id_crear_vehiculo           AS vehicle_id,
                (rd.id_despacho) :: BIGINT     AS dispatch_id,
                rd.h_reg_despachado            AS departure_time,
                rd.h_llegada_prog              AS arrival_time_scheduled,
                rd.dif_llegada                 AS arrival_time_difference,
                rd.h_reg_llegada               AS arrival_time,
                rd.cancelado                   AS canceled,
                rd.h_reg_cancelado             AS time_canceled,
                rd.observaciones               AS status,
                rd.registradora_salida         AS start_recorder,
                rd.registradora_llegada        AS end_recorder
              FROM
                registrodespacho rd
              JOIN crear_vehiculo cv ON ( cv.placa = (rd.n_placa) :: TEXT)
              JOIN day_types dt ON ((dt.name) :: TEXT = (rd.tipo_dia) :: TEXT)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS passengers_dispatch_registers");
        DB::statement("DROP VIEW IF EXISTS dispatch_registers");
    }
}
