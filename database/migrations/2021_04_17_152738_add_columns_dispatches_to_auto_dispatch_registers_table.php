<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsDispatchesToAutoDispatchRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auto_dispatch_registers', function (Blueprint $table) {
            $table->unsignedInteger('origin_dispatch_id')->nullable();
            $table->unsignedInteger('destiny_dispatch_id')->nullable();

            $table->timestamp('event_date')->nullable();

            $table->foreign('origin_dispatch_id')->references('id')->on('dispatches')->onDelete('cascade');
            $table->foreign('destiny_dispatch_id')->references('id')->on('dispatches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auto_dispatch_registers', function (Blueprint $table) {
            $table->dropColumn('event_date');
            $table->dropColumn('destiny_dispatch_id');
            $table->dropColumn('origin_dispatch_id');
        });
    }
}
