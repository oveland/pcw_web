<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrentLocationsGpsView extends Migration
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
            CREATE OR REPLACE VIEW current_locations_gps AS SELECT m.id,
              (((m.fecha || ' '::text) || m.hora))::timestamp without time zone AS date,
              m.lat AS latitude,
              m.lng AS longitude,
              m.orientacion AS orientation,
              m.velocidad AS speed,
              m.status vehicle_status_id,
              v.id AS vehicle_id,
              m.name AS vehicle_plate
            FROM (markers m
              JOIN vehicles v ON (((v.plate)::text = (m.name)::text)))
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS current_locations_gps");
    }
}
