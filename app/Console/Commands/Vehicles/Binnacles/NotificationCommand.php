<?php

namespace App\Console\Commands\Vehicles\Binnacles;

use App\Mail\Vehicles\Binnacles\NotificationMail;
use App\Models\Company\Company;
use App\Services\Operation\Vehicles\BinnacleService;
use Illuminate\Console\Command;
use Mail;

class NotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicles:binnacle:notify {--company=14}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail notifications for binnacle of vehicles with expiration date current';

    /**
     * @var BinnacleService
     */
    private $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->service = new BinnacleService();
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
            $notificationsByUsers = $this->service->notificationsByUsers($company);

            foreach ($notificationsByUsers as $notificationsByUser) {
                $user = $notificationsByUser->user;
                $binnacles = $notificationsByUser->binnacles;
                $notifications = $notificationsByUser->notifications;

                $mail = new NotificationMail($binnacles);
                $mailTo = $user->email;

                $mailTo = 'oiva.pcw@gmail.com';

                $rta = Mail::to($mailTo)->send($mail);
            }
        } else {
            $this->info('Company not found');
        }
    }
}
