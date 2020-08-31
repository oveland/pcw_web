<?php

namespace App\Models\Apps\Concox;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Apps\Concox\AccessToken
 *
 * @method static Builder|AccessToken newModelQuery()
 * @method static Builder|AccessToken newQuery()
 * @method static Builder|AccessToken query()
 * @mixin Eloquent
 * @property int $id
 * @property string $app_key
 * @property string $account
 * @property string $access_token
 * @property string $refresh_token
 * @property int $expires_in
 * @property string | Carbon $time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|AccessToken whereAccessToken($value)
 * @method static Builder|AccessToken whereAccount($value)
 * @method static Builder|AccessToken whereAppKey($value)
 * @method static Builder|AccessToken whereCreatedAt($value)
 * @method static Builder|AccessToken whereExpiresIn($value)
 * @method static Builder|AccessToken whereId($value)
 * @method static Builder|AccessToken whereRefreshToken($value)
 * @method static Builder|AccessToken whereTime($value)
 * @method static Builder|AccessToken whereUpdatedAt($value)
 * @property string $time
 */
class AccessToken extends Model
{
    protected $table = 'concox_access_tokens';

    protected $fillable = ['app_key', 'account', 'access_token', 'refresh_token', 'expires_in', 'time'];

    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function getTimeAttribute($time)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), $time)->subHours(5);
    }

    /**
     * @param array $data
     */
    public function fillCamelCase($data = [])
    {
        $data = (object)$data;

        $this->app_key = $data->appKey;
        $this->account = $data->account;
        $this->access_token = $data->accessToken;
        $this->refresh_token = $data->refreshToken;
        $this->expires_in = $data->expiresIn;
        $this->time = Carbon::createFromFormat('Y-m-d H:i:s', $data->time)->setTimezone('America/Bogota');
    }

    public function isAlive()
    {
        $now = Carbon::now();
        $diffInSeconds = $now->diffInSeconds($this->time);
        $isAlive = $diffInSeconds < $this->expires_in;
        return $isAlive;
    }

    public function isAboutToBeInvalid()
    {
        $now = Carbon::now();
        $diffInSeconds = $now->diffInSeconds($this->time);
        return $this->isAlive() && $diffInSeconds > ($this->expires_in - 10);
    }


    /**
     * @param array $data
     * @return AccessToken|Collection|Model|null
     */
    public static function findOrCreate($data = [])
    {
        $data = (object)$data;
        $accessToken = self::where('account', $data->account)->first();

        if (!$accessToken) {
            $accessToken = new self();
        }

        $accessToken->fillCamelCase($data);
        return $accessToken;
    }

    function scopeWhereAccount(Builder $query, $account)
    {
        return $query->where('account', $account);
    }
}
