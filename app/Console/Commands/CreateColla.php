<?php

namespace App\Console\Commands;

use App\Helpers\Humans;
use App\Managers\CollesManager;
use App\Managers\UsersManager;
use App\Repositories\CollaConfigRepository;
use App\Repositories\CollaRepository;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\ParameterBag;

class CreateColla extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fempinya:create-colla {name} {email} {password} {--super-admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create colla. --super-admin optional flag';

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

        $input['name'] = $this->arguments()['name'];
        $input['email'] = $this->arguments()['email'];
        $input['password'] = $this->arguments()['password'];
        $input['isSuperAdmin'] = $this->options()['super-admin'] ?? 0;

        $validator = Validator::make($input, [
            'email' => 'required|email:rfc|unique:users,email',
            'name' => 'required|max:255|min:3',
            'password' => [
                'required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->symbols()
                    ->numbers(),
            ],
        ]);

        if ($validator->fails()) {
            $errorsString = implode("\n", $validator->errors()->all());
            $this->printInfo($errorsString);
            $this->printError('Failed due to some errors on the input data');
        }

        $parametersBag = new ParameterBag();

        $colla = [];

        $colla['name'] = $input['name'];
        $colla['shortname'] = Humans::replaceSpecialCharacters($input['name']);
        $colla['email'] = $input['email'];
        $colla['phone'] = 666;

        $parametersBag->add($colla);

        $collesManager = new CollesManager(new CollaRepository(), new CollaConfigRepository());
        $newColla = $collesManager->createColla($parametersBag);

        $parametersBag = new ParameterBag();

        $user = [];
        $user['colla_id'] = $newColla->getId();
        $user['name'] = $input['name'];
        $user['email'] = $input['email'];
        $user['language'] = 'ca';
        $user['password'] = $input['password'];

        $parametersBag->add($user);

        $usersManager = new UsersManager(new UserRepository());
        $newUser = $usersManager->createUser($newColla, $parametersBag);

        if ($input['isSuperAdmin']) {
            $newUser->syncRoles('Super-Admin');
        } else {
            $newUser->syncRoles('Colla-Admin');
        }

        $this->newLine();
        $this->info(' COLLA CREADA!');
        $this->newLine();

    }

    private function printError($string = '')
    {
        $this->newLine();
        $this->error(str_repeat(' ', strlen($string)));
        $this->error($string);
        $this->error(str_repeat(' ', strlen($string)));
        $this->newLine();
        exit;
    }

    private function printCorrectInfo($string = '')
    {
        $this->newLine();
        $this->info($string);
        $this->newLine();
    }

    private function printInfo($string = '')
    {
        $this->newLine();
        $this->line($string);
        $this->newLine();
    }
}
