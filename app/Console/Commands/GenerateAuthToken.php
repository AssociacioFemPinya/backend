<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateAuthToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:token {casteller_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new api client auth token to consume our api';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $casteller_id = $this->arguments()['casteller_id'];

        // Generate a new long token
        // Be sure to import Illuminate\Support\Str at the top
        $token = Str::random(60);

        // Create a new entry with the hashed token value
        // so we don't store the token in plain text
        DB::table('auth_configs')->insert([
            'auth_token' => hash('sha256', $token),
            'casteller_id' => $casteller_id,
        ]);

        // Spit out the token so we can use it
        $this->info($token);

        return 0;
    }
}
