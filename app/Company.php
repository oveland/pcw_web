<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'empresa';

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
