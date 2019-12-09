<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTakenToBeaLiquidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bea_liquidations', function (Blueprint $table) {
            $table->boolean('taken')->default(false);
            $table->timestamp('taking_date')->nullable();
            $table->bigInteger('taking_user_id')->nullable();

            $table->foreign('taking_user_id')->references('id')->on('users');
        });

        DB::statement("alter table bea_liquidations drop constraint if exists bea_liquidations_user_id_foreign");
        DB::statement("alter table bea_liquidations add constraint bea_liquidations_user_id_foreign foreign key (user_id) references users");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bea_liquidations', function (Blueprint $table) {
            $table->dropColumn('taken');
            $table->dropColumn('taking_date');
            $table->dropColumn('taking_user_id');
        });
    }
}
