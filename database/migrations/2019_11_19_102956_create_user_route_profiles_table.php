<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRouteProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_route_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->bigInteger('route_id');
            $table->boolean('active')->default(true);
            $table->timestamps();

            /* table relations */
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //$table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');

            /*Indexes*/
            $table->unique(['user_id', 'route_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_route_profiles');
    }
}
