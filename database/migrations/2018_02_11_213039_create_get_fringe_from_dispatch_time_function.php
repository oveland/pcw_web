<?php

use Illuminate\Database\Migrations\Migration;

class CreateGetFringeFromDispatchTimeFunction extends Migration
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
            CREATE OR REPLACE FUNCTION get_fringe_from_dispatch_time(time_in TIME WITHOUT TIME ZONE, route_id_in BIGINT, day_type_id_in INTEGER)
              RETURNS BIGINT
            LANGUAGE plpgsql
            AS $$
            DECLARE
              fringe_id BIGINT;
            BEGIN
              SELECT f.id
              FROM
                fringes AS f
              WHERE
                f.route_id = route_id_in
                AND f.\"from\" <= time_in
                AND f.day_type_id = day_type_id_in
              ORDER BY f.\"from\" DESC
              LIMIT 1
              INTO fringe_id;
            
              IF fringe_id IS NULL
              THEN
                SELECT f.id
                FROM
                  fringes AS f
                WHERE
                  f.route_id = route_id_in
                  AND f.\"to\" >= time_in
                  AND f.day_type_id = day_type_id_in
                ORDER BY f.\"to\" ASC
                LIMIT 1
                INTO fringe_id;
              END IF;
              RETURN fringe_id;
            END;
            $$
        ");

        DB::statement("
            CREATE OR REPLACE FUNCTION get_fringe_from_dispatch_time(dispatch_register_id BIGINT, from_time VARCHAR(50))
              RETURNS BIGINT
            LANGUAGE plpgsql
            AS $$
            DECLARE
              fringe_id BIGINT;
              dispatch_register RECORD;
              search_time TIME WITHOUT TIME ZONE;
            BEGIN            
              SELECT * FROM dispatch_registers WHERE id = dispatch_register_id LIMIT 1 INTO dispatch_register;            
              search_time = CASE WHEN (from_time = 'arrival' AND dispatch_register.status = 'Terminó' ) THEN dispatch_register.arrival_time ELSE dispatch_register.departure_time END;            
              SELECT get_fringe_from_dispatch_time(search_time,dispatch_register.route_id,dispatch_register.type_of_day) INTO fringe_id;            
              RETURN fringe_id;
            END;
            $$
        ");

        DB::statement("
            CREATE OR REPLACE FUNCTION get_fringe_from_dispatch_time(dispatch_register_id BIGINT)
              RETURNS BIGINT
            LANGUAGE plpgsql
            AS $$
            DECLARE
              fringe_id BIGINT;
            BEGIN
              SELECT get_fringe_from_dispatch_time(dispatch_register_id,'departure') INTO fringe_id;
              RETURN fringe_id;
            END;
            $$
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP FUNCTION IF EXISTS get_fringe_from_dispatch_time(BIGINT)");
        DB::statement("DROP FUNCTION IF EXISTS get_fringe_from_dispatch_time(BIGINT,VARCHAR(50))");
        DB::statement("DROP FUNCTION IF EXISTS get_fringe_from_dispatch_time(TIME WITHOUT TIME ZONE,BIGINT,INTEGER)");
    }
}
