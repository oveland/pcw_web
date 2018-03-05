<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP FUNCTION IF EXISTS get_fringe_from_dispatch_time(TIME WITHOUT TIME ZONE,BIGINT,INTEGER)");
    }
}
