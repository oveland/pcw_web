<?php

namespace App\Console\Commands;

use App\Models\Company\Company;
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
    protected $signature = 'send-mail:consolidated {--company=14} {--prev-days=1} {--date=} {--prod=}';

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
        $company = Company::find($this->option('company'));

        if ($company) {
            $prevDays = $this->option('prev-days');

            if ($this->option('date')) $dateReport = $this->option('date');
            else $dateReport = Carbon::now()->subDay($prevDays)->toDateString();

            $this->info("CONSOLIDATED ROUTES: $company->name > $dateReport");

            $mail = new ConsolidatedReportMail($company, $dateReport);
            if ($mail->buildReport()) {
                if ($this->option('prod')) {
                    $this->info("Sending report for 'prod' case...");
                    $mailTo = ['gerencia@alameda.com.co', 'movilidad@alameda.com.co', 'jeferh@alameda.com.co'];
                    $mailToBcc = ['oiva.pcw@gmail.com', 'olatorre22@hotmail.com', 'soportenivel2pcwtecnologia@outlook.com'];
                } else {
                    $this->info("Sending report for 'test' case...");
                    $mailTo = ['oiva.pcw@gmail.com', 'soportenivel2pcwtecnologia@outlook.com'];
                    $mailToBcc = ['olatorre22@hotmail.com'];
                }

                Mail::to($mailTo, $company->name)->bcc($mailToBcc)->send($mail);

                foreach ($mailTo as $to) {
                    $this->info("   >> To: $to");
                }

                $this->info("-------------------------------------------");
                foreach ($mailToBcc as $bcc) {
                    $this->info("   >> Bcc: $bcc");
                }
            } else {
                $this->info("No reports found for date $dateReport");
            }
        } else {
            $this->info("No company found for id " . $this->option('company'));
        }
    }
}
