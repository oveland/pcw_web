<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\UserLog
 *
 * @property string $usuario
 * @property string|null $clave
 * @property int $nivel
 * @property int $id_usuario
 * @property string|null $nombre
 * @property int|null $id_empresa
 * @property string|null $correo
 * @property string|null $menureporte
 * @property string|null $primer_nombre
 * @property string|null $segundo_nombre
 * @property string|null $primer_apellido
 * @property string|null $segundo_apellido
 * @property int|null $cedula
 * @property bool|null $estado
 * @property int $id_idusuario
 * @property string|null $creado
 * @property string|null $modificado
 * @property string|null $cargo
 * @property string|null $foto
 * @property int|null $estado_session
 * @property string|null $ultima_actividad
 * @property string|null $observaciones
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereCedula($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereClave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereCorreo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereCreado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereEstadoSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereIdEmpresa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereIdIdusuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereIdUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereMenureporte($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereModificado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereNivel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog wherePrimerApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog wherePrimerNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereSegundoApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereSegundoNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereUltimaActividad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserLog whereUsuario($value)
 * @mixin \Eloquent
 */
class UserLog extends Model
{
    protected $table = 'acceso';

    protected $primaryKey = 'id_usuario';
}
