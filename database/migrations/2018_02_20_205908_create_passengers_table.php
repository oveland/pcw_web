<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePassengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date');
            $table->integer('total');
            $table->integer('total_prev');
            $table->integer('total_front_sensor')->default(0);
            $table->integer('total_back_sensor')->default(0);
            $table->integer('total_sensor_recorder')->default(0);
            $table->integer('total_platform');
            $table->bigInteger('vehicle_id')->unsigned();
            $table->integer('vehicle_status_id')->nullable();
            $table->bigInteger('dispatch_register_id')->unsigned()->nullable();
            $table->bigInteger('location_id')->unsigned()->nullable();
            $table->bigInteger('fringe_id')->unsigned()->nullable();
            $table->bigInteger('counter_issue_id')->unsigned()->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->string('frame',512);
            $table->timestamps();

            /* Table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('counter_issue_id')->references('id')->on('counter_issues')->onDelete('cascade');
            $table->foreign('fringe_id')->references('id')->on('fringes')->onDelete('cascade');
            //$table->foreign('dispatch_register_id')->references('id')->on('dispatch_registers')->onDelete('cascade');
            //$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });

        DB::statement("
            CREATE OR REPLACE FUNCTION passengers_function() RETURNS TRIGGER
                LANGUAGE plpgsql
            AS $$
            DECLARE
                vehicle RECORD;
                dr RECORD;
                location RECORD;
                route_multi_tariff RECORD;
            
                last_passenger RECORD;
            
                p_tariff INTEGER;
                p_counted INTEGER;
                p_charge INTEGER;
                p_total_charge INTEGER;
            
                current_charges RECORD;
            BEGIN
                IF TG_OP = 'INSERT' OR  TG_OP = 'UPDATE' THEN
                    p_tariff := 0;
                    p_counted := 0;
                    p_charge := 0;
                    p_total_charge := 0;
            
                    SELECT * FROM vehicles where id = NEW.vehicle_id INTO vehicle;
                    SELECT * FROM locations_0 where id = NEW.location_id INTO location;
            
                    IF NEW.dispatch_register_id IS NULL THEN
                        NEW.dispatch_register_id := (SELECT id_registro FROM registrodespacho WHERE fecha = NEW.date::DATE AND n_placa = vehicle.plate AND NEW.date::TIME BETWEEN h_reg_despachado::TIME AND h_reg_llegada::TIME AND (observaciones = 'Terminó' OR observaciones = 'En camino') LIMIT 1);
                    END IF;
                    SELECT * FROM registrodespacho WHERE id_registro = NEW.dispatch_register_id LIMIT 1 INTO dr;
            
                    SELECT * FROM passengers WHERE vehicle_id = NEW.vehicle_id AND date < NEW.date ORDER BY date DESC LIMIT 1 INTO last_passenger;
            
                    p_counted := NEW.total - NEW.total_prev;
                    IF p_counted < 0 THEN
                        p_counted := 0;
                    END IF;
            
                    IF dr.id_registro IS NOT NULL THEN
                        NEW.fringe_id = get_fringe_from_dispatch_time(dr.id_registro, 'current_time');
            
                        IF vehicle.company_id = 17 THEN
                            SELECT *
                            FROM route_multi_tariffs
                            WHERE route_id = dr.id_ruta
                              AND distance > location.distance
                            ORDER BY distance ASC
                            LIMIT 1
                            INTO route_multi_tariff;
            
                            IF route_multi_tariff.value IS NULL THEN
                                SELECT *
                                FROM route_multi_tariffs
                                WHERE route_id = dr.id_ruta
                                ORDER BY value ASC
                                LIMIT 1
                                INTO route_multi_tariff;
                            END IF;
            
                            IF route_multi_tariff.value IS NOT NULL THEN
                                p_tariff := route_multi_tariff.value;
                            END IF;
                        ELSE
                            SELECT passenger FROM route_tariffs WHERE route_id = dr.id_ruta LIMIT 1 INTO p_tariff;
                        END IF;
            
                        IF p_tariff IS NOT NULL AND p_tariff > 0 THEN
                            p_charge := p_counted * p_tariff;
            
                            IF last_passenger.id IS NOT NULL THEN
                                p_total_charge := last_passenger.total_charge + p_charge;
                            ELSE
                                p_total_charge := p_charge;
                            END IF;
            
                            SELECT * FROM current_tariff_charges WHERE vehicle_id = NEW.vehicle_id AND tariff = p_tariff LIMIT 1 INTO current_charges;
            
                            IF current_charges.id IS NOT NULL THEN
                                IF NEW.date::date = current_charges.updated_at::DATE THEN
                                    UPDATE current_tariff_charges SET
                                                                      charge = p_charge,
                                                                      total_charge = total_charge + p_charge,
                                                                      total_counted = total_counted + p_counted,
                                                                      updated_at = current_timestamp
                                    WHERE id = current_charges.id;
                                ELSE
                                    UPDATE current_tariff_charges SET
                                                                      charge = p_charge,
                                                                      total_charge = p_total_charge,
                                                                      total_counted = p_counted,
                                                                      updated_at = current_timestamp
                                    WHERE id = current_charges.id;
                                END IF;
                            ELSE
                                INSERT INTO current_tariff_charges (tariff, charge, total_charge, updated_at, vehicle_id, total_counted) VALUES (p_tariff, p_charge, p_charge, current_timestamp, NEW.vehicle_id, p_counted);
                            END IF;
                        END IF;
                    END IF;
            
                    NEW.tariff := p_tariff;
                    NEW.counted := p_counted;
                    NEW.charge := p_charge;
                    NEW.total_charge := p_total_charge;
            
                END IF;
            
                RETURN NEW;
            END;
            $$;
        ");

        DB::statement("
            CREATE TRIGGER passengers_trigger BEFORE INSERT OR UPDATE
                ON passengers FOR EACH ROW
            EXECUTE PROCEDURE passengers_function();
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TRIGGER IF EXISTS passengers_trigger ON passengers");
        DB::statement("DROP FUNCTION IF EXISTS passengers_function()");
        Schema::dropIfExists('passengers');
    }
}
