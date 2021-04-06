<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('route_name', 512)->nullable();
            $table->string('category1', 100)->nullable();
            $table->string('category2', 100)->nullable();
            $table->string('category3', 1024)->nullable();
            $table->string('url', 512);
            $table->string('params', 1024)->nullable();
            $table->string('method', 20)->nullable();
            $table->string('agent', 512)->nullable();

            $table->unsignedInteger('user_id')->nullable();

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
        Schema::dropIfExists('activity_logs');
    }
}
