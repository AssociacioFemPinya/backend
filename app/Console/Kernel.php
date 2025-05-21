<?php

namespace App\Console;

use App\Managers\ScheduledNotificationsManager;
use App\Repositories\ScheduledNotificationRepository;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Function to process ready scheduled notifications
        $schedule->call(function () {
            $manager = new ScheduledNotificationsManager(new ScheduledNotificationRepository());
            $manager->processReadyNotification();
        })->everyMinute();
        // $schedule->command('inspire')
        //          ->hourly();
        if (env('TELESCOPE_ENABLED', false)) {
            $schedule->command('telescope:prune --hours=24')->hourly();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
