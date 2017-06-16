<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plate')->unique();
            $table->string('number');
            $table->bigInteger('company_id')->unsigned()->default(6);
            $table->boolean('active')->default(true);
            $table->boolean('in_repair')->default(true);
            $table->timestamps();

            /* table relations */
            $table->foreign('company_id')->references('id')->on('companies');

            /*Indexes*/
            $table->index(['number', 'plate']); // One plate has a unique vehicle number
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
