<?php

namespace App\Services\API\Apps;

use App\Services\API\Apps\Contracts\APIAppsInterface;
use App\Services\API\Apps\Contracts\APIFilesInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class APIConcoxService implements APIAppsInterface
{
    private const API_URL = 'http://open.10000track.com/route/rest';
    private const APP_KEY = 'APP_KEY_HERE';
    private const APP_SECRET = 'APP_SECRET_HERE';
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
        $this->v = '1.0';
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

    public function buildAccessTokenBody()
    {
        $this->timestamp = Carbon::now('UTC')->toDateTimeString();
        $this->sign = md5(self::APP_SECRET . "app_key" . self::APP_KEY . "expires_in" . $this->expiresIn . "format" . $this->format . "method" . $this->method . "sign_method" . $this->signMethod . "timestamp" . $this->timestamp . "user_id" . self::USER_ID . "user_pwd_md5" . self::USER_PWD_MD5 . "v" . $this->version . self::APP_SECRET);

        return [
            'method' => $this->method,
            'timestamp' => $this->timestamp,
            'app_key' => self::APP_KEY,
            'sign' => Str::upper($this->sign),
            'sign_method' => $this->signMethod,
            'v' => $this->version,
            'format' => $this->format,
            'user_id' => self::USER_ID,
            'user_pwd_md5' => self::USER_PWD_MD5,
            'expires_in' => $this->expiresIn,
        ];
    }

    /**
     * @return JsonResponse
     */
    public function getAccessToken()
    {
        $response = [];
        $this->method = 'jimi.oauth.token.get';

        $requestBody = $this->buildAccessTokenBody();

        dd($requestBody);

        //TODO: Implement logic for get Access Token

        return response()->json($response);
    }
}
