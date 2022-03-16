<?php

namespace App\Models\System;

use App\Mapping;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\System\ViewPermission
 * @mixin Eloquent
 *
 * @property int $id
 * @property string $name
 * @property string $category
 * @property string $sub_category
 * @property string $url
 * @property integer $status
 * @property boolean $migrated
 * @method static Builder|ViewPermission migrated()
 */
class ViewPermission extends Model
{
    use Mapping;

    private $mapping = [
        'id' => 'id',
        'name' => 'nombre',
        'category' => 'categoria',
        'sub_category' => 'sub_categoria',
        'url' => 'url',
        'status' => 'estado',
        'migrated' => 'migrated',
    ];

    protected $table = 'permisos_user';

    function scopeMigrated(Builder $query)
    {
        return $query->where('migrated', true);
    }

    static function includes($path)
    {
        $user = \Auth::user();
        $permissionProfile = ViewPermission::migrated()->where('url', 'like', "%$path%")->get()->first();

        if (!$user || $user->isAdmin() || !$permissionProfile || $path == '/') return true;

        return $user->permissions->contains($permissionProfile->id) || $user->permissions->contains(777);
    }
}
