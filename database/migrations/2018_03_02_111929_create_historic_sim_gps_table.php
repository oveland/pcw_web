<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricSimGpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historic_sim_gps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sim');
            $table->string('operator');
            $table->string('gps_type');
            $table->bigInteger('vehicle_id');
            $table->boolean('active')->default(true);
            $table->timestamps();

            /* table relations */
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });

        DB::statement("
            CREATE OR REPLACE FUNCTION sim_gps_function()
              RETURNS TRIGGER
            LANGUAGE plpgsql
            AS $$
            DECLARE
            BEGIN
              IF TG_OP = 'UPDATE'
              THEN
                NEW.updated_at = current_timestamp;
                INSERT INTO historic_sim_gps VALUES (
                  DEFAULT, NEW.sim, NEW.operator, NEW.gps_type, NEW.vehicle_id, NEW.active, NEW.created_at, NEW.updated_at
                );
              END IF;
            
              RETURN NEW;
            END;
            $$;
        ");

        DB::statement("
            CREATE TRIGGER sim_gps_trigger
              BEFORE INSERT OR UPDATE
              ON sim_gps
              FOR EACH ROW EXECUTE PROCEDURE sim_gps_function();
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TRIGGER sim_gps_trigger ON sim_gps");
        DB::statement("DROP FUNCTION IF EXISTS sim_gps_function()");
        Schema::dropIfExists('historic_sim_gps');
    }
}
