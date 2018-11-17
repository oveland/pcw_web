<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Users\AccessLog
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property string|null $user_id
 * @property-read \App\Models\Users\UserLog|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\AccessLog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\AccessLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\AccessLog whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\AccessLog whereUserId($value)
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
