<?php

namespace App\Models\Routes;

use App\Mapping;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\RouteGoogle
 *
 * @property int $id_ruta
 * @property string|null $url
 * @property string|null $coordenadas
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\RouteGoogle whereCoordenadas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\RouteGoogle whereIdRuta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\RouteGoogle whereUrl($value)
 * @mixin \Eloquent
 */
class RouteGoogle extends Model
{
    use Mapping;

    protected $fillable = ['url', 'id_ruta'];

    protected $table = 'rutas_google_v3';
    protected $primaryKey = 'id_ruta';

    protected $mapping = [
        'id' => 'id_ruta',
        'url' => 'url'
    ];
}
