<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLocationReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();

        /*
         * Update general location_reports view
         *
         * This view is used as template to create and segment locations_reports
         */
        DB::statement("
            CREATE OR REPLACE VIEW location_reports AS
              SELECT l.id AS location_id,
                l.vehicle_id,
                l.dispatch_register_id,
                l.off_road,
                l.latitude,
                l.longitude,
                l.orientation,
                l.date AS location_date,
                l.vehicle_status_id,
                l.speed,
                l.distance,
                l.odometer,
                r.id report_id,
                r.date,
                r.timed,
                r.distancem,
                r.status,
                r.status_in_minutes,
                r.control_point_id,
                r.fringe_id
               FROM (locations l LEFT JOIN reports r ON ((r.location_id = l.id)))
        ");

        /*
         * Segment before location_reports view though materialized views
         *
        */

        /* Update segmented tables */
        $numberSegments = config('maintenance.number_segments');
        $daysPerSegment = config('maintenance.day_per_segment');

        foreach (range(1, $numberSegments) as $segment) {
            $interval = $segment * $daysPerSegment;
            $lastInterval = ($segment - 1) * $daysPerSegment;

            DB::statement("
                CREATE MATERIALIZED VIEW location_reports_$segment AS
                SELECT * FROM location_reports                 
                WHERE date > current_date - $interval AND date <= ((current_date - $lastInterval)||' 23:59:59')::TIMESTAMP                
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
                    current_location.vehicle_id,
                    current_location.dispatch_register_id,
                    current_location.off_road,
                    current_location.latitude,
                    current_location.longitude,
                    current_location.orientation,
                    current_location.date,
                    current_location.vehicle_status_id,
                    current_location.speed,
                    current_location.distance,
                    current_location.odometer,
                    NEW.id,
                    NEW.date,                                        
                    NEW.timed,
                    NEW.distancem,
                    NEW.status,
                    NEW.status_in_minutes,
                    NEW.control_point_id,
                    NEW.fringe_id
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
        $numberSegments = config('maintenance.number_segments');
        /* Drop current location_reports tables because this will be overwrite with materialized views */
        foreach (range(1, $numberSegments) as $segment) {
            $targetTable = "location_reports_$segment";
            $table = DB::select("SELECT 1 FROM pg_tables where tablename = '$targetTable'");
            if ($table) DB::statement("DROP TABLE IF EXISTS $targetTable");
        }

        /* Drop last triggers */
        DB::statement("DROP TRIGGER IF EXISTS current_reports_trigger ON current_reports");
        DB::statement("DROP FUNCTION IF EXISTS current_reports_function()");

        /* Drop materialized views */
        foreach (range(1, $numberSegments) as $segment) {
            DB::statement("DROP MATERIALIZED VIEW IF EXISTS location_reports_$segment");
        }
        DB::statement("DROP TABLE IF EXISTS current_location_reports");

        /* Finally drop main view */
        DB::statement('DROP VIEW IF EXISTS location_reports');
    }
}