<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 4/06/2018
 * Time: 11:05 PM
 */

namespace App\Services\API\Web\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Interface APIWebInterface
 * @package App\Services\API\Web\Contracts
 */
interface APIWebInterface
{
    /**
     * @param $service
     * @param Request $request
     * @return JsonResponse
     */
    public static function serve($service, Request $request): JsonResponse;
}