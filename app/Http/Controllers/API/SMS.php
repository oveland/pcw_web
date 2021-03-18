<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 9/01/2018
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\API;


use App\Models\Vehicles\SimGPS;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SMS
{

    public static function sendCommand1($sms, $phone)
    {
        $ch = curl_init();

        $post = array(
            'account' => config('sms.api_id'),
            'apiKey' => config('sms.api_key'),
            'token' => config('sms.api_token'),
            'toNumber' => $phone,
            'sms' => $sms,
            'flash' => 0,
            'isPriority' => 1,
            'sc' => '210522',
            'sendDate' => time()
        );

        $url = "https://api101.hablame.co/api/sms/v2.1/send/";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response, true);
    }

    public static function sendCommand($sms, $phone)
    {
        $url = 'https://api101.hablame.co/api/sms/v2.1/send/';
//        $url = 'https://api102.hablame.co/api/sms/v2.1/send/';
        $data = array(
            'account' => config('sms.api_id'),
            'apiKey' => config('sms.api_key'),
            'token' => config('sms.api_token'),
            'toNumber' => $phone,
            'sms' => $sms,
            'flash' => 0,
            'isPriority' => 1,
            'sc' => '210522',
            'sendDate' => time()
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
        return (object)['status' => false];
    }

    public static function sendResetCommandToVehicle($vehicle)
    {
        $company = $vehicle->company;
        $simGPS = SimGPS::findByVehicleId($vehicle->id)->get()->first();
        $response = [
            'success' => false,
            'log' => ''
        ];

        if ($simGPS) {
            //$command = $simGPS->gps_type == 'COBAN' ? "reset123456" : 'AT$RESET';
            $command = $simGPS->getResetCommand();

            $responseSMS = self::sendCommand($command, $simGPS->sim);
            $response['success'] = $responseSMS["resultado"] === 0;

            if ($response['success']) $responseLog = "Send SMS for:";
            else $responseLog = "Message not tx for:";

            $responseLog .= " $simGPS->sim $simGPS->gps_type => $command: ";
        } else {
            $responseLog = "No found SIM for:";
        }

        $response['log'] = $responseLog . " $vehicle->id | $vehicle->plate | $vehicle->number | $company->short_name";
        Log::useDailyFiles(storage_path() . '/logs/sms.log', 10);
        Log::info($response['log']);

        return (object)$response;
    }
}