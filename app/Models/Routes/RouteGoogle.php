<?php

namespace App\Models\Routes;

use App\Mapping;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\RouteGoogle
 *
 * @property int $id_ruta
 * @property string|null $url
 * @property string|null $coordenadas
 * @method static Builder|RouteGoogle whereCoordenadas($value)
 * @method static Builder|RouteGoogle whereIdRuta($value)
 * @method static Builder|RouteGoogle whereUrl($value)
 * @mixin Eloquent
 * @method static Builder|RouteGoogle newModelQuery()
 * @method static Builder|RouteGoogle newQuery()
 * @method static Builder|RouteGoogle query()
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $file_name
 * @method static Builder|RouteGoogle whereCreatedAt($value)
 * @method static Builder|RouteGoogle whereFileName($value)
 * @method static Builder|RouteGoogle whereUpdatedAt($value)
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
