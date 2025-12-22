<?php


namespace App\Services\GPS\Syrus5G;


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

class Service5G extends SyrusService
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

        $service = new SavePhotoService();
        $imeiOriginal = $imei;
        $replacements = [
            '352557104777777' => '352557104834810',
            '352557104123456789' => '352557104834810',
            '352557104777778' => '352557104466092',
            '3525571048064201' => '352557104806420',
            '3525571047276001' => '352557104727600',
            '3525571048126912' => '352557104812691',
            '3525571044860331' => '352557104486033',
            '3525571044846321' => '352557104484632',
            '3525571044662171' => '352557104466217',
            '3525571044662172' => '352557104466217',
            '3525571047885031' => '352557104788503',
            '3525571047806411' => '352557104813640',
            '3525571047806412' => '352557104813640',
            '352557104722222' => '352557104772119',
            '3525571047111111' => '352557104772119',
            '3525571047466723' => '352557104746667',
            '35255710474667986' => '352557104746667',
            '3525571044762241' => '352557104476224',
            '3525571044762242' => '352557104476224',
            '3525571044818772' => '352557104481877',
            '3525571044818773' => '352557104481877',
            '3525571044661423' => '352557104466142',
            '3525571044661424' => '352557104466142',
            '3525571044772234' => '352557104477222',
            '3525571044772245' => '352557104477222',
            '3525571044788231' => '352557104478824',
            '3525571044788225' => '352557104478824',
            '35255710480865812' => '352557104808657',
            '35255710480865412' => '352557104808657',
            '3525571048401234' => '352557104840254',
            '3525571048405678' => '352557104840254',
            '35255710446935112' => '352557104469351',
            '35255710446935113' => '352557104469351',
        ];
        if (array_key_exists($imei, $replacements)) {
            $imeiParaConsulta = $replacements[$imei];
        } else {
            $imeiParaConsulta = $imei;
        }
        $gpsVehicle = GpsVehicle::where('imei', $imeiParaConsulta)->first();

        if (!$gpsVehicle) return collect([
            'success' => false,
            'message' => "Imei $imei is not associated with a vehicle",
        ]);

        $vehicle = $gpsVehicle->vehicle;

        $waitSeconds = random_int(0, 40);
        $this->log("Sync photo from API GPS Syrus and vehicle $vehicle->number id: $vehicle->id in next $waitSeconds seconds");
        sleep($waitSeconds);
        $this->log("        • Start sync for vehicle $vehicle->number");

        $response = collect([
            'success' => true,
            'message' => "Success sync 4G",
        ]);
        if ($imei == '352557104777777' || $imei == '352557104777778' || $imei == '3525571048064201'
            || $imei == '3525571047276001' || $imei == '3525571048126912'
            || $imei == '3525571044860331' || $imei == '3525571044846321'
            || $imei == '3525571044662171'
            || $imei == '3525571047885031'|| $imei == '3525571047806411'|| $imei == '352557104722222'
            || $imei == '3525571047466723'|| $imei == '3525571044762241' || $imei == '3525571044818772' || $imei == '3525571044661423'
            || $imei == '3525571044772234' || $imei == '3525571044788231' || $imei == '35255710480865812'|| $imei == '3525571048401234'
            || $imei == '35255710446935112'
        )
        {
            $deviceID = $gpsVehicle->tags;
        }
        elseif ($imei == '3525571044662172'|| $imei == '3525571047806412'|| $imei == '352557104123456789'|| $imei == '3525571047111111'
            || $imei == '35255710474667986'|| $imei == '3525571044762242' || $imei == '3525571044818773' || $imei == '3525571044661424'
            || $imei == '3525571044772245' || $imei == '3525571044788225' || $imei == '35255710480865412'|| $imei == '3525571048405678'
            || $imei == '35255710446935113'
        ) {
            $deviceID = $gpsVehicle->device_id_2;
        }
        else {
            $deviceID = $gpsVehicle->device_id;
        }

     



        if ($vehicle->number == '252525525'){
            $date5G = '2025-11-25';
        }else{
            $date5G = carbon::now()->toDateString();
        }
        $date5G = carbon::now()->toDateString();

        $path = "$deviceID/$date5G";
        $response->put('imei', $imei);

        $storage = Storage::disk('Sync4G');
        $files = collect($storage->files($path));

        $this->log("         • Vehicle #$vehicle->number total FPT photos: " . $files->count());

        $saveFiles = collect([]);
        foreach ($files as $index => $file) {
            $fileName = collect(explode('/', $file))->last();

            if (Str::endsWith($file,['T.jpg']) && !Photo::where('uid', $file)->first()) {
              //  dd('no existe la foto en la base de datos', $file);
                var_dump('ingresa a sincronizar esta foto en 5G', $file);
                $side = $this->getSide($fileName, $imei, $file);
                $service->for($vehicle, $side);
                var_dump($side);
                $uid = $vehicle->id . "_" . $fileName;
                dump("este es el uid que guardara",$uid);

                // Verificar si ya existe en ambas tablas
               /* $existsInAppPhotos = \DB::table('app_photos')
                    ->where('uid', $uid)
                    ->exists();

                $existsInFileNames = \DB::table('file_names')
                    ->where('file_name', $fileName)
                    ->exists();*/

                // Si existe en ambas tablas, saltar al siguiente archivo
              /*  if ($existsInAppPhotos && $existsInFileNames) {
                    var_dump("• Vehicle #$vehicle->number • Photo already exists in both tables: $fileName");
                    continue; // Saltar al siguiente archivo
                }*/


                $fileHasError = false;
                try {
                    $jpegInfo = exec("jpeginfo -c " . escapeshellarg($storage->path($file)));
                    $fileHasError = Str::contains($jpegInfo, "ERROR");
                } catch (Exception $e) {

                }
                $fileNames = explode('_', $fileName);
                $dateImag = '';
                // Verificar si el fragmento de la fecha está en $fileNames[2] o $fileNames[3]
                if (isset($fileNames[2]) && preg_match('/^\d{14}$/', $fileNames[2])) {
                    $dateImag = Carbon::createFromFormat("YmdHis", $fileNames[2])->toDateTimeString();
                } elseif (isset($fileNames[3]) && preg_match('/^\d{14}$/', $fileNames[3])) {
                    $dateImag = Carbon::createFromFormat("YmdHis", $fileNames[3])->toDateTimeString();
                }

                $date = ($dateImag == '')
                    ? Carbon::createFromTimestamp($storage->lastModified($file))->toDateTimeString()
                    : $dateImag;
                if (!$fileHasError) {
                    $image = Image::make($storage->get($file));
                    $fileType = Str::endsWith($file, 'T.jpg') ? 'T.jpg' : 'E.jpg';
                    $process = $service->saveImageData([
                        'date' => $date,
                        'img' => $image->encode('data-url'),
                        'type' => 'syrus',
                        'side' => $side,
                        'uid' => $vehicle->number . "_" . $fileName,
                        'file_type' => $fileType,
                        'file_name' => $fileName
                    ]);

                    $success = $process->response->success;
                    $message = $process->response->message;
                    $extra = "";
                    if ($success === true
                        &&$vehicle->number !== '8353'
                        &&$vehicle->number !== '9104'
                        //&& $vehicle->number !== '8331'
                        //&& $vehicle->number !== '8509'
                        //&& $vehicle->number !== '6605'
                        //&& $vehicle->number !== '2907'
                        //&& $vehicle->number !== '8511'
                        //&& $vehicle->number !== '8517'
                        //&& $vehicle->number !== '8253'
                        //&& $vehicle->number !== '8501'
                        )
                    {
                        $deleted = $storage->delete($file);

                        if (!$deleted) {
                            $extra = ". Error photo NOT deleted!";
                        }
                        $message .= $extra;
                    } else {
                        $extra = $message;
                    }
                    $this->log("             • Vehicle #$vehicle->number saveImageData • #$index/" . $files->count() . " $extra" . $message);
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

    function getSide($fileName, $imei, $file = null)
    {
        $fileNames = explode('_', $fileName);
        if (Str::endsWith($file,['E.jpg'])){
            return 'E';
        }else{
            if ($imei == '352557104469351'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '7';
                if ($fileNames[1] == 'ch4') return '8';
            }
            if ($imei == '35255710446935112'){
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';

            }
            if ($imei == '35255710446935113'){
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';

            }





            if ($imei == '352557104840254'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '7';
                if ($fileNames[1] == 'ch4') return '8';
            }

            if ($imei == '3525571048401234'){
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
            }
            if ($imei == '3525571048405678'){
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';
            }


            if ($imei == '352557104808657'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '7';
                if ($fileNames[1] == 'ch4') return '8';
            }
            if ($imei == '35255710480865812'){
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';

            }
            if ($imei == '35255710480865412'){
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';
            }

            if ($imei == '352557104478824'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '7';
                if ($fileNames[1] == 'ch4') return '8';
            }
            if ($imei == '3525571044788231'){
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';

            }
            if ($imei == '3525571044788225'){
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';

            }

            if ($imei == '352557104477222'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '7';
                if ($fileNames[1] == 'ch4') return '8';
            }

            if ($imei == '3525571044772234'){
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
            }

            if ($imei == '3525571044772245'){
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';
            }



            if ($imei == '352557104466142'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
            }

            if ($imei == '3525571044661423'){
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
            }

            if ($imei == '3525571044661424'){
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';
                if ($fileNames[1] == 'ch3') return '7';
                if ($fileNames[1] == 'ch4') return '8';
            }

            if ($imei == '352557104481877'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '7';
                if ($fileNames[1] == 'ch4') return '8';

            }
            if ($imei == '3525571044818772'){
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
            }
            if ($imei == '3525571044818773'){
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';
            }


            if ($imei == '352557104476224'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '7';
                if ($fileNames[1] == 'ch4') return '8';

            }
            if ($imei == '3525571044762241'){
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
            }
            if ($imei == '3525571044762242'){
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';
            }


            if ($imei == '352557104746667'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '7';
                if ($fileNames[1] == 'ch4') return '8';

            }
            if ($imei == '3525571047466723'){
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';

            }
            if ($imei == '35255710474667986'){
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';

            }

            if ($imei == '352557104772119'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '7';
                if ($fileNames[1] == 'ch4') return '8';

            }
            if ($imei == '352557104722222'){
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';

            }
            if ($imei == '3525571047111111'){
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';

            }


            if ($imei == '352557104813640'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';

            }
            if ($imei == '3525571047806411'){
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';

            }
            if ($imei == '3525571047806412'){
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';
                if ($fileNames[1] == 'ch3') return '7';

            }
            if ($imei == '3525571044662171'){
                if ($fileNames[1] == 'ch1') return '2';
                if ($fileNames[1] == 'ch2') return '3';
            }
            if ($imei == '3525571047885031'){ //8217 xvr 2
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
            }
            if ($imei == '352557104788503'){ //8217 xvr 1
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '5';
                if ($fileNames[1] == 'ch4') return '6';
            }

            if ($imei == '3525571044662172'){
                if ($fileNames[1] == 'ch1') return '4';
                if ($fileNames[1] == 'ch2') return '5';
            }

            if ($imei == '352557104466217'){
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '6';
                if ($fileNames[1] == 'ch3') return '7';
            }


            if ($imei == '3525571044846321') {
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
                if ($fileNames[1] == 'ch4') return '5';
                if ($fileNames[1] == 'ch3') return '6';
            }

            if ($imei == '3525571044860331') {
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
                if ($fileNames[1] == 'ch4') return '5';
                if ($fileNames[1] == 'ch3') return '6';
            }

            if ($imei == '352557104812691') {
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';

            }
            if ($imei == '3525571048126912') {
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
                if ($fileNames[1] == 'ch3') return '5';
                if ($fileNames[1] == 'ch4') return '6';
            }

            if ($imei == '352557104806420') {
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '5';
                if ($fileNames[1] == 'ch4') return '6';
            }
            if ($imei == '3525571048064201') {
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
            }
            if ($imei == '3525571047276001') {
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
                if ($fileNames[1] == 'ch3') return '5';

            }

            if ($imei == '352557104727600') {
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';

            }

            if ($imei == '352557104791564') {
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '3';
                if ($fileNames[1] == 'ch4') return '4';
                if ($fileNames[1] == 'ch5') return '5';
                if ($fileNames[1] == 'ch6') return '6';
            }

            if ($imei == '352557104555559') {
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '3';
                if ($fileNames[1] == 'ch4') return '4';
                if ($fileNames[1] == 'ch5') return '5';
                if ($fileNames[1] == 'ch6') return '6';
            }

            if ($imei == '352557104831642') {
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '3';
                if ($fileNames[1] == 'ch4') return '4';
                if ($fileNames[1] == 'ch5') return '5';
                if ($fileNames[1] == 'ch6') return '6';
            }

            if ($imei == '352557104788503') {
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '3';
                if ($fileNames[1] == 'ch4') return '4';
                if ($fileNames[1] == 'ch5') return '5';
                if ($fileNames[1] == 'ch6') return '6';
            }
            if ($imei == '352557104839116') {
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '3';
                if ($fileNames[1] == 'ch4') return '4';
                if ($fileNames[1] == 'ch5') return '5';
                if ($fileNames[1] == 'ch6') return '6';
                if ($fileNames[1] == 'ch7') return '7';
                if ($fileNames[1] == 'ch8') return '8';
            }
            if ($imei == '352557104723690') {
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';
                if ($fileNames[1] == 'ch3') return '3';
                if ($fileNames[1] == 'ch4') return '4';
                if ($fileNames[1] == 'ch5') return '5';
                if ($fileNames[1] == 'ch6') return '6';
                if ($fileNames[1] == 'ch7') return '7';
                if ($fileNames[1] == 'ch8') return '8';
            }
            if ($imei == '352557104834810') {
                if ($fileNames[1] == 'ch1') return '1';
                if ($fileNames[1] == 'ch2') return '2';

            }
            if ($imei == '352557104777777') {
                if ($fileNames[1] == 'ch1') return '3';
                if ($fileNames[1] == 'ch2') return '4';
            }
            if ($imei == '352557104123456789') {
                if ($fileNames[1] == 'ch1') return '5';
                if ($fileNames[1] == 'ch2') return '6';
                if ($fileNames[1] == 'ch3') return '7';
                if ($fileNames[1] == 'ch4') return '8';
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
                if ($fileNames[2] == 'ch8') return '7';
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

    }

    function log($message)
    {
        Log::channel('sync4g')->info("[Service4G] $message");
    }
}
