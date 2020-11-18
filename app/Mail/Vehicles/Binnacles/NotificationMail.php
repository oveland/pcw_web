<?php

namespace App\Mail\Vehicles\Binnacles;

use App\Models\Vehicles\Binnacles\Binnacle;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Binnacle[]
     */
    private $binnacles;

    /**
     * Create a new message instance.
     *
     * @param $binnacles
     */
    public function __construct($binnacles)
    {
        $this->binnacles = $binnacles;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $binnacles = $this->binnacles;
        return $this->view('operation.vehicles.binnacle.mails.notification', compact('binnacles'))->subject(__('Vehicle notifications'));
    }
}
