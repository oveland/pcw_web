<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEffectsColumnToAppPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_photos', function (Blueprint $table) {
            $table->string('effects',512)->nullable(true);
        });

        Schema::table('app_current_photos', function (Blueprint $table) {
            $table->string('effects',512)->nullable(true);
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
            $table->dropColumn('effects');
        });

        Schema::table('app_current_photos', function (Blueprint $table) {
            $table->dropColumn('effects');
        });
    }
}
