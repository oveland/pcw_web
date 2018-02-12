<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoDispatchRegistersTrigger extends Migration
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
            CREATE OR REPLACE FUNCTION auto_dispatch_registers_function() RETURNS TRIGGER
            LANGUAGE plpgsql
            AS $$
            DECLARE
              new_dispatch_register_id BIGINT;
              vehicle RECORD;
            BEGIN
              IF (TG_OP = 'UPDATE') THEN
                SELECT * FROM vehicles WHERE id = NEW.vehicle_id LIMIT 1 INTO vehicle;
            
                IF (NEW.in_dispatch IS TRUE AND OLD.in_dispatch IS FALSE) THEN   -- ARRIVED TO DISPATCH
                  NEW.date = current_timestamp;
                  NEW.hour = current_time;
            
                  UPDATE registrodespacho SET h_reg_llegada = NEW.hour, observaciones = 'Terminó' WHERE id_registro = OLD.dispatch_register_id AND observaciones = 'En camino';
                  NEW.dispatch_register_id = NULL;
                END IF;
            
                IF (NEW.in_dispatch IS FALSE AND OLD.in_dispatch IS TRUE) THEN -- DISPATCH VEHICLE
                  NEW.date = current_timestamp;
                  NEW.hour = current_time;
            
                  new_dispatch_register_id = 0;
            
                  INSERT INTO public.registrodespacho (
                    fecha,
                    hora,
                    id_empresa,
                    id_despacho,
                    id_ruta,
                    tipo_dia,
                    n_turno,
                    n_vehiculo,
                    n_placa,
                    n_vuelta,
                    h_salida_prog,
                    h_reg_despachado,
                    dif_salida,
                    h_llegada_prog,
                    h_reg_llegada,
                    dif_llegada,
                    cancelado,
                    h_reg_cancelado,
                    observaciones,
                    parametros,
                    id_view,
                    tiempos_puntos,
                    registradora_salida,
                    registradora_llegada,
                    codigo_interno_conductor,
                    fringe_id
                  )
                  VALUES (
                    NEW.date,
                    NEW.hour,
                    27,
                    44,
                    172,
                    (SELECT dt.name FROM day_types as dt WHERE dt.id = (SELECT get_day_type_id(NEW.date::DATE))),
                    0,
                    vehicle.number,
                    vehicle.plate,
                    '0',
                    NEW.hour::TEXT::TIME,
                    NEW.hour::TEXT::TIME,
                    '- 00:00:00',
                    (current_time + (SELECT get_route_total_time_from_dispatch_time((NEW.date||' '||NEW.hour)::TIMESTAMP,172)))::TIME,
                    '00:00:00',
                    '+ 00:00:00',
                    FALSE,
                    NULL,
                    'En camino',
                    '','','',0,0,'', (SELECT get_fringe_from_dispatch_time(NEW.hour, 172::BIGINT,(SELECT get_day_type_id(NEW.date::DATE))))
                  ) RETURNING id_registro INTO new_dispatch_register_id;
            
                  NEW.dispatch_register_id = new_dispatch_register_id;
            
                  UPDATE registrodespacho SET id_view = ('despacho-'||new_dispatch_register_id) WHERE id_registro = new_dispatch_register_id;
            
                END IF;
              END IF;
              RETURN NEW;
            END;
            $$
        ");

        DB::statement("
            CREATE TRIGGER auto_dispatch_registers_trigger BEFORE INSERT OR UPDATE
            ON auto_dispatch_registers FOR EACH ROW
            EXECUTE PROCEDURE auto_dispatch_registers_function();
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TRIGGER IF EXISTS auto_dispatch_registers_trigger ON auto_dispatch_registers");
        DB::statement("DROP FUNCTION IF EXISTS auto_dispatch_registers_function()");
    }
}
