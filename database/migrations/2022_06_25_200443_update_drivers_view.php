<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDriversView extends Migration
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
            CREATE VIEW drivers(id, code, identity, first_name, second_name, last_name, phone, cellphone, address, email, active, company_id, bea_id, db_id) AS
                SELECT conductor.id_idconductor::bigint AS id,
                conductor.codigo_interno AS code,
                conductor.identidad AS identity,
                conductor.nombre1 AS first_name,
                conductor.nombre2 AS second_name,
                (conductor.apellido1 || ' '::text) || conductor.apellido2 AS last_name,
                conductor.telefono AS phone,
                conductor.celular AS cellphone,
                conductor.direccion AS address,
                conductor.correo AS email,
                conductor.activo AS active,
                conductor.empresa AS company_id,
                conductor.bea_id::text AS bea_id,
                conductor.db_id
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
