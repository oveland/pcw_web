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
            CREATE OR REPLACE VIEW recorder_counter_per_days AS
            SELECT
            max(dr.id) AS dispatch_register_id,
            dr.date,
            v.id AS vehicle_id,
            v.company_id,
            v.number,
            CASE
                WHEN (max(dr.start_recorder) > 0) THEN
                (
                  SELECT min(drs.start_recorder)
                    FROM dispatch_registers AS drs
                  WHERE drs.date = dr.date AND drs.vehicle_id = v.id AND drs.start_recorder > 0
                  LIMIT 1
                )
                ELSE
                (
                  SELECT max(drp.end_recorder) AS max
                    FROM dispatch_registers drp
                  WHERE drp.date < dr.date AND drp.vehicle_id = v.id AND drp.end_recorder > 0
                  GROUP BY drp.date
                  ORDER BY drp.date DESC
                  LIMIT 1
                )
            END AS start_recorder,
            0::BIGINT AS start_recorder_prev,
            CASE
              WHEN (max(dr.start_recorder) > 0) THEN
              (
                dr.date
              )
              ELSE
              (
                SELECT drp.date AS max
                FROM dispatch_registers drp
                WHERE drp.date < dr.date AND drp.vehicle_id = v.id AND drp.end_recorder > 0
                ORDER BY drp.date DESC
                LIMIT 1
              )
            END AS date_start_recorder_prev,
            max(dr.end_recorder) AS end_recorder,
            (
                max(dr.end_recorder) -
                CASE
                  WHEN (max(dr.start_recorder) > 0) THEN
                  (
                    SELECT min(drs.start_recorder)
                    FROM dispatch_registers AS drs
                    WHERE drs.date = dr.date AND drs.vehicle_id = v.id AND drs.start_recorder > 0
                    LIMIT 1
                  )
                  ELSE
                  (
                    SELECT max(drp.end_recorder) AS max
                    FROM dispatch_registers drp
                    WHERE drp.date < dr.date AND drp.vehicle_id = v.id AND drp.end_recorder > 0
                    GROUP BY drp.date
                    ORDER BY drp.date DESC
                    LIMIT 1
                  )
                END
            ) AS passengers
            FROM (dispatch_registers dr
             JOIN vehicles v ON ((v.id = dr.vehicle_id)))
            WHERE ((dr.status::text = 'Terminó'::text) OR (dr.status::text = 'En camino'::text))
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
