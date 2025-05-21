<?php

namespace App\Providers;

use App\CastellerConfig;
use App\Events\NotificationReady;
use App\NotificationOrder;
use App\Observers\CastellerConfigObserver;
use App\Observers\EventObserver;
use App\Observers\NotificationObserver;
use App\Observers\NotificationOrderObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        CastellerConfig::observe(CastellerConfigObserver::class);
        NotificationOrder::observe(NotificationOrderObserver::class);
        \App\Event::observe(EventObserver::class);

        Event::listen(NotificationReady::class, [NotificationObserver::class, 'ready']);
    }
}
