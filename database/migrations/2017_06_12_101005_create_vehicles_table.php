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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bea_id')->unique();
            $table->string('plate')->unique();
            $table->string('number');
            $table->bigInteger('company_id')->unsigned()->default(6);
            $table->boolean('active')->default(true);
            $table->boolean('in_repair')->default(true);
            $table->unsignedBigInteger('bea_id')->nullable(true);
            $table->timestamps();

            /* Table relations */
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            /*Indexes*/
            $table->unique(['number', 'plate']); // One plate has a unique vehicle number
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
