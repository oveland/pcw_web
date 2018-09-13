<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFringesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fringes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->time('from');
            $table->time('to');
            $table->integer('sequence');
            $table->boolean('active')->default(true);
            $table->bigInteger('route_id')->unsigned();
            $table->integer('day_type_id')->unsigned();
            $table->string('style_color')->nullable();
            $table->timestamps();

            /* table relations */
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->foreign('day_type_id')->references('id')->on('day_types');

            /*Indexes*/
            $table->unique(['route_id', 'day_type_id', 'from']); // A fringe of a route has a unique time 'from' in a specific day type
            $table->unique(['route_id', 'day_type_id', 'to']); // A fringe of a route has a unique time 'to' in a specific day type
        });

        DB::statement("
            create or replace function get_fringe_from_dispatch_time(time_in time without time zone, route_id_in bigint, day_type_id_in integer ) returns bigint
            language plpgsql
            as $$
            DECLARE
              fringe_id BIGINT;
            BEGIN
              SELECT f.id 
              FROM fringes AS f
              WHERE f.route_id = route_id_in
                AND f.\"from\" <= time_in
                AND f.day_type_id = day_type_id_in
              ORDER BY f.\"from\" DESC
              LIMIT 1 INTO fringe_id;
            
              IF fringe_id IS NULL
              THEN
                SELECT f.id
                FROM fringes AS f
                WHERE f.route_id = route_id_in
                  AND f.\"to\" >= time_in
                  AND f.day_type_id = day_type_id_in
                ORDER BY f.\"to\" ASC
                LIMIT 1 INTO fringe_id;
              END IF;
              RETURN fringe_id;
            END;
            $$;
        ");

        DB::statement("
            create or replace function get_fringe_from_dispatch_time(dispatch_register_id bigint, from_time character varying ) returns bigint
            language plpgsql
            as $$
            DECLARE
              fringe_id BIGINT;
              dispatch_register RECORD;
              search_time TIME WITHOUT TIME ZONE;
            BEGIN
              SELECT * FROM dispatch_registers WHERE id = dispatch_register_id LIMIT 1 INTO dispatch_register;
            
              IF from_time = 'current_time' THEN
                search_time := current_time;
              ELSEIF (from_time = 'arrival' AND dispatch_register.status = 'Terminó') THEN
                search_time := dispatch_register.arrival_time;
              ELSE
                search_time := dispatch_register.departure_time;
              END IF;
            
              SELECT get_fringe_from_dispatch_time(search_time,dispatch_register.route_id,dispatch_register.type_of_day) INTO fringe_id;
              RETURN fringe_id;
            END;
            $$
            ;
        ");

        DB::statement("
            create or replace function get_fringe_from_dispatch_time(dispatch_register_id bigint) returns bigint
            language plpgsql
            as $$
            DECLARE
              fringe_id BIGINT;
            BEGIN
              SELECT get_fringe_from_dispatch_time(dispatch_register_id,'departure') INTO fringe_id;
              RETURN fringe_id;
            END;
            $$
            ;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP FUNCTION IF EXISTS get_fringe_from_dispatch_time(bigint)");
        DB::statement("DROP FUNCTION IF EXISTS get_fringe_from_dispatch_time(bigint, character varying)");
        DB::statement("DROP FUNCTION IF EXISTS get_fringe_from_dispatch_time(time without time zone, bigint, integer)");

        Schema::dropIfExists('fringes');
    }
}
