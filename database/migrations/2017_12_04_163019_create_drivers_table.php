<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        DB::statement("            
            CREATE OR REPLACE VIEW drivers AS
              SELECT
                id_idconductor::BIGINT          id,
                codigo_interno                  code,
                identidad                       \"identity\",
                apellido1 \"first_name\",
                apellido2 \"second_name\",
                nombre1 || ' ' || nombre2     last_name,
                activo::BOOLEAN                 active
              FROM conductor
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS drivers");
    }
}
