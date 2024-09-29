<?php

namespace App\Console\Commands\Tests;

use App\Services\AWS\RekognitionService;
use Aws\S3\S3Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;


use Aws\Rekognition\RekognitionClient;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for test write log';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    /**
     * @var RekognitionService
     */
    private $rekognitionService;
    public function __construct()
    {
        parent::__construct();
        $this->rekognitionService = new RekognitionService();
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->rekognitionService->process('asociate');
    }


}
