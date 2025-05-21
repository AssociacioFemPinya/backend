<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Casteller;
use App\Enums\NotificationStateEnum;
use App\Mail\Notification as NotificationMail;
use App\Notification;
use App\NotificationOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private NotificationOrder $notificationOrder;

    private string $message;

    private Casteller $casteller;

    private Notification $notification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(NotificationOrder $notificationOrder, Casteller $casteller, Notification $notification, string $message)
    {
        $this->message = $message;
        $this->casteller = $casteller;
        $this->notificationOrder = $notificationOrder;
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->casteller->getEmail())->send(new NotificationMail($this->message, $this->notification->getTitle()));

        $this->notificationOrder->logs()->create([
            'channel' => 'Mail',
            'status' => NotificationStateEnum::SENT,
        ]);
    }

    /*
    * Determine the time at which the job should timeout.
    *
    */
    public function retryUntil(): \DateTime
    {
        return now()->addDay();
    }

    /**
     * Handle error during the execution.
     */
    public function failed(string $exception): void
    {
        $this->notificationOrder->logs()->create([
            'channel' => 'Mail',
            'status' => NotificationStateEnum::FAILED,
        ]);
    }
}
