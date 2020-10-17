<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppPhotoRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_photo_requests', function (Blueprint $table) {
            $table->id();

            $table->timestamp('date')->default(Carbon::now());
            $table->bigInteger('vehicle_id');
            $table->string('type', 40);
            $table->string('params', 512)->nullable();

            $table->unique('vehicle_id');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');

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
        Schema::dropIfExists('app_photo_requests');
    }
}
