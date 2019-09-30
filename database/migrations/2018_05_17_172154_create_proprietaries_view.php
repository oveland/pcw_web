<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProprietariesView extends Migration
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
            CREATE OR REPLACE VIEW proprietaries(id,identity, first_name, second_name, surname, second_surname, phone, cellphone, address, email, active, passenger_report_via_sms, company_id) AS
                SELECT propietario.id_idpropietario   AS id,
                       propietario.id_propietario     AS identity,
                       propietario.p_primer_nombre    AS first_name,
                       propietario.p_segundo_nombre   AS second_name,
                       propietario.p_primer_apellido  AS surname,
                       propietario.p_segundo_apellido AS second_surname,
                       propietario.p_tel              AS phone,
                       propietario.p_cel              AS cellphone,
                       propietario.p_direccion        AS address,
                       propietario.p_correo           AS email,
                       propietario.activo             AS active,
                       propietario.passenger_report_via_sms,
                       propietario.id_empresa         AS company_id
                FROM propietario;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS proprietaries");
    }
}
