<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecorderCounterPerRoundTripsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE VIEW recorder_counter_per_round_trips AS
            SELECT
            dr.id                AS dispatch_register_id,
            dr.date,
            dr.route_id,
            v.id                 AS vehicle_id,
            v.company_id,
            v.number,
            CASE
            WHEN dr.start_recorder = 0
              THEN
                (
                  SELECT drs.start_recorder
                  FROM dispatch_registers AS drs
                  WHERE drs.date = dr.date AND drs.vehicle_id = v.id AND drs.start_recorder > 0
                  ORDER BY drs.start_recorder ASC
                  LIMIT 1
                )
            ELSE
              (
                SELECT drs.start_recorder
                FROM dispatch_registers AS drs
                WHERE drs.date = dr.date AND drs.vehicle_id = v.id AND drs.start_recorder > 0 AND drs.route_id = dr.route_id
                ORDER BY drs.start_recorder ASC
                LIMIT 1
              )
            END                  AS start_recorder,
            CASE
            WHEN dr.start_recorder > 0
              THEN
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
            END                  AS date_start_recorder_prev,
            max(dr.end_recorder) AS end_recorder,
            CASE WHEN (
                        SELECT drp.end_recorder
                        FROM dispatch_registers AS drp
                        WHERE drp.date = dr.date AND drp.vehicle_id = v.id AND drp.end_recorder > 0 AND drp.id < dr.id
                        ORDER BY drp.id DESC
                        LIMIT 1
                      ) IS NULL
              THEN
                dr.start_recorder
            ELSE
              (
                SELECT drp.end_recorder
                FROM dispatch_registers AS drp
                WHERE drp.date = dr.date AND drp.vehicle_id = v.id AND drp.end_recorder > 0 AND drp.id < dr.id
                ORDER BY drp.id DESC
                LIMIT 1
              )
            END                  AS end_recorder_prev,
            (
              max(dr.end_recorder) -
              CASE
              WHEN dr.start_recorder = 0
                THEN
                  (
                    SELECT drs.start_recorder
                    FROM dispatch_registers AS drs
                    WHERE drs.date = dr.date AND drs.vehicle_id = v.id AND drs.start_recorder > 0
                    ORDER BY drs.start_recorder ASC
                    LIMIT 1
                  )
              ELSE
                (
                  SELECT drs.start_recorder
                  FROM dispatch_registers AS drs
                  WHERE drs.date = dr.date AND drs.vehicle_id = v.id AND drs.start_recorder > 0 AND drs.route_id = dr.route_id
                  ORDER BY drs.start_recorder ASC
                  LIMIT 1
                )
              END
            )                    AS passengers,
            (
              max(dr.end_recorder) -
              CASE WHEN (
                          SELECT drp.end_recorder
                          FROM dispatch_registers AS drp
                          WHERE drp.date = dr.date AND drp.vehicle_id = v.id AND drp.end_recorder > 0 AND drp.id < dr.id
                          ORDER BY drp.id DESC
                          LIMIT 1
                        ) IS NULL
                THEN
                  dr.start_recorder
              ELSE
                (
                  SELECT drp.end_recorder
                  FROM dispatch_registers AS drp
                  WHERE drp.date = dr.date AND drp.vehicle_id = v.id AND drp.end_recorder > 0 AND drp.id < dr.id
                  ORDER BY drp.id DESC
                  LIMIT 1
                )
              END
            )                    AS passengers_round_trip
            
            FROM (dispatch_registers dr
            JOIN vehicles v ON ((v.id = dr.vehicle_id)))
            WHERE ((dr.status :: TEXT = 'Terminó' :: TEXT) OR (dr.status :: TEXT = 'En camino' :: TEXT))
            GROUP BY v.id, dr.id, dr.date, dr.route_id, dr.start_recorder
            ORDER BY v.id, dr.id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS recorder_counter_per_round_trips");
    }
}
