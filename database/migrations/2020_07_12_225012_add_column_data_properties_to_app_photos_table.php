<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDataPropertiesToAppPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_photos', function (Blueprint $table) {
            $table->text('data_properties')->nullable();
        });

        Schema::table('app_current_photos', function (Blueprint $table) {
            $table->text('data_properties')->nullable();
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
            $table->dropColumn('data_properties');
        });

        Schema::table('app_current_photos', function (Blueprint $table) {
            $table->dropColumn('data_properties');
        });
    }
}
