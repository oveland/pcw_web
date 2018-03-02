<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffRoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Create off_road table from locations with off_road */
        $dateChangedOffRoadMethod = '2017-09-17';
        //DB::statement("CREATE TABLE off_roads AS SELECT l.* FROM locations AS l WHERE date < '$dateChangedOffRoadMethod' OR (off_road = TRUE AND date >= '$dateChangedOffRoadMethod')");
        DB::statement("CREATE TABLE off_roads AS SELECT l.* FROM locations AS l WHERE (off_road = TRUE AND date >= '$dateChangedOffRoadMethod')");

        /* Create function that save off_roads from INSERT locations */
        DB::statement("
            CREATE OR REPLACE FUNCTION locations_function() RETURNS TRIGGER
            LANGUAGE plpgsql
            AS $$
                DECLARE
                BEGIN
                IF (TG_OP = 'INSERT' ) THEN
                    IF NEW.off_road = TRUE THEN
                        INSERT INTO off_roads VALUES (NEW.id,NEW.version,NEW.date,NEW.date_created,NEW.dispatch_register_id,NEW.distance,NEW.last_updated,NEW.latitude,NEW.longitude,NEW.odometer,NEW.orientation,NEW.speed,NEW.status,NEW.vehicle_id,NEW.off_road);
                    END IF;
                END IF;
                RETURN NEW;
                END;
            $$;
        ");

        /* Create trigger on locations table to execute locations_function on INSERT */
        DB::statement("
            CREATE TRIGGER off_roads_trigger BEFORE INSERT
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
        DB::statement("DROP TRIGGER IF EXISTS locations_trigger ON locations");
        DB::statement("DROP FUNCTION IF EXISTS locations_function()");
        DB::statement("DROP TABLE IF EXISTS off_roads");
    }
}
