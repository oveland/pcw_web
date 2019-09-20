<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGpsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('description', 100)->nullable();
            $table->string('tags', 256)->nullable();
            $table->string('server_ip', 20)->default('53.38.73.219');
            $table->string('server_port', 5)->default('1000');
            $table->string('reset_command', 100)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            /*Indexes*/
            $table->unique(['name', 'server_port']); // One company has a unique name route
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gps_types');
    }
}
