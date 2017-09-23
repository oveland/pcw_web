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
            SELECT dr.date,
            v.id AS vehicle_id,
            v.company_id,
            v.number,
            max(dr.start_recorder) AS start_recorder,
            (
            CASE
            WHEN (max(dr.start_recorder) = 0) THEN
              (
                SELECT max(drp.end_recorder)
                FROM dispatch_registers as drp
                  WHERE drp.date < dr.date
                  AND drp.vehicle_id = v.id
                  AND drp.end_recorder > 0
                GROUP BY drp.date
                ORDER BY drp.date
                DESC LIMIT 1
              )
            ELSE
              max(dr.start_recorder)
            END
            ) AS start_recorder_prev,
            
            (
            CASE
            WHEN (max(dr.start_recorder) = 0) THEN
              (
                SELECT drp.date
                FROM dispatch_registers as drp
                WHERE drp.date < dr.date
                      AND drp.vehicle_id = v.id
                      AND drp.end_recorder > 0
                GROUP BY drp.date
                ORDER BY drp.date
                DESC LIMIT 1
              )
            ELSE
              max(dr.date)
            END
            ) AS date_start_recorder_prev,
            
            max(dr.end_recorder) AS end_recorder,
            (
            max(dr.end_recorder) -
            (
              CASE
              WHEN (max(dr.start_recorder) = 0) THEN
                (
                  SELECT max(drp.end_recorder)
                  FROM dispatch_registers as drp
                  WHERE drp.date < dr.date
                        AND drp.vehicle_id = v.id
                        AND drp.end_recorder > 0
                  GROUP BY drp.date
                  ORDER BY drp.date
                  DESC LIMIT 1
                )
              ELSE
                max(dr.start_recorder)
              END
            )
            ) AS passengers
            FROM (dispatch_registers dr
            JOIN vehicles v ON ((v.id = dr.vehicle_id)))
            WHERE (((dr.status)::text = 'Terminó'::text) OR ((dr.status)::text = 'En camino'::text))
            --AND dr.date = '2017-09-18' AND v.number = '381'
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
