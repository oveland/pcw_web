<?php

use Illuminate\Database\Migrations\Migration;

class CreateRouteFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE FUNCTION get_route_distance_from_dr(in_dispatch_register_id BIGINT) RETURNS INT AS
            $$
            DECLARE
                last_location RECORD;
                dr RECORD;
                dispatch RECORD;
                traveled_distance INT;
            BEGIN
                SELECT * FROM dispatch_registers WHERE id = in_dispatch_register_id LIMIT 1 INTO dr;
                SELECT * FROM dispatches WHERE id = dr.dispatch_id LIMIT 1 INTO dispatch;
                SELECT * FROM locations WHERE dispatch_register_id = in_dispatch_register_id ORDER BY date DESC LIMIT 1 INTO last_location;
            
                IF last_location.id IS NOT NULL THEN
                    SELECT geodistance(last_location.latitude, last_location.longitude, dispatch.latitude, dispatch.longitude) INTO traveled_distance;
            
                    IF last_location.distance::INT > traveled_distance::INT THEN
                        RETURN last_location.distance::INT;
                    END IF;
            
                    RETURN traveled_distance::INT;
                END IF;
            
                RETURN 0;
            END;
            $$ LANGUAGE PLPGSQL
        ");

        DB::statement("
            CREATE OR REPLACE FUNCTION get_total_locations_from_dr(in_dispatch_register_id BIGINT) RETURNS INT AS
            $$
            BEGIN
                RETURN (SELECT count(1) FROM locations WHERE dispatch_register_id = in_dispatch_register_id);
            END;
            $$ LANGUAGE PLPGSQL;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP FUNCTION IF EXISTS get_total_locations_from_dr(in_dispatch_register_id BIGINT)");
        DB::statement("DROP FUNCTION IF EXISTS get_route_distance_from_dr(BIGINT)");
    }
}
