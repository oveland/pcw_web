<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto-dispatcher', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->double('latitude')->defult(0);
            $table->double('longitude')->defult(0);
            $table->bigInteger('company_id')->unsigned()->default(6);
            $table->boolean('active')->default(true);
            $table->timestamps();

            /* table relations */
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
        Schema::dropIfExists('auto-dispatcher');
    }
}
