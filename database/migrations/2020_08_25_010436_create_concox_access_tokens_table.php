<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConcoxAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concox_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('app_key', 32);
            $table->string('account', 32);
            $table->string('access_token', 32);
            $table->string('refresh_token', 32);
            $table->integer('expires_in');
            $table->timestamp('time');
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
        Schema::dropIfExists('concox_access_tokens');
    }
}
