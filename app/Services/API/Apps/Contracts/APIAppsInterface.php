<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 4/06/2018
 * Time: 11:05 PM
 */

namespace App\Services\API\Apps\Contracts;

use Illuminate\Http\JsonResponse;

/**
 * Interface APIAppsInterface
 * @package App\Services\API\Apps\Contracts
 */
interface APIAppsInterface
{
    /**
     * @return JsonResponse
     */
    public function serve(): JsonResponse;
}