<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\SimGPS
 *
 * @property int $id
 * @property string $sim
 * @property string $operator
 * @property string $gps_type
 * @property int $vehicle_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS findByVehicleId($vehicle_id)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS findBySim($sim)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereGpsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereSim($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\SimGPS whereVehicleId($value)
 * @mixin \Eloquent
 */
class SimGPS extends Model
{
    /* Info operators */
    const CLARO = 'claro';
    const MOVISTAR = 'movistar';
    const AVANTEL = 'avantel';

    const OPERATORS = [
        self::CLARO,
        self::MOVISTAR,
        self::AVANTEL
    ];

    const OPERATORS_COLOR = [
        self::CLARO => 'danger',
        self::MOVISTAR => 'info',
        self::AVANTEL => 'purple'
    ];

    /* Info GPS types */
    const SKYPATROL = 'SKYPATROL';
    const SKYPATROL_OLD = 'SKYPATROL-OLD';
    const COBAN = 'COBAN';
    const RUPTELA = 'RUPTELA';
    const MEITRACK = 'MEITRACK';
    const ANTARES = 'ANTARES';
    const ANDROID = 'ANDROID';

    const RESET_COMMAND = [
        self::SKYPATROL => 'AT$RESET',
        self::SKYPATROL_OLD => '>RSP=T;ID=IMEI;AT$AT$RESET<',
        self::COBAN => 'reset123456',
        self::RUPTELA => 'reset',
        self::MEITRACK => 'reset',
        self::ANTARES => 'reset',
        self::ANDROID => 'reset',
    ];

    const GPS_COLOR = [
        self::SKYPATROL => 'primary',
        self::SKYPATROL_OLD => 'primary',
        self::COBAN => 'warning',
        self::RUPTELA => 'purple',
        self::MEITRACK => 'success',
        self::ANTARES => 'info',
        self::ANDROID => 'success',
    ];

    const DEVICES = [self::SKYPATROL, self::SKYPATROL_OLD, self::COBAN, self::RUPTELA, self::MEITRACK, self::ANTARES, self::ANDROID];

    protected $table = 'sim_gps';

    protected $fillable = ['sim', 'operator', 'gps_type', 'vehicle_id', 'active'];

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    public function scopeFindByVehicleId($query, $vehicle_id)
    {
        return $query->where('vehicle_id', $vehicle_id)->where('active', true);
    }

    public function scopeFindBySim($query, $sim)
    {
        return $query->where('sim', $sim)->where('active', true)->get()->first();
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gps()
    {
        return $this->belongsTo(GpsVehicle::class, 'vehicle_id', 'vehicle_id');
    }

    public function isSkypatrol()
    {
        return $this->gps_type == self::SKYPATROL;
    }

    public function isCoban()
    {
        return $this->gps_type == self::COBAN;
    }

    public function isRuptela()
    {
        return $this->gps_type == self::RUPTELA;
    }

    public function getGPSTypeCssColor()
    {
        return self::GPS_COLOR[$this->gps_type];
    }

    public function getOperatorCssColor()
    {
        return self::OPERATORS_COLOR[$this->operator];
    }

    public function getResetCommand()
    {
        return self::RESET_COMMAND[$this->gps_type];
    }

    public function setSimAttribute($sim)
    {
        $prefix = intval(substr($sim, 0, 3));

        if ($prefix >= 350) $operator = self::AVANTEL;
        else if (($prefix >= 320 && $prefix < 350) || ($prefix >= 310 && $prefix < 315)) $operator = self::CLARO;
        else $operator = self::MOVISTAR;

        $this->attributes['sim'] = $sim;
        $this->attributes['operator'] = $operator;
    }

    public function getUrlImageOperator()
    {
        if ($this->operator == self::MOVISTAR) {
            $urlImage = "https://cdn.iconverticons.com/files/png/1a712eaf7266f623_256x256.png";
            $width = "15px";
        } else if ($this->operator == self::AVANTEL) {
            $urlImage = "https://static-s.aa-cdn.net/img/gp/20600003166342/9dHdvx-VIURI3nv_XqIiGitDqIOCZ0ZlvFcISfYSF7EnoqM1VjC78aujcafZwocwgA=w300";
            $width = "20px";
        } else if ($this->operator == self::CLARO) {
            $urlImage = "https://vignette.wikia.nocookie.net/telefono/images/6/6f/Claro.png/revision/latest?cb=20131231034644&path-prefix=es";
            $width = "20px";
        }

        return "<img src=\"$urlImage\" width=\"$width\">";
    }
}
