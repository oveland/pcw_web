<?php

namespace App\Services\Apps\Concox;

use App\Services\API\Apps\Contracts\APIFilesInterface;
use Illuminate\Support\Collection;

class ConcoxService
{
    /**
     * @var AuthService
     */
    private $auth;

    /**
     * ConcoxService constructor.
     */
    public function __construct()
    {
        $this->auth = new AuthService();
    }

    /**
     * @param string $camera
     * @return Collection
     */
    public function takePhoto($camera = '1')
    {
        $response = collect([]);
        $accessToken = $this->auth->getAccessToken();

        if ($accessToken) {
            $this->auth->setPrivateParams([
                'method' => 'jimi.device.meida.cmd.send',
                'access_token' => $accessToken->access_token,
                'imei' => '351777095427025',
                'camera' => $camera,
                'mediaType' => '1',
            ]);

            $request = $this->auth->request();

            if ($request->get('code') == 0) {
                $response = collect($request->get('result'));
            }
        }

        return $response;
    }

    /**
     * @param string $camera
     * @return Collection
     */
    public function getPhoto($camera = '1')
    {
        $photos = collect([]);
        $accessToken = $this->auth->getAccessToken();

        if ($accessToken) {
            $this->auth->setPrivateParams([
                'method' => 'jimi.device.media.URL',
                'access_token' => $accessToken->access_token,
                'imei' => '351777095427025',
                'camera' => $camera,
                'media_type' => '1',
                'page_no' => 0,
                'page_size' => 20,
            ]);

            $request = $this->auth->request();

            if ($request->get('code') == 0) {
                $photos = collect($request->get('result'))->sortByDesc('create_time');
            }
        }

        dd($photos->toArray());

        return $photos;
    }

    /**
     * @return Collection
     */
    public function getLiveStreamVideo()
    {
        $response = collect([]);
        $accessToken = $this->auth->getAccessToken();

        if ($accessToken) {
            $this->auth->setPrivateParams([
                'method' => 'jimi.device.live.page.url',
                'access_token' => $accessToken->access_token,
                'imei' => '351777095427025'
            ]);

            $request = $this->auth->request();

            if ($request->get('code') == 0) {
                $response = collect($request->get('result'));
            }
        }

        return $response;
    }

    /**
     * @return Collection
     */
    public function getCommandSupportList()
    {
        $response = collect([]);
        $accessToken = $this->auth->getAccessToken();

        if ($accessToken) {
            $this->auth->setPrivateParams([
                'method' => 'jimi.open.instruction.list',
                'access_token' => $accessToken->access_token,
                'imei' => '351777095427025'
            ]);

            $request = $this->auth->request();

            if ($request->get('code') == 0) {
                $response = collect($request->get('result'));
            }
        }

        return $response;
    }
}
