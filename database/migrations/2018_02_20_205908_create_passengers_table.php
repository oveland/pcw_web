<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePassengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date');
            $table->integer('total');
            $table->integer('total_prev');
            $table->integer('total_front_sensor')->default(0);
            $table->integer('total_back_sensor')->default(0);
            $table->integer('total_sensor_recorder')->default(0);
            $table->integer('total_platform');
            $table->bigInteger('vehicle_id')->unsigned();
            $table->integer('vehicle_status_id')->nullable();
            $table->bigInteger('dispatch_register_id')->unsigned()->nullable();
            $table->bigInteger('location_id')->unsigned()->nullable();
            $table->bigInteger('fringe_id')->unsigned()->nullable();
            $table->bigInteger('counter_issue_id')->unsigned()->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->string('frame',512);
            $table->timestamps();

            /* table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('counter_issue_id')->references('id')->on('counter_issues')->onDelete('cascade');
            $table->foreign('fringe_id')->references('id')->on('fringes')->onDelete('cascade');
            //$table->foreign('dispatch_register_id')->references('id')->on('dispatch_registers')->onDelete('cascade');
            //$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });

        DB::statement("
            CREATE OR REPLACE FUNCTION passengers_function()
              RETURNS TRIGGER
            LANGUAGE PLPGSQL
            AS $$
            DECLARE
            BEGIN
              IF (TG_OP = 'INSERT') THEN
                IF NEW.dispatch_register_id IS NOT NULL THEN
                  NEW.fringe_id = get_fringe_from_dispatch_time(NEW.dispatch_register_id,'current_time');
                END IF;
              END IF;
              RETURN NEW;
            END;
            $$;
        ");

        DB::statement("
            CREATE TRIGGER passengers_trigger BEFORE INSERT OR UPDATE
              ON passengers FOR EACH ROW
            EXECUTE PROCEDURE passengers_function();
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TRIGGER IF EXISTS passengers_trigger ON passengers");
        DB::statement("DROP FUNCTION IF EXISTS passengers_function()");
        Schema::dropIfExists('passengers');
    }
}
