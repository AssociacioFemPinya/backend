<?php

namespace App\Providers;

use App\Casteller;
use App\Event;
use App\Notification;
use App\Policies\BoardPolicy;
use App\Policies\CastellerPolicy;
use App\Policies\EventPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\ScheduledNotificationPolicy;
use App\Policies\TagPolicy;
use App\ScheduledNotification;
use App\Tag;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Casteller::class => CastellerPolicy::class,
        Tag::class => TagPolicy::class,
        Event::class => EventPolicy::class,
        Board::class => BoardPolicy::class,
        Notification::class => NotificationPolicy::class,
        ScheduledNotification::class => ScheduledNotificationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "Super-Admin" role all permission checks using can()
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Super-Admin')) {
                return true;
            }
        });

    }
}
