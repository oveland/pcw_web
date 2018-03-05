<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGetTimezoneByVehiclePlateFunction extends Migration
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
            CREATE OR REPLACE FUNCTION get_time_zone_by_vehicle_plate(CHARACTER VARYING) RETURNS CHARACTER VARYING
            LANGUAGE plpgsql
            AS $$
            DECLARE
              vehicle_plate ALIAS FOR $1;
              timezone_vehicle VARCHAR(100);
            BEGIN
              SELECT \"timezone\" FROM companies WHERE id = (SELECT company_id FROM vehicles WHERE plate = vehicle_plate ) LIMIT 1 INTO timezone_vehicle;
              IF timezone_vehicle IS NULL THEN
                timezone_vehicle := 'America/Bogota';
              END IF;
              RETURN timezone_vehicle;
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
        DB::statement("DROP FUNCTION IF EXISTS get_time_zone_by_vehicle_plate(CHARACTER VARYING)");
    }
}
