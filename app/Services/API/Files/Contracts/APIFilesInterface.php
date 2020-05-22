<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 4/06/2018
 * Time: 11:05 PM
 */

namespace App\Services\API\Files\Contracts;

/**
 * Interface APIAppsInterface
 * @package App\Services\API\Apps\Contracts
 */
interface APIFilesInterface
{
    /**
     * @return mixed
     */
    public function serve();
}