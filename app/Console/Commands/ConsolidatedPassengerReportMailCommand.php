<?php

namespace App\Console\Commands;

use App\Models\Company\Company;
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
            $mailTo = ['gerencia@alameda.com.co', 'movilidad@alameda.com.co', 'jeferh@alameda.com.co'];
            $mailToBcc= ['oiva.pcw@gmail.com', 'olatorre22@hotmail.com', 'soportenivel2pcwtecnologia@outlook.com'];

            Mail::to($mailTo, $company->name)
                ->bcc($mailToBcc)
                ->send($mail);

            $this->info("$company->name Consolidated Passengers Mail send for date $dateReport!");
            foreach ($mailTo as $to){
                $this->info("   >> To: $to");
            }

            $this->info("-------------------------------------------");
            foreach ($mailToBcc as $bcc){
                $this->info("   >> Bcc: $bcc");
            }
        } else {
            $this->info("No consolidated passengers reports found for date $dateReport");
        }
    }
}
