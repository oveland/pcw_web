<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bea_id')->unique()->nullable(true);
            $table->string('name');
            $table->integer('distance')->default(0)->comment('Distance in meters');
            $table->integer('road_time')->default(0)->comment('Road time in minutes');
            $table->string("url");
            $table->bigInteger('company_id')->unsigned()->default(6);
            $table->integer('dispatch_id')->default(0);
            $table->boolean('active')->default(true);
            $table->boolean('as_group')->default(false);
            $table->time('min_route_time')->default('01:00:00');
            $table->unsignedBigInteger('route_id')->nullable(true);
            $table->timestamps();

            /* Table relations */
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            /*Indexes*/
            $table->unique(['name', 'company_id']); // One company has a unique name route
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routes');
    }
}
