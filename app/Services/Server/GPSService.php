<?php


namespace App\Services\Server;


use Exception;

class GPSService
{
    function testConnection($address, $port)
    {
        $response = false;

        try {
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array(
                "sec" => 1, // Timeout in seconds
                "usec" => 0  // I assume timeout in microseconds
            ));

            if ($socket && socket_connect($socket, $address, $port)) {
                socket_close($socket);
                $response = true;
            }
        } catch (Exception $e) {

        }

        return $response;
    }
}