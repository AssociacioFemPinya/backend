<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportOldDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fempinya:import-old-db {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import OLD DB (overwrites the current DB data if existed)';

    protected string $fileDoesNotExist = " The file does not exist. Please, add the sql file on /storage/app/databases/ folder and use the file's name as parameter ";

    protected string $fileExists = 'The file exists... ';

    protected string $fileWrongExtension = ' The file has a wrong extension. Please, use a .sql file ';

    protected string $fileCorrectExtension = 'The file has the correct extension... ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $file = $this->arguments()['file'];

        $filesPath = storage_path('app/databases/'.$file);

        if (! file_exists($filesPath)) {

            $this->printError($this->fileDoesNotExist);

            return 0;
        }

        $this->newLine();
        $this->info($this->fileExists);
        $this->newLine();

        $extension = pathinfo($filesPath, PATHINFO_EXTENSION);

        if ($extension !== 'sql') {

            $this->printError($this->fileWrongExtension);

            return 0;
        }

        $this->info($this->fileCorrectExtension);
        $this->newLine();

        $oldDB = env('DB_OLD_DATABASE');

        DB::statement("DROP DATABASE IF EXISTS `{$oldDB}`");

        DB::statement("CREATE DATABASE IF NOT EXISTS `{$oldDB}`");

        DB::connection('old_db')->statement('SET foreign_key_checks=0');

        DB::connection('old_db')->unprepared(file_get_contents($filesPath));

        DB::connection('old_db')->statement('SET foreign_key_checks=1');

        $this->info('IMPORT FINISHED!');
        $this->newLine();
    }

    private function printError($string = '')
    {
        $this->newLine();
        $this->error(str_repeat(' ', strlen($string)));
        $this->error($string);
        $this->error(str_repeat(' ', strlen($string)));
        $this->newLine();

    }
}
