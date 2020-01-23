<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCompanyIdToBeaDiscountTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bea_discount_types', function (Blueprint $table) {
            $table->bigInteger('company_id')->default(App\Models\Company\Company::COODETRANS);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unique(['company_id', 'uid']);
        });

        Schema::table('bea_mark_discount_types', function (Blueprint $table) {
            $table->bigInteger('company_id')->default(App\Models\Company\Company::COODETRANS);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bea_discount_types', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'uid']);
            $table->dropForeign(['company_id']);

            $table->dropColumn('company_id');
        });

        Schema::table('bea_mark_discount_types', function (Blueprint $table) {
            $table->dropForeign(['company_id']);

            $table->dropColumn('company_id');
        });
    }
}
