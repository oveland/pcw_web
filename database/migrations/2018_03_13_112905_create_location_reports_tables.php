<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationReportsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();

        $numberSegments = config('maintenance.number_segments');

        foreach (range(1, $numberSegments) as $segment) {
            DB::statement("
                CREATE TABLE location_reports_$segment AS  
                SELECT * FROM location_reports
                WITH NO DATA
            ");
        }
        /*
         * -----------------------------------
         * Create current_location_reports table
         * -----------------------------------
         *
         *  This table is fed by current_location_trigger
         *
         */
        DB::statement("
            CREATE TABLE current_location_reports AS  
            SELECT * FROM location_reports
            WITH NO DATA
        ");

        /* Create current_reports function that uses the trigger */
        DB::statement("
            CREATE OR REPLACE FUNCTION current_reports_function() RETURNS TRIGGER
            LANGUAGE plpgsql
            AS $$
            DECLARE
              current_location RECORD;
            BEGIN
              IF (TG_OP = 'INSERT' ) OR (TG_OP = 'UPDATE' ) THEN
                SELECT * FROM current_locations AS cl WHERE cl.vehicle_id = NEW.vehicle_id AND cl.date::DATE = NEW.date::DATE LIMIT 1 INTO current_location;
                IF current_location IS NOT NULL THEN
                  INSERT INTO current_location_reports VALUES (
                    current_location.id,
                    NEW.dispatch_register_id,
                    current_location.off_road,
                    current_location.latitude,
                    current_location.longitude,
                    NEW.date,
                    current_location.date,
                    NEW.timed,
                    NEW.distancem,
                    NEW.status_in_minutes
                  );
                END IF;
              END IF;
              RETURN NEW;
            END;
            $$;
        ");

        /* Create the trigger for current_locations_reports */
        DB::statement("
            CREATE TRIGGER current_reports_trigger BEFORE INSERT OR UPDATE
              ON current_reports FOR EACH ROW
            EXECUTE PROCEDURE current_reports_function()
        ");
        /* NOTE: The trigger is set to current_reports table because its data is saved after those in the current_locations table */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TRIGGER IF EXISTS current_reports_trigger ON current_reports");
        DB::statement("DROP FUNCTION IF EXISTS current_reports_function()");

        /* ------------------------------------------------------------------------------------------ */

        $numberSegments = config('maintenance.number_segments');

        foreach (range(1, $numberSegments) as $segment) {
            DB::statement("DROP TABLE IF EXISTS location_reports_$segment");
        }

        DB::statement("DROP TABLE IF EXISTS current_location_reports");
    }
}
