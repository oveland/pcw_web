<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDadConfigDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dad_config_dispatches', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('dispatch_id');
            $table->string('type')->default('allow');
            $table->integer('order')->default(0);
            $table->string('description', 512);
            $table->string('pattern_type', 64)->default('vehicle_number');
            $table->text('pattern');
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dad_config_dispatches');
    }
}
