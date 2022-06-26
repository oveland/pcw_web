<?php

namespace App\Models\Drivers;

use Carbon\Carbon;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Drivers\Driver
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $identity
 * @property string|null $first_name
 * @property string|null $second_name
 * @property string|null $last_name
 * @property bool|null $active
 * @property int $company_id
 * @property int $bea_id
 * @property int $db_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @mixin Eloquent
 * @method static Builder|Driver whereActive($value)
 * @method static Builder|Driver whereCode($value)
 * @method static Builder|Driver whereCompanyId($value)
 * @method static Builder|Driver whereFirstName($value)
 * @method static Builder|Driver whereId($value)
 * @method static Builder|Driver whereIdentity($value)
 * @method static Builder|Driver whereLastName($value)
 * @method static Builder|Driver whereSecondName($value)
 * @method static Builder|Driver withCode($code)
 * @property-read mixed $full_name
 * @method static Builder|Driver active()
 * @method static Builder|Driver newModelQuery()
 * @method static Builder|Driver newQuery()
 * @method static Builder|Driver query()
 * @method static Builder|Driver whereBeaId($value)
 * @property string|null $phone
 * @property string|null $cellphone
 * @property string|null $address
 * @property string|null $email
 * @method static Builder|Driver whereAddress($value)
 * @method static Builder|Driver whereCellphone($value)
 * @method static Builder|Driver whereEmail($value)
 * @method static Builder|Driver wherePhone($value)
 */
class Driver extends Model
{
    public function fullName()
    {
        return "$this->first_name $this->last_name" ?? "";
    }

    public function getFullNameAttribute()
    {
        return $this->fullName();
    }

    public function scopeWithCode($query, $code)
    {
        return $query->where('code', $code)->get()->first();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * @return bool|int|null
     */
    public function saveData()
    {
        $this->last_name = preg_replace('/[\x00-\x1F\x7F]/u', '', $this->last_name);
        $exploded = explode(" ", $this->last_name);

        list($lastName1, $lastName2) = count($exploded) > 1 ? $exploded : [$this->last_name, ''];
        if ($this->id) {
            $response = DB::update("
                UPDATE conductor 
                SET nombre1 = '" . utf8_encode($this->first_name) . "',
                    apellido1 = '$lastName1', 
                    apellido2 = '$lastName2',
                    identidad = '$this->identity',
                    empresa = '$this->company_id',
                    activo = 'TRUE',
                    bea_id = '$this->bea_id',
                    db_id = $this->db_id
                WHERE id_idconductor = $this->id 
            ");
        } else {
            $response = DB::insert("
                INSERT INTO conductor (nombre1, apellido1, apellido2, identidad, empresa, activo, bea_id, db_id) 
                VALUES ('" . utf8_encode($this->first_name) . "', '$lastName1', '$lastName2', '$this->identity', '$this->company_id', 'TRUE', '$this->bea_id', $this->db_id)
            ");
        }

        return $response;
    }

    function toArray()
    {
        return (object)[
            'id' => $this->id,
            'code' => $this->code,
            'identity' => $this->identity,
            'fullName' => $this->fullName(),
        ];
    }
}
