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

            /* Table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('dispatch_register_id')->references('id_registro')->on('registrodespacho')->onDelete('cascade');
            $table->foreign('reference_location_id')->references('id')->on('locations')->onDelete('cascade');
        });

        /* Create function that save current_off_roads alerts from INSERT off_roads_table */
        DB::statement("
            CREATE OR REPLACE FUNCTION locations_function() RETURNS TRIGGER
            LANGUAGE plpgsql
            AS $$
            DECLARE
              off_road_vehicle RECORD;
              alert_off_road_vehicle BOOLEAN;
            BEGIN
              IF NEW.date > CURRENT_TIMESTAMP THEN
                NEW.date = CURRENT_TIMESTAMP;
              END IF;
            
              IF (NEW.latitude = 0 OR NEW.longitude = 0) THEN
                RETURN NULL;
              END IF;
            
              IF (TG_OP = 'INSERT') THEN
                IF (NEW.off_road IS TRUE) THEN
            
                  SELECT * FROM current_off_roads WHERE vehicle_id = NEW.vehicle_id LIMIT 1 INTO off_road_vehicle;
            
                  IF off_road_vehicle.id IS NOT NULL THEN
            
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
                      alert_off_road = ((NEW.date - off_road_vehicle.date) :: TIME > '00:03:00' :: TIME),
                      updated_at = CURRENT_TIMESTAMP
                    WHERE vehicle_id = NEW.vehicle_id;
                  ELSE
                    INSERT INTO current_off_roads (vehicle_id,date,dispatch_register_id,distance,longitude,latitude,orientation,odometer,speed,off_road,alert_off_road,created_at,updated_at)
                    VALUES (
                    NEW.vehicle_id,
                    CURRENT_TIMESTAMP,
                    NEW.dispatch_register_id,
                    NEW.distance,
                    NEW.longitude,
                    NEW.latitude,
                    NEW.orientation,
                    NEW.odometer,
                    NEW.speed,
                    NEW.off_road,
                    TRUE,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                    );
                  END IF;
                END IF;
              END IF;
              RETURN NEW;
            END;
            $$;
        ");

        /* Create trigger on off_roads table to execute current_off_roads_function on INSERT */
        DB::statement("
            CREATE TRIGGER locations_trigger AFTER INSERT
                ON off_roads FOR EACH ROW
            EXECUTE PROCEDURE locations_function();
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TRIGGER IF EXISTS locations_trigger ON off_roads");
        DB::statement("DROP FUNCTION IF EXISTS locations_function()");
        Schema::dropIfExists('current_off_roads');
    }
}
