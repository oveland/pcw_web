<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleIssueTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_issue_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 32)->unique(true);
            $table->string('description', 128)->nullable();
            $table->boolean('active')->default(true);
            $table->string('css_class', 10)->default('default');

            $table->timestamps();

            /* table relations */

            /*Indexes*/

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_issue_types');
    }
}
