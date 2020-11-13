<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRequiredToDiscountTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bea_discount_types', function (Blueprint $table) {
            $table->boolean('required')->default(true);
            $table->boolean('optional')->default(false);
        });

        Schema::table('bea_discounts', function (Blueprint $table) {
            $table->boolean('required')->default(true);
            $table->boolean('optional')->default(false);
        });

        Schema::table('bea_mark_discount_types', function (Blueprint $table) {
            $table->boolean('required')->default(true);
            $table->boolean('optional')->default(false);
        });

        Schema::table('bea_mark_discounts', function (Blueprint $table) {
            $table->boolean('required')->default(true);
            $table->boolean('optional')->default(false);
        });

        DB::statement("UPDATE bea_discount_types SET required = FALSE, optional = TRUE WHERE uid = 5");
        DB::statement("UPDATE bea_discounts SET required = (SELECT required FROM bea_discount_types WHERE id = discount_type_id), optional = (SELECT optional FROM bea_discount_types WHERE id = discount_type_id)");
        DB::statement("
            UPDATE bea_mark_discounts 
            SET required = (
                SELECT required FROM bea_discount_types 
                WHERE uid = (
                    SELECT uid 
                    FROM bea_mark_discount_types 
                    WHERE id = discount_type_id) 
                    AND company_id = (
                        SELECT company_id 
                        FROM bea_mark_discount_types 
                        WHERE id = discount_type_id
                    )
                ),
                optional = (
                SELECT optional FROM bea_discount_types 
                WHERE uid = (
                    SELECT uid 
                    FROM bea_mark_discount_types 
                    WHERE id = discount_type_id) 
                    AND company_id = (
                        SELECT company_id 
                        FROM bea_mark_discount_types 
                        WHERE id = discount_type_id
                    )
                )
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bea_discount_types', function (Blueprint $table) {
            $table->dropColumn('required');
            $table->dropColumn('optional');
        });

        Schema::table('bea_discounts', function (Blueprint $table) {
            $table->dropColumn('required');
            $table->dropColumn('optional');
        });

        Schema::table('bea_mark_discount_types', function (Blueprint $table) {
            $table->dropColumn('required');
            $table->dropColumn('optional');
        });

        Schema::table('bea_mark_discounts', function (Blueprint $table) {
            $table->dropColumn('required');
            $table->dropColumn('optional');
        });
    }
}
