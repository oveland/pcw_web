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
            create or replace view dispatch_registers as
              SELECT rd.id_registro AS id, rd.fecha AS date,
              rd.hora AS \"time\",
              rd.id_ruta AS route_id,
              dt.id AS type_of_day,
              rd.n_turno AS turn,
              (rd.n_vuelta)::integer AS round_trip,
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
              rd.codigo_interno_conductor AS driver_code,
              rd.departure_fringe_id,
              rd.arrival_fringe_id,
              rd.available_vehicles,
              rd.user_id,
              rd.initial_sensor_counter,
              rd.initial_frame_sensor_counter,
              rd.initial_sensor_recorder,
              rd.final_sensor_counter,
              rd.final_frame_sensor_counter,
              rd.final_sensor_recorder,
              rd.initial_time_sensor_counter,
              rd.final_time_sensor_counter,
              rd.initial_front_sensor_counter,
              rd.initial_back_sensor_counter,
              rd.final_front_sensor_counter,
              rd.final_back_sensor_counter,
              rd.initial_counter_obs,
              rd.final_counter_obs
            FROM registrodespacho rd,
              crear_vehiculo cv,
              day_types dt
            WHERE ((cv.placa = (rd.n_placa)::text) AND ((dt.name)::text = (rd.tipo_dia)::text));
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
