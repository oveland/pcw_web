<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeaGlobalCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bea_global_costs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 64);
            $table->string('description', 256)->nullable();
            $table->integer('value')->default(0);
            $table->string('concept', 128)->nullable();
            $table->integer('uid');
            $table->integer('priority')->nullable();
            $table->integer('company_id');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unique(['company_id', 'name']);
            $table->timestamps();
        });

        Schema::table('bea_management_costs', function (Blueprint $table) {
            $table->string('concept', 128)->nullable();
            $table->integer('priority')->nullable();
            $table->boolean('global')->default(false);
            $table->boolean('active')->default(true);
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
            $table->dropUnique(['vehicle_id', 'uid']);

            $table->dropColumn('active');
            $table->dropColumn('global');
            $table->dropColumn('priority');
            $table->dropColumn('concept');
        });

        Schema::dropIfExists('bea_global_costs');
    }
}
