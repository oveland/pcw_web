<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AccessLog
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property string|null $user_id
 * @property-read \App\UserLog|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccessLog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccessLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccessLog whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccessLog whereUserId($value)
 * @mixin \Eloquent
 */
class AccessLog extends Model
{
    protected $table = 'acceso_historial';

    public function user()
    {
        return $this->belongsTo(UserLog::class,'user_id','id_usuario');
    }
}
