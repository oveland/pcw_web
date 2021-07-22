<?php

namespace App\Services\Apps\Concox;

use App\Models\Apps\Concox\AccessToken;
use App\Services\API\Apps\Contracts\APIFilesInterface;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AuthService
{
    private const API_URL = 'http://open.10000track.com/route/rest';
    private const APP_KEY = '8FB345B8693CCD00E573E7459324059A';
    private const APP_SECRET = 'be8fe6b5babc49e3ba8c2c3682ac0a05';
    private const USER_ID = 'pcwoveland';
    private const USER_PWD_MD5 = '5d0ea53f421c80b519878c78b188fcef';

    /**
     * @var string
     */
    private $timestamp;

    /**
     * @var string
     */
    private $sign;

    /**
     * @var string
     */
    private $signMethod;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $format;

    /**
     * @var int
     */
    private $expiresIn;

    /**
     * @var Client
     */
    private $http;

    /**
     * @var Collection
     */
    private $privateParams;

    /**
     * APIAuthConcoxService constructor.
     */
    public function __construct()
    {
        $this->signMethod = 'md5';
        $this->version = '1.0';
        $this->format = 'json';
        $this->expiresIn = '600';

        $this->http = new Client(['http_errors' => false]);
    }

    /**
     * @param Collection | array $private
     */
    public function setPrivateParams($private)
    {
        $this->privateParams = collect($private);
        $this->sign();
    }

    /**
     * @return Collection
     */
    public function getCommonParams()
    {
        return collect([
            'timestamp' => $this->timestamp,
            'app_key' => self::APP_KEY,
            'sign' => $this->sign,
            'sign_method' => $this->signMethod,
            'v' => $this->version,
            'format' => $this->format,
        ]);
    }

    public function getParams()
    {
        return $this->getCommonParams()->merge($this->privateParams);
    }

    public function sign()
    {
        $this->timestamp = Carbon::now('UTC')->toDateTimeString();
        $paramsSorted = $this->getParams()->forget('sign')->sortBy(function ($value, $key) {
            return $key;
        });

        $base = self::APP_SECRET;
        foreach ($paramsSorted as $key => $value) {
            $base .= "$key$value";
        }
        $base .= self::APP_SECRET;

        $this->sign = Str::upper(md5($base));
    }

    /**
     * @return Collection
     */
    public function request()
    {
        $response = $this->http->request('POST', self::API_URL, [
            'form_params' => $this->getParams()->toArray()
        ])->getBody()->getContents();

        return collect(json_decode($response));
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken()
    {
        $accessToken = AccessToken::whereAccount(self::USER_ID)->first();
        $requestNew = false;

        if ($accessToken) {
            if ($accessToken->isAboutToBeInvalid()) {
                $this->setPrivateParams([
                    'method' => 'jimi.oauth.token.refresh',
                    'access_token' => $accessToken->access_token,
                    'refresh_token' => $accessToken->refresh_token,
                    'expires_in' => $accessToken->expires_in,
                ]);
            } else if ($accessToken->isAlive()) {
                return $accessToken;
            } else {
                $requestNew = true;
            }
        } else {
            $requestNew = true;
        }

        if ($requestNew) {
            $this->setPrivateParams([
                'method' => 'jimi.oauth.token.get',
                'user_id' => self::USER_ID,
                'user_pwd_md5' => self::USER_PWD_MD5,
                'expires_in' => $this->expiresIn,
            ]);
        }

        $response = $this->request();

        if ($response->get('code') == 0) {
            $accessToken = AccessToken::findOrCreate($response->get('result'));
            $accessToken->save();
            return $accessToken;
        }

        return null;
    }
}
