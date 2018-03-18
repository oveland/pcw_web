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
              SELECT (conductor.id_idconductor)::BIGINT AS id,
                conductor.codigo_interno AS code,
                conductor.identidad AS identity,
                conductor.apellido1 AS first_name,
                conductor.apellido2 AS second_name,
                ((conductor.nombre1 || ' '::text) || conductor.nombre2) AS last_name,
                conductor.activo AS active,
                conductor.empresa AS company_id
               FROM conductor;
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
