<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppPhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_photos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date');
            $table->integer('vehicle_id');
            $table->unsignedBigInteger('dispatch_register_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('path', 64);
            $table->string('side', 20);
            $table->string('type', 20);
            $table->string('data',255)->nullable();
            $table->timestamps();

            $table->index('vehicle_id');
            $table->index(['dispatch_register_id']);
            $table->index(['location_id']);

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });

        Schema::create('app_current_photos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date');
            $table->integer('vehicle_id');
            $table->unsignedBigInteger('dispatch_register_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('path', 64);
            $table->string('side', 20);
            $table->string('type', 20);
            $table->string('data',255)->nullable();
            $table->timestamps();

            $table->unique('vehicle_id');

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_current_photos');
        Schema::dropIfExists('app_photos');
    }
}
