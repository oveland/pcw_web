<?php

namespace App\Console\Commands\Rocket;

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
    protected $signature = 'rocket:image:rekognition {--type=persons}';

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
        $data = null;
        $type = $this->option('type');

        $data = $this->rekognition->sefFile('Apps/Rocket/Photos/1908/20200526015304.jpeg')->process($type);

        dd($data);
    }
}
