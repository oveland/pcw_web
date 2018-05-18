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
            CREATE OR REPLACE VIEW proprietaries AS
              SELECT
                id_idpropietario id,
                p_primer_nombre first_name,
                p_segundo_nombre second_name,
                p_primer_apellido surname,
                p_segundo_apellido second_surname,    
                p_tel phone,
                p_cel cellphone,
                p_direccion address,
                p_correo email,
                activo active,
                passenger_report_via_sms,
                id_empresa company_id
              FROM propietario
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
