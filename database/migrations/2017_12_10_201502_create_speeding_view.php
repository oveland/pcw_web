<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpeedingView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE VIEW speeding AS
              SELECT
                e.id_frame  id,
                e.fecha     \"date\",
                e.hora      \"time\",
                v.id        vehicle_id,
                e.velocidad speed,
                e.latitud   latitude,
                e.longitud  longitude,
                e.dispatch_register_id
              FROM excepciones AS e
                JOIN vehicles AS v ON (v.plate = e.usuario)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS speeding");
    }
}
