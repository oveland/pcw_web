<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsBeaIdType extends Migration
{
    protected $tables = ['bea_marks', 'vehicles', 'routes', 'bea_trajectories', 'bea_turns', 'routes', 'conductor'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $t) {
            if($t === 'conductor') {
                DB::statement("DROP VIEW IF EXISTS drivers");
            }
            Schema::table($t, function (Blueprint $table) {
                $table->string('bea_id', 256)->change();
            });

            if($t == 'conductor') {
                DB::statement("
                    create or replace view drivers(id, code, identity, first_name, second_name, last_name, phone, cellphone, address, email, active, company_id, bea_id) as
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
                        conductor.bea_id::text
                    FROM conductor
                ");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        foreach ($this->tables as $t) {
//            DB::statement("ALTER TABLE $t ALTER bea_id TYPE BIGINT USING bea_id::BIGINT");
//        }
    }
}
