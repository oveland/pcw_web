<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\HistoryMarker
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereIdGps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereOrientacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereVelocidad($value)
 * @mixin \Eloquent
 * @property int|null $km_gps
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryMarker whereKmGps($value)
 */
class HistoryMarker extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    protected $table = 'markers_historial';
}
