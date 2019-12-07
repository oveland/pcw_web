<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNumberColumnToBeaMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bea_marks', function (Blueprint $table) {
            $table->integer('number')->nullable();
        });

        DB::statement("CREATE INDEX bea_marks_date_index ON bea_marks (date DESC)");

        DB::statement("
            CREATE OR REPLACE FUNCTION bea_marks_function() RETURNS TRIGGER
                LANGUAGE plpgsql
            AS $$
            DECLARE
                last_mark RECORD;
                turn RECORD;
            BEGIN
                IF (TG_OP = 'INSERT' ) THEN
                    SELECT * FROM bea_turns WHERE id = NEW.turn_id LIMIT 1 INTO turn;
            
                    IF turn IS NOT NULL THEN
                        SELECT *
                        FROM bea_marks
                        WHERE trajectory_id IS NOT NULL
                          AND date::date = NEW.date::date
                          AND turn_id IN (SELECT id FROM bea_turns WHERE vehicle_id = turn.vehicle_id)
                        ORDER BY id DESC
                        LIMIT 1
                        INTO last_mark;
            
                        IF last_mark IS NOT NULL THEN
                            NEW.number = last_mark.NUMBER + 1;
                        ELSE
                            NEW.number = 1;
                        END IF;
                    ELSE
                        NEW.number = 1;
                    END IF;
            
                END IF;
                RETURN NEW;
            END;
            $$
        ");

        DB::statement("
            CREATE TRIGGER bea_marks_trigger BEFORE INSERT
                ON bea_marks FOR EACH ROW
            EXECUTE PROCEDURE bea_marks_function()
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bea_marks', function (Blueprint $table) {
            $table->dropColumn('number');

            $table->dropIndex('bea_marks_date_index');
        });

        DB::statement("DROP TRIGGER IF EXISTS bea_marks_trigger ON bea_marks");
        DB::statement("DROP FUNCTION IF EXISTS bea_marks_function()");
    }
}
