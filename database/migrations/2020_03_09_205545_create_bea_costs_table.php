<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bea_costs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('uid');
            $table->string('name', 64);
            $table->string('description', 256)->nullable();
            $table->integer('value')->default(0);
            $table->string('concept', 128)->nullable();
            $table->integer('priority')->nullable();
            $table->integer('company_id');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unique(['company_id', 'name']);
            $table->timestamps();
        });

        Schema::table('bea_management_costs', function (Blueprint $table) {
            $table->string('concept', 128)->nullable();
            $table->unique(['vehicle_id', 'uid']);
        });


        DB::statement("UPDATE bea_management_costs SET uid = 0 WHERE uid = 1");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bea_management_costs', function (Blueprint $table) {
            $table->dropColumn('concept');
            $table->dropUnique(['vehicle_id', 'uid']);
        });

        Schema::dropIfExists('bea_costs');
    }
}
