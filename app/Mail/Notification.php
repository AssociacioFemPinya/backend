<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $notification_message;

    public string $notification_subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $message, string $subject)
    {
        $this->notification_message = $message;
        $this->notification_subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->notification_subject)->view('emails.notification');
    }
}
