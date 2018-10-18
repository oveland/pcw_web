<?php

namespace App\Console\Commands;

use App\Company;
use App\Mail\ConsolidatedReportMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Mail;

class ConsolidatedReportMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-mail:consolidated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends consolidated report mail';

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
        $company = Company::find(12);
        $prevDays = 1;

        $dateReport = Carbon::now()->subDay($prevDays)->toDateString();

        $mail = new ConsolidatedReportMail($company, $dateReport);
        if ($mail->buildReport()) {
            Mail::to('olatorre22@hotmail.com', $company->name)
                ->cc('oscarivelan@gmail.com')
                ->send($mail);
            $this->info("$company->name Mail send for date $dateReport!");
        } else {
            $this->info("No reports found for date $dateReport");
        }
    }
}
