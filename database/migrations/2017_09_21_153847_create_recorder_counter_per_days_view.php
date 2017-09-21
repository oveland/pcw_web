<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecorderCounterPerDaysView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE VIEW recorder_counter_per_days AS SELECT dr.date,
                v.id AS vehicle_id,
                v.company_id,
                v.number,
                max(dr.start_recorder) AS start_recorder,
                CASE
                    WHEN (max(dr.start_recorder) = 0) THEN (12)::bigint
                    ELSE max(dr.start_recorder)
                END AS start_recorder_prev,
                max(dr.end_recorder) AS end_recorder,
                (max(dr.end_recorder) - max(dr.start_recorder)) AS passengers
            FROM (dispatch_registers dr
                JOIN vehicles v ON ((v.id = dr.vehicle_id)))
            WHERE (dr.status = 'Terminó' OR dr.status = 'En camino')
            GROUP BY v.id, dr.date, v.company_id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS recorder_counter_per_days");
    }
}
