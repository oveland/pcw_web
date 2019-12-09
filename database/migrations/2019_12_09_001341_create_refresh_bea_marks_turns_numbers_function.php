<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefreshBeaMarksTurnsNumbersFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE FUNCTION refresh_bea_marks_turns_numbers_function(vehicle_id_in BIGINT, date_in DATE) RETURNS VOID
                LANGUAGE plpgsql
            AS $$
            DECLARE
                marks CURSOR FOR SELECT * FROM bea_marks WHERE date::DATE = date_in AND trajectory_id IS NOT NULL AND turn_id IN (SELECT id FROM bea_turns WHERE vehicle_id = vehicle_id_in) ORDER BY date;
                next_turn INTEGER;
            BEGIN
                next_turn := 1;
                FOR mark IN marks LOOP
                    UPDATE bea_marks SET number = next_turn WHERE id = mark.id;
                    next_turn := next_turn + 1;
                END LOOP ;
            END;
            $$;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP FUNCTION IF EXISTS refresh_bea_marks_turns_numbers_function(BIGINT, DATE)");
    }
}
