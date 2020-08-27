<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnUidToAppPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_photos', function (Blueprint $table) {
            $table->unsignedBigInteger('uid')->nullable()->unique();
        });

        Schema::table('app_current_photos', function (Blueprint $table) {
            $table->unsignedBigInteger('uid')->nullable()->unique();
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
            $table->dropColumn('uid');
        });

        Schema::table('app_current_photos', function (Blueprint $table) {
            $table->dropColumn('uid');
        });
    }
}
