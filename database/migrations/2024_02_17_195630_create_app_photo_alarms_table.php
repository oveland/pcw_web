<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppPhotoAlarmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_photo_alarms', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('date');
            $table->unsignedBigInteger('app_photo_id');
            $table->boolean('detected');
            $table->string('type');
            $table->json('data');

            $table->foreign('app_photo_id')->references('id')->on('app_photos')->onDelete('cascade');

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
        Schema::dropIfExists('app_photo_alarms');
    }
}
