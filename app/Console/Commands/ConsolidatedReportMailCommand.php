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

            $this->logData("CONSOLIDATED ROUTES: $company->name > $dateReport");

            $mail = new ConsolidatedReportMail($company, $dateReport);
            if ($mail->buildReport()) {
                $mail->setProduction($this->option('prod'));
                $mailTo = $this->getMailToFromCompany($company, $this->option('prod'));

                Mail::to($mailTo, $company->name)->send($mail);

                foreach ($mailTo as $to) {
                    $this->logData("   >> To: $to");
                }
            } else {
                $this->logData("No reports found for date $dateReport", 'error');
            }
        } else {
            $this->logData("No company found for id " . $this->option('company'), 'error');
        }
    }

    public function logData($message, $level = 'info')
    {
        $message = "CONSOLIDATED ROUTE > $message";

        switch ($level) {
            case 'warning':
                \Log::warning($message);
                break;
            case 'error':
                \Log::error($message);
                break;
            default:
                \Log::info($message);
                break;
        }
    }

    /**
     * @param Company $company
     * @param bool $production
     * @return array
     */
    public function getMailToFromCompany(Company $company, $production = false)
    {
        $this->logData("Making mail route report for '" . ($production ? 'production' : 'development') . "' case...");

        switch ($company->id) {
            case 14:
                if ($production) {
                    $mailTo = ['gerencia@alameda.com.co', 'movilidad@alameda.com.co', 'jeferh@alameda.com.co', 'oiva.pcw@gmail.com'];
                } else {
                    $mailTo = ['oiva.pcw@gmail.com', 'soportenivel2pcwtecnologia@outlook.com'];
                }
                break;
            case 28:
                if ($production) {
                    $mailTo = ['Migui_213@hotmail.com', 'olatorre22@hotmail.com', 'oiva.pcw@gmail.com'];
                } else {
                    $mailTo = ['oiva.fz@gmail.com'];
                }
                break;
            default:
                $mailTo = ['oiva.pcw@gmail.com'];
                break;
        }

        return $mailTo;
    }
}
