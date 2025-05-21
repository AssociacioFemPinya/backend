<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateAllFresh extends Command
{
    protected $signature = 'migrate:all-fresh';

    protected $description = 'Run migrate:fresh on all databases';

    public function handle()
    {
        // List of database connections
        $connections = ['mysql', 'telescope'];

        foreach ($connections as $connection) {
            $this->info("Running migrate:fresh on connection: {$connection}");
            // Set the database connection for the migrations
            Artisan::call('migrate:fresh', ['--database' => $connection, '--force' => true]);
            // Run the seeder for the specific connection if needed
            Artisan::call('db:seed', ['--class' => 'PermissionsSeeder', '--database' => $connection, '--force' => true]);
        }

        $this->info('All migrations refreshed and seeded.');
    }
}
