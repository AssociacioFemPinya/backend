<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Casteller;
use App\Enums\NotificationStateEnum;
use App\Notification;
use App\NotificationOrder;
use App\Services\FirebaseNotificator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FirebaseNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private NotificationOrder $notificationOrder;

    private string $firebasePayload;

    private Casteller $casteller;

    private Notification $notification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(NotificationOrder $notificationOrder, Casteller $casteller, Notification $notification, string $firebasePayload)
    {
        $this->firebasePayload = $firebasePayload;
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
        $firebaseNotificator = new FirebaseNotificator();
        $error = $firebaseNotificator->sendPayload(
            $this->casteller,
            $this->firebasePayload
        );

        if ($error) {
            $this->fail('Failed to deliver Firebase notification');

            return;
        }

        $this->notificationOrder->logs()->create([
            'channel' => 'Firebase',
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
            'channel' => 'Firebase',
            'status' => NotificationStateEnum::FAILED,
        ]);
    }
}
