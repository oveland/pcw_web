<?php

namespace App\Models\Reports\Activity;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Reports\Activity\IgnoreUrl
 *
 * @mixin Eloquent
 * @property int $id
 * @property string $url
 * @property string|null $type
 * @method static Builder|IgnoreUrl whereId($value)
 * @method static Builder|IgnoreUrl whereType($value)
 * @method static Builder|IgnoreUrl whereUrl($value)
 */
class IgnoreUrl extends Model
{
    protected $table = 'activity_ignore_urls';
}
