<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnBeaDbIdToBeaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->integer('bea_db_id')->default(1);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('bea_db_id')->default(1);
        });

        Schema::table('conductor', function (Blueprint $table) {
            $table->integer('bea_db_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn('bea_db_id');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('bea_db_id');
        });

        Schema::table('conductor', function (Blueprint $table) {
            $table->dropColumn('bea_db_id');
        });
    }
}
