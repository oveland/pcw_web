<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotoEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();

        Schema::create('app_photo_events', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('date');
            $table->string('imei');
            $table->string('uid');
            $table->string('side');
            $table->boolean('taken');

            $table->timestamps();

            $table->index(['imei', 'uid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_photo_events');
    }
}
