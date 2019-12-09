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
    }
}
