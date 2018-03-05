<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GetRouteTotalTimeFromDispatchTimeFunction extends Migration
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
            CREATE OR REPLACE FUNCTION get_route_total_time_from_dispatch_time(timestamp_in TIMESTAMP WITHOUT TIME ZONE, route_id_in BIGINT)
              RETURNS TIME
            LANGUAGE plpgsql
            AS $$
            DECLARE
              total_time TIME;
            BEGIN
              SELECT cpt.time_from_dispatch::TIME FROM control_point_times as cpt
              WHERE fringe_id = (
                SELECT get_fringe_from_dispatch_time(
                    timestamp_in::TIME,
                    route_id_in,
                    (SELECT get_day_type_id(timestamp_in::DATE))
                )
                LIMIT 1
              )
              ORDER BY time_from_dispatch DESC LIMIT 1
              INTO total_time;
              RETURN total_time;
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
        DB::statement("DROP FUNCTION IF EXISTS get_route_total_time_from_dispatch_time( TIMESTAMP WITHOUT TIME ZONE, BIGINT )");
    }
}
