<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFringesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('fringes');
        Schema::create('fringes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->time('from');
            $table->time('to');
            $table->integer('sequence');
            $table->boolean('active')->default(true);
            $table->bigInteger('route_id')->unsigned();
            $table->integer('day_type_id')->unsigned();
            $table->timestamps();

            /* table relations */
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->foreign('day_type_id')->references('id')->on('day_types');

            /*Indexes*/
            $table->unique(['route_id', 'day_type_id', 'from']); // A fringe of a route has a unique time 'from' in a specific day type
            $table->unique(['route_id', 'day_type_id', 'to']); // A fringe of a route has a unique time 'to' in a specific day type
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fringes');
    }
}
