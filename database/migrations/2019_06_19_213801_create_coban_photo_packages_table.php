<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCobanPhotoPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coban_photo_packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('photo_id');
            $table->integer('package_id');
            $table->integer('package_length');
            $table->text('data');
            $table->timestamps();

            /* Table relations */
            $table->foreign('photo_id')->references('id')->on('coban_photos')->onDelete('cascade');

            /* Indexes */
            $table->unique(['photo_id', 'package_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coban_photo_packages');
    }
}
