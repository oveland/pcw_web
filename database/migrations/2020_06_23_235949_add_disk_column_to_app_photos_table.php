<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiskColumnToAppPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_photos', function (Blueprint $table) {
            $table->string('disk',20)->default('local');
        });

        Schema::table('app_current_photos', function (Blueprint $table) {
            $table->string('disk',20)->default('local');
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
            $table->dropColumn('disk');
        });

        Schema::table('app_current_photos', function (Blueprint $table) {
            $table->dropColumn('disk');
        });
    }
}
