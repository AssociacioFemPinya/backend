<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Casteller;
use App\Enums\NotificationStateEnum;
use App\NotificationOrder;
use App\Services\TelegramNotificator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//use Spatie\RateLimitedMiddleware\RateLimited; # DISABLED. Only works with redis

class TelegramNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private NotificationOrder $notificationOrder;

    private string $message;

    private Casteller $casteller;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(NotificationOrder $notificationOrder, Casteller $casteller, string $message)
    {
        $this->message = $message;
        $this->casteller = $casteller;
        $this->notificationOrder = $notificationOrder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->casteller->getCastellerTelegram() == null) {
            $this->fail('Casteller does not have telegram configured');

            return;
        }

        $telegramNotificator = new TelegramNotificator();
        $error = $telegramNotificator->send($this->casteller, $this->message);
        if ($error) {
            $this->fail('Failed to deliver telegram message');

            return;
        }
        $this->notificationOrder->logs()->create([
            'channel' => 'Telegram',
            'status' => NotificationStateEnum::SENT,
        ]);
    }

    // DISABLED. Only works with redis
    // public function middleware()
    // {
    //     $rateLimitedMiddleware = (new RateLimited())
    //         ->allow(20)
    //         ->everySeconds(1)
    //         ->releaseAfterSeconds(1);

    //     return [$rateLimitedMiddleware];
    // }

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
            'channel' => 'Telegram',
            'status' => NotificationStateEnum::FAILED,
        ]);
    }
}
