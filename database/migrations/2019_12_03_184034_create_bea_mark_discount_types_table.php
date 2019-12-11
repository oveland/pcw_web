<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaMarkDiscountTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bea_mark_discount_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('description', 300);
            $table->string('icon', 50);
            $table->integer('default')->default(0);
            $table->integer('uid')->nullable();
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
        Schema::dropIfExists('bea_mark_discount_types');
    }
}
