<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleBinnacleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_binnacle_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid');
            $table->string('name', 32)->unique(true);
            $table->string('description', 128)->nullable();
            $table->boolean('active')->default(true);
            $table->string('css_class', 10)->default('default');
            $table->string('icon', 50)->default('icon-wrench');
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
        Schema::dropIfExists('vehicle_binnacle_types');
    }
}
