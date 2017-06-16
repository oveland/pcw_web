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
            $table->increments('id');
            $table->string('name');
            $table->integer('distance')->default(0)->comment('Distance in meters');
            $table->integer('roadTime')->default(0)->comment('Road time in minutes');
            $table->integer('company_id')->unsigned()->default(6);
            $table->integer('dispatch_id')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();

            /* table relations */
            $table->foreign('company_id')->references('id')->on('companies');

            /*Indexes*/
            $table->index(['name', 'company_id']); // One company has a unique name route
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
