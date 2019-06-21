<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaPenaltiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bea_penalties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('route_id');
            $table->string('type', 100);
            $table->integer('value')->default(0);
            $table->timestamps();

            /* Table relations */
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');

            /*Indexes*/
            $table->unique(['route_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bea_penalties');
    }
}
