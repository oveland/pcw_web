<?php

namespace App\Console\Commands;

use App\Company;
use App\Mail\ConsolidatedPassengersReportMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Mail;

class ConsolidatedPassengerReportMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-mail:consolidated-passengers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends consolidated passenger report mail';

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
     */
    public function handle()
    {
        $company = Company::find(14);
        $prevDays = 1;

        $dateReport = Carbon::now()->subDay($prevDays)->toDateString();

        $mail = new ConsolidatedPassengersReportMail($company, $dateReport);
        if ($mail->buildReport()) {
            Mail::to(['gerencia@alameda.com.co', 'movilidad@alameda.com.co', 'jeferh@alameda.com.co'], $company->name)
                ->bcc(['oscarivelan@gmail.com', 'olatorre22@hotmail.com', 'soportenivel2pcwtecnologia@outlook.com'])
                ->send($mail);

            $this->info("$company->name Consolidated Passengers Mail send for date $dateReport!");
        } else {
            $this->info("No consolidated passengers reports found for date $dateReport");
        }
    }
}
