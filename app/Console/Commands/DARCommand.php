<?php

namespace App\Console\Commands;

use App\Models\Company\Company;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class DARCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dar:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes Automatic Route Detection';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        ini_set('MAX_EXECUTION_TIME', 0);
        set_time_limit(0);

        $company = Company::find(12);
        $prevDays = 1;

        $dateReport = Carbon::now()->subDay($prevDays)->toDateString();

        $client = new Client();
        $url = config('gps.server.url') . "/api/auto-dispatch-register/process-companies-reports?start=true&company=$company->id&date=$dateReport";
        $this->info("Request to: " . $url);
        $response = $client->request('GET', $url, ['timeout' => 0]);

        dump($response->getBody());
    }
}
