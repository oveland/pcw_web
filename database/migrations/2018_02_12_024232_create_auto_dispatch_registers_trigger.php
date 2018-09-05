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
            create or replace function auto_dispatch_registers_function() returns trigger
            language plpgsql
            as $$
            DECLARE
              new_dispatch_register_id BIGINT;
              vehicle                  RECORD;
              dispatcher_vehicle       RECORD;
              route                    RECORD;
              company                  RECORD;
            BEGIN
              IF (TG_OP = 'UPDATE')
              THEN
                SELECT *
                FROM vehicles
                WHERE id = NEW.vehicle_id
                LIMIT 1
                INTO vehicle;
                SELECT *
                FROM dispatcher_vehicles
                WHERE id = NEW.dispatcher_vehicle_id
                LIMIT 1
                INTO dispatcher_vehicle;
            
                IF (NEW.in_dispatch IS TRUE AND OLD.in_dispatch IS FALSE AND OLD.dispatch_register_id IS NOT NULL)
                THEN -- ARRIVED TO DISPATCH
                  UPDATE registrodespacho SET h_reg_llegada = NEW.date :: TIME, observaciones = 'Terminó' WHERE id_registro = OLD.dispatch_register_id;
                  NEW.dispatch_register_id = NULL;
                END IF;
            
                IF (NEW.in_dispatch IS FALSE AND OLD.in_dispatch IS TRUE AND dispatcher_vehicle IS NOT NULL)
                THEN -- DISPATCH VEHICLE
                  SELECT * FROM routes WHERE id = dispatcher_vehicle.route_id LIMIT 1 INTO route;
                  SELECT * FROM companies WHERE id = route.company_id LIMIT 1 INTO company;
            
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
                    arrival_fringe_id
                  )
                  VALUES (
                    NEW.date :: DATE,
                    NEW.date :: TIME,
                    company.id,
                    dispatcher_vehicle.dispatch_id,
                    dispatcher_vehicle.route_id,
                    (SELECT dt.name
                     FROM day_types AS dt
                     WHERE dt.id = dispatcher_vehicle.day_type_id),
                    0,
                    vehicle.number,
                    vehicle.plate,
                    '0',
                    NEW.date :: TIME,
                    NEW.date :: TIME,
                    '- 00:00:00',
                    (NEW.date :: TEXT :: TIME :: INTERVAL +
                     (SELECT get_route_total_time_from_dispatch_time(NEW.date, dispatcher_vehicle.route_id))) :: INTERVAL,
                    '00:00:00',
                    '+ 00:00:00',
                    FALSE,
                    NULL,
                    'En camino',
                    '', '', '', 0, 0, '',
                    (SELECT get_fringe_from_dispatch_time(NEW.date :: TEXT :: TIME, dispatcher_vehicle.route_id :: BIGINT,
                                                          dispatcher_vehicle.day_type_id))
                  )
                  RETURNING id_registro
                    INTO new_dispatch_register_id;
            
                  NEW.dispatch_register_id = new_dispatch_register_id;
            
                  UPDATE registrodespacho
                  SET id_view = ('despacho-' || new_dispatch_register_id), ignore_trigger = TRUE
                  WHERE id_registro = new_dispatch_register_id;
            
                END IF;
              END IF;
              RETURN NEW;
            END;
            $$
            ;
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
