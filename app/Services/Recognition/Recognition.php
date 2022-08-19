<?php

namespace App\Services\Recognition;

use App\Models\Apps\Rocket\Photo;

interface Recognition
{
    /**
     * @param Photo $photo
     * @return Recognition
     */
    function setPhoto(Photo $photo);

    function process($type = 'persons');
}