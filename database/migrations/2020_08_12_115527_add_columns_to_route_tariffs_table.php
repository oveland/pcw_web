<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToRouteTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('route_tariffs', function (Blueprint $table) {
            $table->integer('passenger')->default(0);
            $table->double('fuel')->default(0);
            $table->dropColumn('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('route_tariffs', function (Blueprint $table) {
            $table->integer('value')->default(0);
            $table->dropColumn('fuel');
            $table->dropColumn('passenger');
        });
    }
}
