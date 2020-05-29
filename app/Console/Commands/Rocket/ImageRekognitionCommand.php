<?php

namespace App\Console\Commands\Rocket;

use App\Models\Apps\Rocket\Photo;
use App\Services\AWS\RekognitionService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ImageRekognitionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:image:rekognition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var RekognitionService
     */
    private $rekognition;

    /**
     * Create a new command instance.
     *
     * @param RekognitionService $rekognition
     */
    public function __construct(RekognitionService $rekognition)
    {
        parent::__construct();
        $this->rekognition = $rekognition;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $photos = Photo::where('dispatch_register_id', 1202190)->get();

        foreach ($photos as $photo) {
            $photo->size = \Storage::disk('local')->size($photo->path);
            $this->info("$photo->path >>> $photo->size bytes");
            $photo->save();
        }

        //$data = $this->rekognition->sefFile('Apps/Rocket/Photos/1245/20200522080634.jpeg')->person();
        //dd($data);
    }
}
