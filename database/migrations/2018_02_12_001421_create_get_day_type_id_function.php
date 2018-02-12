<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGetDayTypeIdFunction extends Migration
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
            CREATE OR REPLACE FUNCTION get_day_type_id(time_in DATE)
              RETURNS INTEGER
            LANGUAGE plpgsql
            AS $$
            DECLARE
              week_day INTEGER;
              day_type_id INTEGER;
            BEGIN
              SELECT EXTRACT(DOW FROM time_in) INTO week_day;
            
              IF week_day = 0 THEN
                day_type_id = 2;
              ELSE
                IF week_day = 1 THEN
                  day_type_id = 3;
                ELSE
                  day_type_id = 1;
                END IF;
              END IF;
            
              RETURN day_type_id;
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
        DB::statement("DROP FUNCTION IF EXISTS get_day_type_id(DATE)");
    }
}
