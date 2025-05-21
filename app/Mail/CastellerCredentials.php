<?php

namespace App\Mail;

use App\Casteller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CastellerCredentials extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Casteller $casteller;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Casteller $casteller)
    {
        $this->casteller = $casteller;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.credentials');
    }
}
