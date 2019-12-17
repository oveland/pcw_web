<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaMarksTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bea_marks', function(Blueprint $table){
            $table->boolean('duplicated')->default(false);
            $table->bigInteger('duplicated_mark_id')->nullable();
        });

        DB::statement("
            CREATE OR REPLACE FUNCTION bea_marks_function() RETURNS TRIGGER
                LANGUAGE plpgsql
            AS
            $$
            DECLARE
                duplicated_id INTEGER;
            BEGIN
                SELECT id
                FROM bea_marks
                WHERE date = NEW.date
                  AND turn_id = NEW.turn_id
                  AND total_bea = NEW.total_bea
                  AND im_bea_max = NEW.im_bea_max
                  AND im_bea_min = NEW.im_bea_min
                  AND passengers_bea = NEW.passengers_bea
                  AND final_time > NEW.initial_time
                  AND trajectory_id IS NOT NULL
                  AND id <> NEW.id
                  ORDER BY final_time DESC
                LIMIT 1
                INTO duplicated_id;
            
                IF duplicated_id IS NOT NULL THEN
                    NEW.duplicated = TRUE;
                    NEW.duplicated_mark_id = duplicated_id;
                    NEW.trajectory_id = NULL;
                ELSE
                    NEW.duplicated = FALSE;
                    NEW.duplicated_mark_id = NULL;
                END IF;
            
                RETURN NEW;
            END;
            $$
        ");

        DB::statement("
            CREATE TRIGGER bea_marks_trigger
                BEFORE INSERT OR UPDATE
                ON bea_marks
                FOR EACH ROW
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
        DB::statement("DROP TRIGGER IF EXISTS bea_marks_trigger ON bea_marks");
        DB::statement("DROP FUNCTION IF EXISTS bea_marks_function()");

        Schema::table('bea_marks', function(Blueprint $table){
            $table->dropColumn('duplicated_mark_id');
            $table->dropColumn('duplicated');
        });
    }
}
