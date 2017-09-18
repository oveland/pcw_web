<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW location_reports AS
            SELECT
              l.id location_id,
              l.dispatch_register_id,
              l.off_road,
              l.latitude,
              l.longitude,
              r.date,
              r.timed,
              r.distancem,
              r.status_in_minutes
            FROM locations as l
              INNER JOIN reports as r ON (r.location_id = l.id)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS location_reports');
    }
}
