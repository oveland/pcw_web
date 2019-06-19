<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaTrajectoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('bea_trajectories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('description',300)->nullable(true);
            $table->unsignedBigInteger('route_id');
            $table->timestamps();

            $table->foreign('route_id')->references('bea_id')->on('routes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bea_trajectories');
    }
}
