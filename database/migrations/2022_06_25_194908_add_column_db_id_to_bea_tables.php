<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDbIdToBeaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->integer('db_id')->default(1);
            $table->dropIndex('routes_company_id_bea_id_uindex');
            $table->unique(['company_id', 'bea_id', 'db_id']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('db_id')->default(1);
            $table->dropIndex('vehicles_company_id_bea_id_uindex');
            $table->unique(['company_id', 'bea_id', 'db_id']);

            $table->dropUnique(['plate']);
            $table->unique(['plate', 'db_id']);
        });

        Schema::table('conductor', function (Blueprint $table) {
            $table->integer('db_id')->default(1);
            $table->dropIndex('conductor_empresa_bea_id_uindex');
            $table->unique(['empresa', 'bea_id', 'db_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'bea_id', 'db_id']);
//            $table->unique(['company_id', 'bea_id'], 'routes_company_id_bea_id_uindex');
            DB::statement('CREATE UNIQUE INDEX routes_company_id_bea_id_uindex ON vehicles (company_id, bea_id)');
            $table->dropColumn('db_id');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropUnique(['plate', 'db_id']);
            $table->unique(['plate']);

            $table->dropUnique(['company_id', 'bea_id', 'db_id']);
//            $table->unique(['company_id', 'bea_id'], 'vehicles_company_id_bea_id_uindex');
            DB::statement('CREATE UNIQUE INDEX vehicles_company_id_bea_id_uindex ON vehicles (company_id, bea_id)');
            $table->dropColumn('db_id');
        });

        Schema::table('conductor', function (Blueprint $table) {
            $table->dropUnique(['empresa', 'bea_id', 'db_id']);
//            $table->unique(['empresa', 'bea_id'], 'conductor_empresa_bea_id_uindex');
            DB::statement('CREATE UNIQUE INDEX conductor_empresa_bea_id_uindex ON conductor (empresa, bea_id)');
            $table->dropColumn('db_id');
        });
    }
}
