<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParseIntervalToTimeFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE FUNCTION parse_interval_to_time(str_time TEXT)
              RETURNS TIME AS $$
            DECLARE
              array_time TEXT[];
              parsed_time TIME;
            BEGIN
              array_time := regexp_split_to_array($1, ':');
              
              IF array_length(array_time,1) = 2 THEN
                SELECT (CONCAT(array_time [1], ' minutes ', array_time [2], ' seconds') :: INTERVAL) :: TIME INTO parsed_time;
              ELSE
                SELECT (CONCAT(array_time [1], ' hours ', array_time [2], ' minutes ', array_time [3], ' seconds') :: INTERVAL) :: TIME INTO parsed_time;
              END IF;                            
              
              RETURN parsed_time;
            END;
            $$  LANGUAGE plpgsql
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP FUNCTION IF EXISTS parse_interval_to_time(TEXT)");
    }
}
