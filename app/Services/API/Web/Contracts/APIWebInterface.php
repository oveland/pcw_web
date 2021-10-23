<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 4/06/2018
 * Time: 11:05 PM
 */

namespace App\Services\API\Web\Contracts;

use Illuminate\Http\JsonResponse;

/**
 * Interface APIWebInterface
 * @package App\Services\API\Web\Contracts
 */
interface APIWebInterface
{
    /**
     * @return JsonResponse
     */
    public function serve(): JsonResponse;
}