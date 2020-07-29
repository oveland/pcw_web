<?php

namespace App\Services\Reports\Routes\Takings;

use App\Models\Routes\DispatchRegister;

class RouteTakingsService
{
    /**
     * @param DispatchRegister $dispatchRegister
     * @param array $data
     * @return object
     */
    function taking(DispatchRegister $dispatchRegister, $data = [])
    {
        $response = (object)['success' => false, 'message' => ''];

        $takings = $dispatchRegister->takings;
        $takings->fill($data);

        if ($takings->save()) {
            $response->success = true;
            $response->message = __('Takings registered successfully');
            $response->taken = $takings->isTaken();
        } else {
            $response->message = __('Takings not registered');
        }

        return $response;
    }
}