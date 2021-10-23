<?php

use App\Models\LM\Mark;
use App\Models\LM\Trajectory;
use App\Models\LM\Turn;
use App\Models\Company\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnBeaIdToBeaMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //********* bea turns *******************

        Schema::table('bea_marks', function (Blueprint $table) {
            $table->unsignedBigInteger('bea_id')->nullable();
            $table->unsignedBigInteger('company_id')->default(Company::COODETRANS);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        $marks = Mark::all();
        foreach ($marks as $mark){
            $mark->bea_id = $mark->id;
            $mark->save();
        }

        Schema::table('bea_marks', function (Blueprint $table) {
            $table->index('bea_id');
            $table->index('company_id');
            $table->unique(['company_id', 'bea_id']);
        });


        //********* bea turns *******************

        Schema::table('bea_turns', function (Blueprint $table) {
            $table->unsignedBigInteger('bea_id')->nullable();
            $table->unsignedBigInteger('company_id')->default(Company::COODETRANS);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        $turns = Turn::all();
        foreach ($turns as $turn){
            $turn->bea_id = $turn->id;
            $turn->save();
        }

        Schema::table('bea_turns', function (Blueprint $table) {
            $table->index('bea_id');
            $table->index('company_id');
            $table->unique(['company_id', 'bea_id']);
        });

        //********* bea trajectories *******************

        Schema::table('bea_trajectories', function (Blueprint $table) {
            $table->unsignedBigInteger('bea_id')->nullable();
            $table->unsignedBigInteger('company_id')->default(Company::COODETRANS);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        $trajectories = Trajectory::all();
        foreach ($trajectories as $trajectory){
            $trajectory->bea_id = $trajectory->id;
            $trajectory->save();
        }

        Schema::table('bea_trajectories', function (Blueprint $table) {
            $table->index('bea_id');
            $table->index('company_id');
            $table->unique(['company_id', 'bea_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bea_marks', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'bea_id']);
            $table->dropIndex(['company_id']);
            $table->dropIndex(['bea_id']);

            $table->dropColumn('company_id');
            $table->dropColumn('bea_id');
        });

        Schema::table('bea_turns', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'bea_id']);
            $table->dropIndex(['company_id']);
            $table->dropIndex(['bea_id']);

            $table->dropColumn('company_id');
            $table->dropColumn('bea_id');
        });

        Schema::table('bea_trajectories', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'bea_id']);
            $table->dropIndex(['company_id']);
            $table->dropIndex(['bea_id']);

            $table->dropColumn('company_id');
            $table->dropColumn('bea_id');
        });
    }
}
