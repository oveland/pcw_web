<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGetSeatFromHexIndexFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
          CREATE OR REPLACE FUNCTION get_seat_from_hex_index(vehicle_id_in BIGINT, hex_index INTEGER) RETURNS INTEGER
            LANGUAGE plpgsql AS $$
            DECLARE
                seat INTEGER;
                index_hex_loop INTEGER;
                distribution json;
              BEGIN
            
                SELECT json_distribution::json->'row1' as topology from vehicle_seat_distributions where vehicle_id = vehicle_id_in INTO distribution;
            
                FOR seat_loop IN 1..24 LOOP
                  index_hex_loop := distribution->(''||seat_loop);
                  IF index_hex_loop IS NOT NULL AND hex_index = index_hex_loop THEN
                    seat := seat_loop;
                  END IF;
                END LOOP;
            
                IF seat IS NOT NULL THEN
                  RAISE NOTICE 'For index % from HEX the seat is %', hex_index, seat;
                ELSE
                  RAISE NOTICE 'For index % from HEX not seat found!', hex_index;
                END IF;
            
                RETURN seat;
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
        DB::statement("DROP FUNCTION IF EXISTS get_seat_from_hex_index(vehicle_id_in BIGINT, hex_index INTEGER)");
    }
}
