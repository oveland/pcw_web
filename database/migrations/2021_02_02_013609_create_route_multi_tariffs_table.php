<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteMultiTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_multi_tariffs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('route_id');
            $table->integer('value');
            $table->integer('distance');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['route_id', 'distance']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_multi_tariffs');
    }
}
