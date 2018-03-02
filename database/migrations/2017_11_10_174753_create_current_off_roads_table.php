<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrentOffRoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_off_roads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('date');
            $table->bigInteger('vehicle_id')->unsigned()->unique();
            $table->bigInteger('dispatch_register_id')->unsigned()->nullable();
            $table->bigInteger('reference_location_id')->unsigned()->nullable();
            $table->integer('distance')->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->double('orientation')->nullable();
            $table->double('odometer');
            $table->integer('speed');
            $table->boolean('off_road')->default(false);
            $table->boolean('alert_off_road')->default(false);
            $table->timestamps();

            /* table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('dispatch_register_id')->references('id_registro')->on('registrodespacho')->onDelete('cascade');
            $table->foreign('reference_location_id')->references('id')->on('locations')->onDelete('cascade');
        });

        /* Create function that save current_off_roads alerts from INSERT off_roads_table */
        DB::statement("
            CREATE OR REPLACE FUNCTION off_roads_function() RETURNS TRIGGER
            language plpgsql
            as $$
            DECLARE
              off_road_vehicle RECORD;
              alert_off_road_vehicle BOOLEAN;
            BEGIN
              IF (TG_OP = 'INSERT' ) THEN
                IF (NEW.latitude = 0 OR NEW.longitude = 0) THEN
                  RETURN OLD;
                END IF;
            
                SELECT * FROM current_off_roads WHERE vehicle_id = NEW.vehicle_id LIMIT 1 INTO off_road_vehicle;
            
                IF off_road_vehicle.id IS NOT NULL THEN
                    alert_off_road_vehicle := FALSE;
            
                    IF (NEW.date - off_road_vehicle.date)::TIME > '00:03:00'::TIME THEN
                      alert_off_road_vehicle := TRUE;
                    END IF;
            
                    UPDATE current_off_roads SET
                      date = NEW.date,
                      dispatch_register_id = NEW.dispatch_register_id,
                      distance = NEW.distance,
                      longitude = NEW.longitude,
                      latitude = NEW.latitude,
                      orientation = NEW.orientation,
                      odometer = NEW.odometer,
                      speed = NEW.speed,
                      off_road = NEW.off_road,
                      alert_off_road = alert_off_road_vehicle,
                      updated_at = current_timestamp
                     WHERE vehicle_id = NEW.vehicle_id;
                ELSE
                  INSERT INTO current_off_roads
                  (
                    vehicle_id,
                    date,
                    dispatch_register_id,
                    distance,
                    longitude,
                    latitude,
                    orientation,
                    odometer,
                    speed,
                    off_road,
                    alert_off_road,
                    created_at,
                    updated_at
                  )
                  VALUES (
                    NEW.vehicle_id,
                    current_timestamp,
                    NEW.dispatch_register_id,
                    NEW.distance,
                    NEW.longitude,
                    NEW.latitude,
                    NEW.orientation,
                    NEW.odometer,
                    NEW.speed,
                    NEW.off_road,
                    TRUE,
                    current_timestamp,
                    current_timestamp
                  );
                END IF;
            
              END IF;
              RETURN NEW;
            END;
            $$
            ;
        ");

        /* Create trigger on off_roads table to execute current_off_roads_function on INSERT */
        DB::statement("
            CREATE TRIGGER off_roads_trigger AFTER INSERT
                ON off_roads FOR EACH ROW
            EXECUTE PROCEDURE off_roads_function();
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TRIGGER IF EXISTS off_roads_trigger ON off_roads");
        DB::statement("DROP FUNCTION IF EXISTS off_roads_function()");
        Schema::dropIfExists('current_off_roads');
    }
}
