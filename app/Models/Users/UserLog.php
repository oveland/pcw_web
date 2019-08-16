<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Users\UserLog
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereCedula($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereClave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereCorreo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereCreado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereEstadoSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereIdEmpresa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereIdIdusuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereIdUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereMenureporte($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereModificado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereNivel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog wherePrimerApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog wherePrimerNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereSegundoApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereSegundoNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereUltimaActividad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog whereUsuario($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserLog query()
 */
class UserLog extends Model
{
    protected $table = 'acceso';

    protected $primaryKey = 'id_usuario';
}
