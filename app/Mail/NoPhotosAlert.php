<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NoPhotosAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $despachosSinFotos;


    public function __construct($despachosSinFotos)
    {
        $this->despachosSinFotos = $despachosSinFotos;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Alerta: Despachos sin fotos detectados')
            ->view('email.no_photos_alert')
            ->with(['despachos' => $this->despachosSinFotos]);
    }
}