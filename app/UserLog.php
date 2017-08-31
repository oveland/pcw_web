<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $table = 'acceso';

    protected $primaryKey = 'id_usuario';
}
