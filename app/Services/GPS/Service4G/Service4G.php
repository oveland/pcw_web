<?php


namespace App\Services\GPS\Service4G;


use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\GpsVehicle;
use App\Services\Apps\Rocket\Photos\PhotoService;
use App\Services\Apps\Rocket\Photos\SavePhotoService;
use App\Services\GPS\Syrus\SyrusService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Image;
use Log;
use Storage;
use Symfony\Component\ErrorHandler\Error\FatalError;

class Service4G extends SyrusService
{

    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    function syncPhoto($imei): Collection
    {
        if (!$this->readyToSync($imei)) return collect([
            'success' => false,
            'message' => " ~~~~ $imei is not ready to Sync",
        ]);

        $this->setStatus($imei, true);

        $imeisForPhotoService = [
            '352557104839868',
            '352557104794196',
            '352557104839869',
            '352557104791564',
            '352557104788131',
            '352557104788503', //8217
            '352557104839116',
            '352557104831642',
            '352557104466092',
            '352557104777778',
            '352557104791564',
            '352557104794196'


        ];

        $service = new SavePhotoService();

        $replacements = [
            '352557104777777' => '352557104834810',
            '352557104777778' => '352557104466092',
            '3525571047885031' => '352557104788503'
        ];

        if (array_key_exists($imei, $replacements)) {
            $imeiParaConsulta = $replacements[$imei];
        } else {
            $imeiParaConsulta = $imei;
        }
        $gpsVehicle = GpsVehicle::where('imei', $imeiParaConsulta)->first();
        $vehicle = $gpsVehicle->vehicle;

        if (!$gpsVehicle) return collect([
            'success' => false,
            'message' => "Imei $imei is not associated with a vehicle",
        ]);

        $waitSeconds = random_int(0, 40);
        $this->log("Sync photo from API GPS Syrus and vehicle $vehicle->number id: $vehicle->id in next $waitSeconds seconds");
        sleep($waitSeconds);
        $this->log("        • Start sync for vehicle $vehicle->number");

        $response = collect([
            'success' => true,
            'message' => "Success sync 4G",
        ]);

        if($imei=='352557104777777' || $imei=='352557104777778' || $imei == '3525571047885031'){
            $deviceID = $gpsVehicle->tags;
        }else{
           $deviceID = $gpsVehicle->device_id;
        }
        $date4G = carbon::now()->toDateString();
     /*   if ($vehicle->number == '8217'){
            $date4G ='2025-10-29';
        }*/
        //$date4G = '2025-01-11';
        $path = "$deviceID/$date4G";
        $response->put('imei', $imei);
        $storage = Storage::disk('Sync4G');
        $files = collect($storage->files($path));

        $this->log("         • Vehicle #$vehicle->number total FPT photos: " . $files->count());

        $saveFiles = collect([]);
        foreach ($files as $index => $file) {
            $fileName = collect(explode('/', $file))->last();

            if (Str::endsWith($file, '.jpg') && !Photo::where('uid', $file)->first()) {
                $side = $this->getSide($fileName, $imei);
                $service->for($vehicle, $side);
                $fileHasError = false;
                
                try {
                    $jpegInfo = exec("jpeginfo -c " . $storage->path($file));
                    $fileHasError = Str::contains($jpegInfo, "ERROR");
                } catch (Exception $e) {
                    
                }
                $fileNames = explode('_', $fileName);
                if (isset($fileNames[2]) && preg_match('/^\d{14}$/', $fileNames[2])) {
                    $dateImag = Carbon::createFromFormat("YmdHis", $fileNames[2])->toDateTimeString();
                } elseif (isset($fileNames[3]) && preg_match('/^\d{14}$/', $fileNames[3])) {
                    $dateImag = Carbon::createFromFormat("YmdHis", $fileNames[3])->toDateTimeString();
                }

                $date = ($dateImag === '')
                    ? Carbon::createFromTimestamp($storage->lastModified($file))->toDateTimeString()
                    : $dateImag;
                if (!$fileHasError) {
                    $image = Image::make($storage->get($file));
                        $process = $service->saveImageData([
                            'date' => $date,
                            'img' => $image->encode('data-url'),
                            'type' => 'syrus',
                            'side' => $side,
                            'uid' => $vehicle->id . "_" . $fileName
                        ]);
                    $success = $process->response->success;
                    $message = $process->response->message;
                    $extra = "";
                    if ($success === true) {
                        $deleted = $storage->delete($file);
                        if (!$deleted) $extra = ". Error photo NOT deleted!";
                        $message .= $extra;
                    } else {
                        $extra = $message;
                    }
                    $this->log("             • Vehicle #$vehicle->number saveImageData • #$index/" . $files->count() . " $extra");
                    $response['success'] = $success;
                    $response['message'] = $message;
                    $saveFiles->push($message);
                } else {
                    $storage->delete($file);
                }
            }
        }

        $response->put('sync', $saveFiles);
        $this->setStatus($imei, false);
        return $response;
    }

    function getSide($fileName, $imei)
    {
        $fileNames = explode('_', $fileName);



        if ($imei == '352557104788503') {
            if ($fileNames[1] == 'ch1') return '1';
            if ($fileNames[1] == 'ch2') return '2';
            if ($fileNames[1] == 'ch3') return 'E';
            if ($fileNames[1] == 'ch4') return 'E';

        }
        if ($imei == '3525571047885031'){
            if ($fileNames[1] == 'ch1') return '3';
            if ($fileNames[1] == 'ch2') return '4';
            if ($fileNames[1] == 'ch3') return '5';
            if ($fileNames[1] == 'ch4') return '6';
        }
        if ($imei == '352557104834810') {
            if ($fileNames[1] == 'ch1') return '1';
            if ($fileNames[1] == 'ch2') return '2';
            if ($fileNames[1] == 'ch3') return '5';
            if ($fileNames[1] == 'ch4') return '6';
         }
        if ($imei == '352557104777777') {
            if ($fileNames[1] == 'ch1') return '3';
            if ($fileNames[1] == 'ch2') return '4';
        }
        if ($imei == '352557104466092') {
            if ($fileNames[1] == 'ch1') return '1';
            if ($fileNames[1] == 'ch4') return '2';
            if ($fileNames[1] == 'ch2') return '6';
            if ($fileNames[1] == 'ch3') return '7';
        }
        if ($imei == '352557104777778') {
            if ($fileNames[1] == 'ch1') return '3';
            if ($fileNames[1] == 'ch2') return '5';
            if ($fileNames[1] == 'ch3') return '4';
        }


        if ($imei == '352557104727915') {
            if ($fileNames[2] == 'ch1') return '1';
            if ($fileNames[2] == 'ch2') return '2';
            if ($fileNames[2] == 'ch3') return '4';
            if ($fileNames[2] == 'ch4') return '3';
            if ($fileNames[2] == 'ch5') return '5';
        }
        if ($imei == '352557104743888') {
            if ($fileNames[2] == 'ch1') return '1';
            if ($fileNames[2] == 'ch2') return '2';
            if ($fileNames[2] == 'ch3') return '3';
            if ($fileNames[2] == 'ch4') return '4';
            if ($fileNames[2] == 'ch5') return '5';
            if ($fileNames[2] == 'ch6') return '6';
        }
        if ($imei == '352557104802940') {
            if ($fileNames[2] == 'ch1') return '1';
            if ($fileNames[2] == 'ch2') return '2';
            if ($fileNames[2] == 'ch3') return '3';
            if ($fileNames[2] == 'ch4') return '4';
            if ($fileNames[2] == 'ch5') return '5';
            if ($fileNames[2] == 'ch6') return '6';
            if ($fileNames[2] == 'ch7') return '7';
            if ($fileNames[2] == 'ch8') return '8';
        }
        if ($fileNames[1] == 'ch1') return '1';
        if ($fileNames[1] == 'ch2') return '2';
        if ($fileNames[1] == 'ch3') return '3';
        if ($fileNames[1] == 'ch4') return '4';
        if ($fileNames[1] == 'ch5') return '5';
        if ($fileNames[1] == 'ch6') return '6';
        if ($fileNames[1] == 'ch7') return '7';


        return '0';
    }

    function log($message)
    {
        Log::channel('sync4g')->info("[Service4G] $message");
    }
}
