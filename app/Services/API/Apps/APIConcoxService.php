<?php

namespace App\Services\API\Apps;

use App\Services\API\Apps\Contracts\APIAppsInterface;
use App\Services\API\Apps\Contracts\APIFilesInterface;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class APIConcoxService implements APIAppsInterface
{
    private const API_URL = 'http://open.10000track.com/route/rest';
//    private const API_URL = 'https://postman-echo.com/post';
    private const APP_KEY = '8FB345B8693CCD00E573E7459324059A';
//    private const APP_SECRET = 'be8fe6b5babc49e3ba8c2c3682ac0a05';
    private const APP_SECRET = 'BE8FE6B5BABC49E3BA8C2C3682AC0A05';
    private const USER_ID = 'pcwoveland';
    private const USER_PWD_MD5 = 'BA1E3ADBF24AD7221BFC4F5C0446DF4E';

    /**
     * @var Request | Collection
     */
    private $request;

    /**
     * @var string
     */
    private $service;

    /**
     * @var string
     */
    private $method;

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
     * APIRocketService constructor.
     * @param $service
     */
    public function __construct($service)
    {
        $this->request = request();
        $this->service = $service ?? $this->request->get('action');

        $this->signMethod = 'md5';
        $this->version = '1.0';
        $this->format = 'json';
        $this->expiresIn = 7200; // 2 Hours
    }

    /**
     * @return JsonResponse
     */
    public function serve(): JsonResponse
    {
        switch ($this->service) {
            case 'get-access-token':
                return $this->getAccessToken();
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => __('Service not found')
                ]);
                break;
        }
    }

    public function buildAccessTokenBody($type)
    {
        $private = collect([]);
        switch ($type) {
            case 'get-access-token':
                $this->method = 'jimi.oauth.token.get';
                $private = collect([
                    'user_id' => self::USER_ID,
                    'user_pwd_md5' => self::USER_PWD_MD5,
                    'expires_in' => $this->expiresIn,
                ]);
                break;
        }

        return $this->getCommonParametersRequest()->merge($private)->toArray();
    }

    /**
     * @return Collection
     */
    public function getCommonParametersRequest()
    {
        $this->timestamp = Carbon::now('UTC')->toDateTimeString();

        $utf8 = utf8_encode(self::APP_SECRET . "app_key" . self::APP_KEY . "expires_in" . $this->expiresIn . "format" . $this->format . "method" . $this->method . "sign_method" . $this->signMethod . "timestamp" . $this->timestamp . "user_id" . self::USER_ID . "user_pwd_md5" . self::USER_PWD_MD5 . "v" . $this->version . self::APP_SECRET);
        $this->sign = md5($utf8);

        return collect([
            'method' => $this->method,
            'timestamp' => $this->timestamp,
            'app_key' => self::APP_KEY,
            'sign' => Str::upper($this->sign),
            'sign_method' => $this->signMethod,
            'v' => $this->version,
            'format' => $this->format,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getAccessToken()
    {
        $response = [];
        $requestBody = $this->buildAccessTokenBody('get-access-token');

//        dd($requestBody);

        $response = $this->rest($requestBody);

        return response()->json(json_decode($this->rest($requestBody), true));
    }

    public function rest($body)
    {
        $client = new Client([
            'base_uri' => new Uri(self::API_URL)
        ]);

        return $client->request('POST', self::API_URL, [
            'json' => $body
        ])->getBody()->getContents();
    }
}
