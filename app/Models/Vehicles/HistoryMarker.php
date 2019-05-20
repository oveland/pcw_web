<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\HistoryMarker
 *
 * @property int $id
 * @property string|null $fecha
 * @property string|null $hora
 * @property float|null $lat
 * @property float|null $lng
 * @property string|null $id_gps
 * @property int|null $km
 * @property float|null $velocidad
 * @property float|null $orientacion
 * @property int|null $estado
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereIdGps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereOrientacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereVelocidad($value)
 * @mixin \Eloquent
 * @property int|null $km_gps
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereKmGps($value)
 * @property string|null $frame
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker whereFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\HistoryMarker query()
 */
class HistoryMarker extends Model
{
    function getDateFormat()
    {
        return config('app.date_time_format');
    }

    protected $table = 'markers_historial';
}
