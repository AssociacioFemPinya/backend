<?php

namespace App\Console\Commands;

use App\CastellerConfig;
use App\Colla;
use App\Managers\AttendanceManager;
use App\Managers\CastellersManager;
use App\Managers\EventsManager;
use App\Managers\TagsManager;
use App\Repositories\AttendanceRepository;
use App\Repositories\CastellerConfigRepository;
use App\Repositories\CastellerRepository;
use App\Repositories\EventRepository;
use App\Repositories\TagRepository;
use App\Tag;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Console\Command;
use Symfony\Component\HttpFoundation\ParameterBag;

class CreateDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fempinya:create-demo {id_colla}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create demo content';

    protected $translation = [
        'ca' => [
            'faker' => 'es_ES',
            'habitual' => ['habitual', 'Habitual'],
            'esporadic' => ['esporadic', 'Esporàdic'],
            'actiu' => ['actiu', 'Actiu'],
            'inactiu' => ['inactiu', 'Inactiu'],
            'soci' => ['soci-protector', 'Soci protector'],
            'graller' => ['graller', 'Graller'],
            'timbaler' => ['timbaler', 'Timbaler'],
        ],

    ];

    protected $eventTags = [
        'casa' => ['casa', 'A Casa'],
        'altra_provincia' => ['altra_provincia', 'En una altra provincia'],
        'mateixa_provincia' => ['mateixa_provincia', 'A la mateixa provincia'],
        'extranger' => ['extranger', 'A l\'extranger'],
    ];

    protected $eventAnswersAssajos = [
        '15_tard' => ['15-tard', 'Arribo 15 minuts tard'],
        '30_tard' => ['30-tard', 'Arribo 30 minuts tard'],
        '45_tard' => ['45-tard', 'Arribo 45 minuts tard'],
        '15_abans' => ['15-abans', 'Marxo 15 abans d\'acabar'],
        '30_abans' => ['30-abans', 'Marxo 30 abans d\'acabar'],
        '45_abans' => ['45-abans', 'Marxo 45 abans d\'acabar'],
    ];

    protected $eventAnswersActuacions = [
        'transport_propi' => ['transport-propi', 'Vinc en transport propi'],
        'transport_colla' => ['transport-colla', 'Vinc amb el transport que ofereix la Colla'],
        'dinar' => ['dinar', 'Em quedo a dinar'],
        'no_dinar' => ['no-dinar', 'No em quedo a dinar'],
        'sopar' => ['sopar', 'Em quedo a sopar'],
        'no_sopar' => ['no-sopar', 'No em quedo a sopar'],
    ];

    protected $cities = ['Barcelona', 'Badalona', 'Hospitalet de Llobregat', 'Sabadell', 'Terrassa', 'Valls'];

    protected $provinces = ['Barcelona', 'Tarragona', 'Lleida', 'Girona'];

    protected $comarques = ['Barcelonés', 'Baix Llobregat', 'l\'Alt Camp', 'Tarragonès', 'l\'Alt Penedès'];

    protected $diades = ['Diada', 'Festa Major', 'Aniversari'];

    protected $assajos = ['Assaigg de Dilluns', 'Assaig de Dimecres', 'Assaig de Divendres'];

    protected $activitats = ['Dinar de colla', 'Calçotada', 'Sortida a la muntanya', 'Festa'];

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
        $id_colla = $this->arguments()['id_colla'];

        $this->castellers($id_colla, 'ca');
        $this->tagsCastellers($id_colla, 'ca');
        $this->events($id_colla, 'ca');
        $this->tagsEvents($id_colla, 'ca');
        $this->attendances($id_colla, 'ca');

        $this->newLine();
        $this->info(' DEMO CREADA!');
        $this->newLine();

    }

    /** create castellers demo */
    private function castellers($id_colla, $lang)
    {
        $faker = Factory::create($this->translation[$lang]['faker']);

        $total = $faker->numberBetween(100, 200);

        $colla = Colla::find($id_colla);

        $this->newLine();
        $this->line('----------------------------');
        $this->line('CREANT '.$total.' CASTELLERS...');
        $this->line('----------------------------');
        $this->newLine();

        $bar = $this->output->createProgressBar($total);

        $bar->start();

        for ($i = 1; $i <= $total; $i++) {
            $parametersBag = new ParameterBag();

            $casteller = [];

            $casteller['colla_id'] = $id_colla;

            $casteller['gender'] = $faker->numberBetween(0, 1);
            if ($casteller['gender'] > 0) {
                $casteller['name'] = $faker->firstName('male');
            } else {
                $casteller['name'] = $faker->firstName('female');
            }
            $casteller['alias'] = $casteller['name'].'_'.$faker->regexify('[A-Za-z0-9]{3}');

            $casteller['last_name'] = $faker->lastName().' '.$faker->lastName();
            // No requerid
            $casteller['email'] = $faker->safeEmail();
            $casteller['email2'] = null;
            $casteller['national_id_type'] = 'dni';
            $casteller['nationality'] = 'nationality';
            $casteller['national_id_number'] = $faker->dni();
            $casteller['birthdate'] = $faker->dateTimeThisCentury()->format('d/m/Y');
            $casteller['mobile_phone'] = $faker->phoneNumber();
            $casteller['phone'] = $faker->phoneNumber();
            $casteller['emergency_phone'] = $faker->phoneNumber();
            $casteller['address'] = $faker->streetAddress();
            $casteller['city'] = $faker->randomElement($this->cities);
            $casteller['postal_code'] = '080'.$faker->numberBetween(10, 42);
            $casteller['province'] = $faker->randomElement($this->provinces);
            $casteller['comarca'] = $faker->randomElement($this->comarques);
            $casteller['country'] = null;
            $casteller['comments'] = null;
            $casteller['weight'] = $faker->numberBetween(50, 100);
            $casteller['height'] = $faker->numberBetween(130, 170);
            $casteller['status'] = $faker->numberBetween(1, 4);

            $parametersBag->add($casteller);

            $castellerManager = new CastellersManager(new CastellerRepository(), new CastellerConfigRepository);
            $newCasteller = $castellerManager->createCasteller($colla, $parametersBag);

            // create Telegram Config
            $castellerConfig = CastellerConfig::where('casteller_id', $newCasteller->getId())->get()->first();
            $castellerConfig->telegram_token = $faker->regexify('[A-Za-z0-9]{8}');
            $castellerConfig->save();

            $bar->advance();

        }

        $bar->finish();

        $this->newLine(2);

    }

    /** put tags in castellers demo */
    private function tagsCastellers($id_colla, $lang)
    {

        $this->newLine();
        $this->line('----------------------------');
        $this->line('ASSIGNANT TAGS ALS CASTELLERS...');
        $this->line('----------------------------');
        $this->newLine();

        $tagManager = new TagsManager(new TagRepository());
        $faker = Factory::create($this->translation[$lang]['faker']);
        $colla = Colla::find($id_colla);
        $castellers = $colla->getCastellers();

        //create new tags
        $new_tags[1] = [
            $this->translation[$lang]['habitual'][0] => $this->translation[$lang]['habitual'][1],
            $this->translation[$lang]['esporadic'][0] => $this->translation[$lang]['esporadic'][1],
        ];
        $new_tags[2] = [
            $this->translation[$lang]['actiu'][0] => $this->translation[$lang]['actiu'][1],
            $this->translation[$lang]['inactiu'][0] => $this->translation[$lang]['inactiu'][1],
            $this->translation[$lang]['soci'][0] => $this->translation[$lang]['soci'][1],
            $this->translation[$lang]['graller'][0] => $this->translation[$lang]['graller'][1],
            $this->translation[$lang]['timbaler'][0] => $this->translation[$lang]['timbaler'][1],
        ];

        foreach ($new_tags as $id_group => $group) {
            foreach ($new_tags[$id_group] as $k => $v) {
                $parametersBag = new ParameterBag();
                $tag = [];
                $tag['name'] = $v;
                $tag['value'] = $k;
                $tag['type'] = 'CASTELLERS';
                $tag['group'] = $id_group;
                $tag['colla_id'] = $id_colla;
                $parametersBag->add($tag);
                $newTag = $tagManager->createTag($colla, $parametersBag);
            }
        }

        $tags = Tag::where('colla_id', $id_colla)->where('type', 'CASTELLERS')
            ->where('value', '!=', 'esporadic')
            ->where('value', '!=', 'habitual')
            ->get();

        $esporadics = $colla->castellers()->orderByRaw('RAND()')->take($faker->numberBetween(1, count($castellers)))->get();
        $esporadicsIds = $esporadics->pluck('id_casteller');
        $habituals = $castellers->whereNotIn('id_casteller', $esporadicsIds)->take($faker->numberBetween(1, (count($castellers) - count($esporadicsIds))));

        $totalEsporadics = count($esporadics);

        $tag_esporadic = $colla->tags()->where('value', 'esporadic')->first();

        $this->line('Assignant '.$totalEsporadics.' Esporadics...');
        $this->newLine();

        $bar = $this->output->createProgressBar($totalEsporadics);

        $bar->start();

        foreach ($esporadics as $esporadic) {
            $esporadic->tags()->attach($tag_esporadic);
            $bar->advance();

        }

        $bar->finish();

        $this->newLine();

        $this->newLine();
        $this->line('Assignant '.count($habituals).' Habituals...');
        $this->newLine();

        $tag_habitual = $colla->tags()->where('value', 'habitual')->first();

        $bar = $this->output->createProgressBar(count($habituals));

        $bar->start();

        foreach ($habituals as $habitual) {
            $habitual->tags()->attach($tag_habitual);
            $bar->advance();

        }

        $bar->finish();

        $this->newLine();

        $this->newLine();
        $this->line('Assignant Altres tags...');
        $this->newLine();

        foreach ($tags as $tag) {
            $this->line($tag->getName());

            $bar->start();

            $castellers = $colla->castellers()->orderByRaw('RAND()')->take($faker->numberBetween(10, 75))->get();

            $bar = $this->output->createProgressBar(count($castellers));

            foreach ($castellers as $casteller) {
                $casteller->tags()->save($tag);
                $bar->advance();

            }
            $bar->finish();

            $this->newLine(2);
        }

        $positions = ['lateral' => ['lateral', 'Lateral'],
            'vent' => ['vent', 'Vent'],
            'mans' => ['mans', 'Mans'],
            'agulla' => ['agulla', 'Agulla'],
            'crossa' => ['crossa', 'Crossa'],
            'contrafort' => ['contrafort', 'Contrafort'],
            'tronc' => ['tronc', 'Tronc'],
            'canalla' => ['canalla', 'Canalla']];

        foreach ($positions as $position) {
            $parametersBag = new ParameterBag();
            $tag = [];
            $tag['name'] = $position[1];
            $tag['value'] = $position[0];
            $tag['type'] = 'POSITIONS';
            $tag['group'] = null;
            $tag['colla_id'] = $id_colla;
            $parametersBag->add($tag);
            $newTag = $tagManager->createTag($colla, $parametersBag);
        }

        $castellers = $colla->getCastellers();
        $positions = $colla->tags()->where('type', 'POSITIONS')->get()->toArray();

        $this->newLine();
        $this->line('Assignant Posicions...');
        $this->newLine();

        $bar = $this->output->createProgressBar(count($castellers));

        $bar->start();

        foreach ($castellers as $casteller) {
            $key = array_rand($positions, 1);

            $casteller->tags()->attach($positions[$key]['id_tag']);

            $bar->advance();

        }

        $bar->finish();

        $this->newLine(2);

    }

    /** create events demo */
    private function events($id_colla, $lang)
    {

        $faker = Factory::create($this->translation[$lang]['faker']);

        $total = $faker->numberBetween(30, 50);

        $colla = Colla::find($id_colla);

        $this->newLine();
        $this->line('----------------------------');
        $this->line('CREANT '.$total.' Esdeveniments...');
        $this->line('----------------------------');
        $this->newLine();

        $bar = $this->output->createProgressBar($total);

        $bar->start();

        for ($i = 1; $i <= $total; $i++) {

            $parametersBag = new ParameterBag();

            $event = [];

            $event['colla_id'] = $id_colla;
            $event['address'] = $faker->streetAddress().', '.$faker->randomElement($this->cities).', '.$faker->randomElement($this->provinces);
            $event['duration'] = $faker->numberBetween(1, 160);
            $event['companions'] = $faker->numberBetween(0, 1);
            $event['visibility'] = $faker->numberBetween(0, 1);

            // TYPE
            $event['type'] = $faker->numberBetween(1, 3);
            if ($event['type'] == 1) {
                $event['name'] = $faker->randomElement($this->assajos);
            } elseif ($event['type'] == 2) {
                $event['name'] = $faker->randomElement($this->diades);
            } else {
                $event['name'] = $faker->randomElement($this->activitats);
            }

            // DATES
            $event['start_date'] = $faker->dateTimeThisYear('+8 months');
            $startDate = Carbon::parse($event['start_date']);
            $event['open_date'] = $faker->dateTimeBetween($startDate->subDays($faker->numberBetween(7, 30)), $event['start_date']);
            $event['close_date'] = $faker->dateTimeBetween($event['open_date'], $event['start_date']);

            // Obertura Immmediata
            $number = $faker->numberBetween(1, 10);
            if ($number == 5) {
                $event['open_date'] = Carbon::today();
            }

            $parametersBag->add($event);

            $eventsManager = new EventsManager(new EventRepository());
            $newEvent = $eventsManager->createEvent($colla, $parametersBag);
            $bar->advance();

        }

        $bar->finish();

        $this->newLine(2);
    }

    /** put tags in events demo */
    private function tagsEvents($id_colla, $lang)
    {

        $this->newLine();
        $this->line('----------------------------');
        $this->line('ASSIGNANT TAGS DELS ESDEVENIMENTS...');
        $this->line('----------------------------');
        $this->newLine();

        $tagManager = new TagsManager(new TagRepository());
        $faker = Factory::create($this->translation[$lang]['faker']);
        $colla = Colla::find($id_colla);

        $addedEventTags = [];
        foreach ($this->eventTags as $eventTag) {
            $parametersBag = new ParameterBag();
            $tag = [];
            $tag['name'] = $eventTag[1];
            $tag['value'] = $eventTag[0];
            $tag['type'] = 'EVENTS';
            $tag['group'] = null;
            $tag['colla_id'] = $id_colla;
            $parametersBag->add($tag);
            $addedEventTags[] = $tagManager->createTag($colla, $parametersBag);
        }

        $eventAnswersActuacionsTags = [];
        foreach ($this->eventAnswersActuacions as $eventAnswer) {
            $parametersBag = new ParameterBag();
            $tag = [];
            $tag['name'] = $eventAnswer[1];
            $tag['value'] = $eventAnswer[0];
            $tag['type'] = 'ATTENDANCE';
            $tag['group'] = null;
            $tag['colla_id'] = $id_colla;
            $parametersBag->add($tag);
            $eventAnswersActuacionsTags[] = $tagManager->createTag($colla, $parametersBag);
        }

        $eventAnswersAssajosTags = [];
        foreach ($this->eventAnswersAssajos as $eventAnswer) {
            $parametersBag = new ParameterBag();
            $tag = [];
            $tag['name'] = $eventAnswer[1];
            $tag['value'] = $eventAnswer[0];
            $tag['type'] = 'ATTENDANCE';
            $tag['group'] = null;
            $tag['colla_id'] = $id_colla;
            $parametersBag->add($tag);
            $eventAnswersAssajosTags[] = $tagManager->createTag($colla, $parametersBag);
        }

        $allEvents = $colla->getEvents();
        $assajos = $colla->events()->where('type', 1)->get();
        $actuacions = $colla->events()->where('type', 2)->get();

        $this->newLine();
        $this->line('Assignant Tags...');
        $this->newLine();

        $bar = $this->output->createProgressBar(count($allEvents));

        $bar->start();

        foreach ($allEvents as $event) {
            $key = array_rand($addedEventTags, 1);
            $event->tags()->attach($addedEventTags[$key]['id_tag']);
            $bar->advance();
        }

        $bar->finish();

        $this->newLine();

        $this->newLine();
        $this->line('Assignant Answers als assajos...');
        $this->newLine();

        $bar = $this->output->createProgressBar(count($assajos));

        $bar->start();

        foreach ($assajos as $assaig) {
            foreach ($eventAnswersAssajosTags as $eventAnswersAssajosTag) {
                $assaig->attendanceAnswers()->attach($eventAnswersAssajosTag->getId());
                //$assaig->tags()->attach(array_column($eventAnswersAssajosTags, 'tag_id'));
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine();

        $this->newLine();
        $this->line('Assignant Answers a les Actuacions...');
        $this->newLine();

        $bar = $this->output->createProgressBar(count($actuacions));

        $bar->start();

        foreach ($actuacions as $actuacio) {
            foreach ($eventAnswersActuacionsTags as $eventAnswersActuacionsTag) {
                $actuacio->attendanceAnswers()->attach($eventAnswersActuacionsTag->getId());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

    }

    /** create events demo */
    private function attendances($id_colla, $lang)
    {

        $faker = Factory::create($this->translation[$lang]['faker']);

        $colla = Colla::find($id_colla);

        $events = $colla->getEvents();
        $castellers = $colla->getCastellers();

        $this->newLine();
        $this->line('----------------------------');
        $this->line('ASSIGNANT ASSISTÈNCIA a '.count($events).' ESDEVENIMENTS...');
        $this->line('----------------------------');
        $this->newLine();

        $bar = $this->output->createProgressBar(count($events));

        $bar->start();

        foreach ($events as $event) {

            $answers = $event->getAttendanceAnswers()->toArray();

            // DEIXEM SENSE ASSISTÈNCIA ALMENYS A UN TERÇ DELS CASTELLERS
            $castellersTaken = $colla->castellers()->take($faker->numberBetween(((count($castellers) * 33) / 100), count($castellers)))->get();

            foreach ($castellersTaken as $casteller) {
                $parametersBag = new ParameterBag();

                $attendance = [];

                $attendance['status'] = $faker->numberBetween(1, 3);

                if ($attendance['status'] === 1) {
                    $attendance['options'] = (! empty($answers)) ? json_encode($faker->randomElements(array_column($answers, 'id_tag'))) : null;
                    $attendance['companions'] = ($event->getCompanions()) ? $faker->numberBetween(0, 5) : null;
                }

                $parametersBag->add($attendance);

                $attendancesManager = new AttendanceManager(new AttendanceRepository());
                $newEvent = $attendancesManager->createAttendance($casteller->getId(), $event->getId(), $parametersBag);

            }

            $bar->advance();

        }

        $bar->finish();

        $this->newLine(2);
    }
}
