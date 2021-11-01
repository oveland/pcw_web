<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatIndexPathToAppPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_photos', function (Blueprint $table) {
            DB::statement("CREATE INDEX app_photos_path_index ON app_photos (path DESC)");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_photos', function (Blueprint $table) {
            DB::statement("DROP INDEX app_photos_path_index");
        });
    }
}
