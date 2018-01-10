<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 9/01/2018
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\API;


use App\SimGPS;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SMS
{
    public static function sendCommand($sms, $phone)
    {
        $ref = Carbon::now()->format("YmdHis");
        $url = 'https://api.hablame.co/sms/envio/';
        $data = array(
            'cliente' => config('sms.api_id'),
            'api' => config('sms.api_key'),
            'numero' => $phone,
            'sms' => $sms,
            'fecha' => '',
            'referencia' => "PCW.$ref." . str_limit(str_replace(" ", "_", $sms), 10),
        );

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $result = json_decode((file_get_contents($url, false, $context)), true);

        return $result;
    }

    public static function sendResetCommandToVehicle($vehicle)
    {
        $company = $vehicle->company;
        $simGPS = SimGPS::findByVehicleId($vehicle->id);
        $response = [
            'success' => false,
            'log' => ''
        ];

        if ($simGPS) {
            $command = $simGPS->gps_type == 'TR' ? "reset123456" : "AT&RESET";

            $responseSMS = self::sendCommand($command, $simGPS->sim);
            $response['success'] = $responseSMS["resultado"] === 0;

            if ($response['success']) $responseLog = "Send SMS for:";
            else $responseLog = "Message not tx for:";

            $responseLog .= " $simGPS->sim => $command: ";
        } else {
            $responseLog = "No found SIM for:";
        }

        $response['log'] = $responseLog . " $vehicle->id | $vehicle->plate | $vehicle->number | $company->short_name";
        Log::error($responseLog);

        return (object)$response;
    }
}