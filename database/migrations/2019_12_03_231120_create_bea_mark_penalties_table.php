<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaMarkPenaltiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bea_mark_penalties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('route_id');
            $table->string('type', 100);
            $table->integer('value')->default(0);
            $table->unsignedBigInteger('mark_id');
            $table->timestamps();

            /* Table relations */
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->foreign('mark_id')->references('id')->on('bea_marks')->onDelete('cascade');

            /*Indexes*/
            $table->unique(['route_id', 'type', 'mark_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bea_mark_penalties');
    }
}
