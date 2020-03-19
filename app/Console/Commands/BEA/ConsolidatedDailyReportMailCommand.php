<?php

namespace App\Console\Commands\BEA;

use App\Mail\BEA\ConsolidatedDailyReportMail;
use App\Models\Company\Company;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Log;

class ConsolidatedDailyReportMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-mail-bea:daily {--company=30} {--prev-days=1} {--date=} {--prod=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends daily report BEA mail';

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

            $this->logData("$company->name > $dateReport");

            $mail = new ConsolidatedDailyReportMail($company, $dateReport);
            if ($mail->buildReport()) {
                $mail->setProduction($this->option('prod'));
                $mailTo = $this->getMailToFromCompany($company, $this->option('prod'));

                Mail::to($mailTo, $company->name)->send($mail);

                foreach ($mailTo as $to) {
                    $this->logData("   >> To: $to");
                }
            } else {
                $this->logData("No reports BEA found for date $dateReport", 'error');
            }
        } else {
            $this->logData("No company found for id " . $this->option('company'), 'error');
        }
    }

    public function logData($message, $level = 'info')
    {
        $message = "BEA CONSOLIDATED DAILY > $message";

        $this->info($message);

        switch ($level) {
            case 'warning':
                Log::warning($message);
                break;
            case 'error':
                Log::error($message);
                break;
            default:
                Log::info($message);
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
        $this->logData("Making mail daily report BEA for '" . ($production ? 'production' : 'development') . "' case...");

        switch ($company->id) {
            case Company::COODETRANS:
                if ($production) {
                    $mailTo = ['oiva.pcw@gmail.com'];
                } else {
                    $mailTo = ['oiva.pcw@gmail.com'];
                }
                break;
            default:
                if ($production) {
                    $mailTo = ['oiva.pcw@gmail.com'];
                } else {
                    $mailTo = ['oiva.fz@gmail.com'];
                }
                break;
        }

        return $mailTo;
    }
}
