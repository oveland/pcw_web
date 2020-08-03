<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTakenToRouteTakingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('route_takings', function (Blueprint $table) {
            $table->boolean('taken')->default(false);
        });

        DB::statement("UPDATE route_takings SET taken = TRUE WHERE total_production > 0 AND net_production > 0");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('route_takings', function (Blueprint $table) {
            $table->dropColumn('taken');
        });
    }
}
