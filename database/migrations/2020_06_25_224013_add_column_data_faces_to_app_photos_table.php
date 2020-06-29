<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDataFacesToAppPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_photos', function (Blueprint $table) {
            $table->string('rekognition', 50)->default('persons');
            $table->text('data_persons')->nullable(true);
            $table->text('data_faces')->nullable(true);
        });

        Schema::table('app_current_photos', function (Blueprint $table) {
            $table->string('rekognition', 50)->default('persons');
            $table->text('data_persons')->nullable(true);
            $table->text('data_faces')->nullable(true);
        });

        DB::statement("UPDATE app_photos SET data_persons = data WHERE TRUE");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_photos', function (Blueprint $table) {
            $table->dropColumn('rekognition');
            $table->dropColumn('data_persons');
            $table->dropColumn('data_faces');
        });

        Schema::table('app_current_photos', function (Blueprint $table) {
            $table->dropColumn('rekognition');
            $table->dropColumn('data_persons');
            $table->dropColumn('data_faces');
        });
    }
}