<?php

namespace App\Console\Commands;

use App\Colla;
use App\Enums\TypeTags;
use App\Services\ImportService;
use App\Tag;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;
use Symfony\Component\HttpFoundation\ParameterBag;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fempinya:import {colla=0} {--all} {--colla} {--castellers} {--castellers-personal} {--events} {--attendances} {--confirm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate OLD DB to new environment.';

    protected string $parameterNotNumeric = " The parameter is NOT a numeric value. You must use Colla's ID ";

    protected string $segurTotesColles = " Are you sure you want to import ALL the Colles? If not, please, set the Colla's ID as a parameter ";

    protected string $failedConnection = ' The connection with the old Database failed. Be sure the connection details are correct on .env and the DB exists ';

    protected string $tableDoesNotExist = " Table 'fp_user' does not exist. Are you sure you have imported the correct DB? ";

    protected string $collaDoesNotExist = ' The selected Colla does not exist. Are you sure you have used the correct ID? ';

    protected string $duplicatedAlias = ' MIGRATION CANCELED: Some duplicated Alias found on the old DB: ';

    protected string $duplicatedEvent = ' DUPLICATED EVENTS FOUND: Some events have been renamed: ';

    protected string $duplicatedAttendance = ' DUPLICATED ATTENDANCES FOUND AND REMOVED: ';

    protected bool $confirmOption = false;

    protected bool $allOption = false;

    protected bool $collaOption = false;

    protected bool $castellersOption = false;

    protected bool $castellersPersonalOption = false;

    protected bool $eventsOption = false;

    protected bool $attendancesOption = false;

    protected ImportService $importService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ImportService $importService)
    {
        parent::__construct();

        $this->importService = $importService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // ID OF THE COLLA WHICH DATA SHOULD BE IMPORTED FROM. DEFAULT = 0 -> ALL COLLES
        $collaParameter = $this->argument('colla');

        $this->validateParameter($collaParameter);
        $this->validateOptions($this->options());
        $this->validateDatabase();

        $collaID = intval($collaParameter);

        if ($collaID === 0) {
            if ($this->confirm($this->segurTotesColles, false)) {

                $colles = DB::connection('old_db')->select('select * from fp_user');

                foreach ($colles as $colla) {
                    $this->import($colla->id);
                }
            }
        } else {
            $this->import($collaID);
        }

        $string = ' IMPORT TASK FINISHED! ';
        $this->newLine();
        $this->info(str_repeat('-', strlen($string)));
        $this->printCorrectInfo($string);
        $this->info(str_repeat('-', strlen($string)));

        return 1;
    }

    private function import(int $collaID)
    {

        $collaInfo = $this->validateOldCollaExists($collaID);
        $castellers = $this->validateCastellers($collaInfo);
        $events = $this->validateEvents($collaInfo);
        $attendances = $this->validateAttendance($collaInfo);

        if (! $castellers['valid']) {
            exit;
        }

        if ($this->allOption || $this->collaOption) {
            $this->importColla($collaInfo);
        }

        if ($this->allOption || $this->castellersOption) {
            $this->importCastellers($collaInfo, $castellers['data']);
        }

        if ($this->allOption || $this->castellersPersonalOption) {
            $this->importCastellersPersonal($collaInfo, $castellers['data']);
        }

        if ($this->allOption || $this->eventsOption) {
            $this->importEvents($collaInfo, $events);
        }

        if ($this->allOption || $this->attendancesOption) {
            $this->importAttendances($collaInfo, $attendances);
        }

    }

    private function importColla(StdClass $collaInfo)
    {

        if (! $this->confirmOption) {
            if ($this->confirm('Are You sure you want to import the Colla called "'.$collaInfo->nombre.'" ? ', true)) {
                $parametersBag = $this->getCollaParameters($collaInfo);
                $this->importService->importColla($parametersBag);
            }
        } else {
            $parametersBag = $this->getCollaParameters($collaInfo);
            $this->importService->importColla($parametersBag);
        }

        $this->info(' Import of "'.$collaInfo->nombre.'" FINISHED!');

    }

    private function importCastellers(StdClass $collaInfo, array $castellers)
    {

        $colla = $this->validateNewCollaExists($collaInfo->id);

        if (! $this->confirmOption) {
            if ($this->confirm('Are You sure you want to import the Castellers from the Colla called "'.$collaInfo->nombre.' ? "', true)) {
                $this->importCastellersPositions($collaInfo, $colla);
                $this->importCastellersMembers($collaInfo, $colla, $castellers);
            }
        } else {
            $this->importCastellersPositions($collaInfo, $colla);
            $this->importCastellersMembers($collaInfo, $colla, $castellers);
        }

    }

    private function importCastellersPersonal(StdClass $collaInfo, array $castellers)
    {

        $colla = $this->validateNewCollaExists($collaInfo->id);

        if (! $this->confirmOption) {
            if ($this->confirm('Are You sure you want to import the Personla info of the Castellers from the Colla called "'.$collaInfo->nombre.' ? "', true)) {
                $this->importCastellersPersonalData($collaInfo, $colla, $castellers);
            }
        } else {
            $this->importCastellersPersonalData($collaInfo, $colla, $castellers);
        }

    }

    private function importEvents(StdClass $collaInfo, array $events)
    {

        $colla = $this->validateNewCollaExists($collaInfo->id);

        if (! $this->confirmOption) {
            if ($this->confirm('Are You sure you want to import the Events from the Colla called "'.$collaInfo->nombre.' ? "', true)) {
                $this->importEventsCollection($collaInfo, $colla, $events);
            }
        } else {
            $this->importEventsCollection($collaInfo, $colla, $events);

        }

    }

    private function importAttendances(StdClass $collaInfo, array $attendances)
    {

        $colla = $this->validateNewCollaExists($collaInfo->id);

        if (! $this->confirmOption) {
            if ($this->confirm('Are You sure you want to import the Attendances from the Colla called "'.$collaInfo->nombre.' ? "', true)) {
                $this->importAttendancesCollection($collaInfo, $colla, $attendances);
            }
        } else {
            $this->importAttendancesCollection($collaInfo, $colla, $attendances);

        }

    }

    private function importAttendancesCollection(StdClass $collaInfo, Colla $colla, array $attendances)
    {

        $castellers = $colla->getCastellers();
        $events = $colla->getEvents();

        $this->printInfo(' Importing '.count($attendances).' attendances from '.count($castellers).' castellers related to '.count($events).' events.');

        $attendancesParameterBagArray = [];

        $bar = $this->output->createProgressBar(count($attendances));

        $bar->start();

        foreach ($attendances as $attendanceInfo) {
            $attendancesParameterBagArray[] = $this->getAttendanceParameters($attendanceInfo, $castellers, $events);
            $bar->advance();
        }
        $bar->finish();

        $this->importService->importAttendances($attendancesParameterBagArray, $colla);
        $this->info(' Import of the Attendances from colla "'.$colla->getShortName().'" FINISHED!');

    }

    private function importEventsCollection(StdClass $collaInfo, Colla $colla, array $events)
    {

        $eventsParameterBagArray = [];

        foreach ($events as $eventInfo) {
            $eventsParameterBagArray[] = $this->getEventParameters($eventInfo);
        }

        $this->importService->importEvents($eventsParameterBagArray, $colla);
        $this->info(' Import of the Events from colla "'.$colla->getShortName().'" FINISHED!');

    }

    private function importCastellersMembers(StdClass $collaInfo, Colla $colla, array $castellers)
    {

        // getting the ID of the Tags used on the new DB
        $newPositions = Tag::currentTags(TypeTags::POSITIONS, $colla);
        $newPositionsArray = [];

        foreach ($newPositions->toArray() as $key => $tag) {
            $newPositionsArray[$tag['id_tag_external']] = $tag['id_tag'];
        }

        $castellersParameterBagArray = [];

        foreach ($castellers as $castellerInfo) {
            $castellersParameterBagArray[] = $this->getCastellerParameters($castellerInfo, $newPositionsArray);
        }
        $this->importService->importCastellers($castellersParameterBagArray, $colla);
        $this->info(' Import of the Castellers from colla "'.$colla->getShortName().'" FINISHED!');

    }

    private function importCastellersPositions(StdClass $collaInfo, Colla $colla)
    {

        // Getting the Basic Positions from OLD DB
        $existingPositions = DB::connection('old_db')->select('select * from fp_vocabulari WHERE id < 31');
        $positions = [];
        $positionsParameterBagArray = [];

        foreach ($existingPositions as $existingPosition) {
            // Getting the translation used by the Colla, iex exists
            $translation = DB::connection('old_db')->select('select * from fp_vocabulari WHERE id_colla = :id AND paraula like :paraula', ['id' => $collaInfo->id, 'paraula' => $existingPosition->paraula]);
            if ($existingPosition->id < 14 || $existingPosition->id == 29 || $existingPosition->id == 30) {
                (empty($translation)) ? $positions[$existingPosition->id] = $existingPosition->traduccio : $positions[$existingPosition->id] = $translation[0]->traduccio;
            } else {
                if (! empty($translation)) {
                    $positions[$existingPosition->id] = $translation[0]->traduccio;
                }
            }
        }

        foreach ($positions as $position_id => $paraula) {
            $positionsParameterBagArray[] = $this->getPositionParameters($position_id, $paraula);
        }

        $this->importService->importPositions($positionsParameterBagArray, $colla);
        $this->info(' Import of the Positions from colla "'.$colla->getShortName().'" FINISHED!');

    }

    private function importCastellersPersonalData(StdClass $collaInfo, Colla $colla, array $castellers)
    {
        $castellersPersonalParameterBagArray = [];

        foreach ($castellers as $castellerInfo) {
            $castellersPersonalParameterBagArray[] = $this->getCastellerPersonalParameters($castellerInfo);
        }
        $this->importService->importCastellers($castellersPersonalParameterBagArray, $colla);
        $this->info(' Import of the Personal info from Castellers from colla "'.$colla->getShortName().'" FINISHED!');

    }

    /*
    * VALIDATIONS
    */

    private function validateParameter(string $collaParameter)
    {
        // Comprovar valor numèric
        if (! is_numeric($collaParameter)) {

            $this->printError($this->parameterNotNumeric);

        }
        $this->printCorrectInfo(' The Parameter is Numeric... ');

    }

    private function validateOptions(array $options)
    {

        // OPTION TO AVOID QUESTIONS. ALWAYS CONFIRM
        $this->confirmOption = $options['confirm'] ?? 0;

        // IMPORT EVERYTHING
        $this->allOption = $options['all'] ?? 0;

        // IMPORT COLLA DATA
        $this->collaOption = $options['colla'] ?? 0;

        // IMPORT CASTELLERS DATA
        $this->castellersOption = $options['castellers'] ?? 0;

        // IMPORT CASTELLERS PERSONAL DATA
        $this->castellersPersonalOption = $options['castellers-personal'] ?? 0;

        // IMPORT EVENTS DATA
        $this->eventsOption = $options['events'] ?? 0;

        // IMPORT ATTENDANCES DATA
        $this->attendancesOption = $options['attendances'] ?? 0;

        if (! $this->allOption && ! $this->collaOption && ! $this->castellersOption && ! $this->eventsOption && ! $this->attendancesOption && ! $this->castellersPersonalOption) {

            $this->printError(' No parameter used, nothing will be imported. Please, use one of the existing options ');

        }
        $this->printCorrectInfo(' Correct Options... ');

    }

    private function validateDatabase()
    {

        // Comprovar Connexió
        try {
            if (DB::connection('old_db')->getPdo()) {
                $this->printCorrectInfo(' Connection Established.... ');
            } else {
                $this->printError($this->failedConnection);
            }
        } catch (\Exception $e) {
            $this->printError($this->failedConnection);
        }

        // Comprovar Taula
        try {
            if (DB::connection('old_db')->getSchemaBuilder()->hasTable('fp_user')) {
                $this->printCorrectInfo(' fp_user table Exists... ');
            } else {
                $this->printError($this->tableDoesNotExist);
            }
        } catch (\Exception $e) {
            $this->printError($this->tableDoesNotExist);
        }

    }

    private function validateOldCollaExists(int $collaID): StdClass
    {
        // Comprovar Colla Existeix
        $collaStd = DB::connection('old_db')->select('select * from fp_user where id = :id', ['id' => $collaID]);

        // Comprovar Colla Existeix
        if (empty($collaStd)) {

            $this->printError($this->collaDoesNotExist);

        }

        $colla = $collaStd[0];

        $this->printCorrectInfo(' Colla "'.$colla->nombre.'" Exists in the Old DB... ');

        return $colla;
    }

    private function validateNewCollaExists(int $idCollaExternal): Colla
    {
        // Comprovar Colla Existeix
        $colla = Colla::where('id_colla_external', $idCollaExternal)->first();

        // Comprovar Colla Existeix
        if (is_null($colla)) {

            $this->printError($this->collaDoesNotExist);

        }

        $this->printCorrectInfo(' Colla "'.$colla->getShortname().'" Exists in the New Db... ');

        return $colla;
    }

    private function validateCastellers(StdClass $collaInfo): array
    {

        $castellers = DB::connection('old_db')->select('select * from fp_casteller where id_colla = :id', ['id' => $collaInfo->id]);

        $castellersList = [];
        $duplicates = [];

        foreach ($castellers as $casteller) {
            $nom = rtrim($casteller->nom);
            if (! array_key_exists($nom, $castellersList)) {
                $castellersList[$nom][] = $casteller->id_g;
            } elseif (! array_key_exists($casteller->nom, $duplicates)) {
                $duplicates[$nom][] = $castellersList[$nom][0];
                $duplicates[$nom][] = $casteller->id_g;
            } else {
                $duplicates[$nom][] = $casteller->id_g;
            }
        }

        ksort($duplicates);

        if (! empty($duplicates)) {
            $this->error(str_repeat(' ', strlen($this->duplicatedAlias)));
            $this->error($this->duplicatedAlias);
            $this->error(str_repeat(' ', strlen($this->duplicatedAlias)));
            foreach ($duplicates as $nom => $duplicate) {
                $string = ' - Nom: '.$nom.' | id_g: '.implode(' , ', $duplicate);
                $this->error(str_pad($string, strlen($this->duplicatedAlias), ' '));
            }
            $this->error(str_repeat(' ', strlen($this->duplicatedAlias)));
            $this->info(str_repeat(' ', strlen($this->duplicatedAlias)));
        } else {
            $this->printCorrectInfo(' No duplicated Alias found... ');
        }

        return [
            'data' => $castellers,
            'valid' => empty($duplicates),
        ];
    }

    private function validateEvents(StdClass $collaInfo): array
    {

        $events = DB::connection('old_db')->select('select * from fp_esdeveniments where id_colla = :id', ['id' => $collaInfo->id]);

        $eventsList = [];
        $duplicates = [];

        $eventsMessage = [];
        foreach ($events as $index => $event) {

            $oldTitol = strtolower(rtrim($event->titol));

            if (! array_key_exists($event->id_assaig_actuacio, $eventsList) || ! in_array($oldTitol, $eventsList[$event->id_assaig_actuacio])) {
                $eventsList[$event->id_assaig_actuacio][] = $oldTitol;
            } elseif (! array_key_exists($oldTitol, $duplicates)) {
                $duplicates[$event->id_assaig_actuacio][] = $oldTitol;
                $duplicates[$event->id_assaig_actuacio][] = $eventsList[$event->id_assaig_actuacio][0];
                $newTitle = rtrim($event->titol).' '.count($duplicates[$event->id_assaig_actuacio]);
                $eventsMessage[] = '- EVENT RENAMED: IdEvent: '.$event->id.' | Date: '.$event->id_assaig_actuacio.' | Titol: '.$oldTitol.' -> '.$newTitle.' ';
                $events[$index]->titol = $newTitle;

            } else {
                $duplicates[$event->id_assaig_actuacio][] = $oldTitol;
                $newTitle = $oldTitol.'_'.count($duplicates[$event->id_assaig_actuacio]);
                $eventsMessage[] = '- EVENT RENAMED: IdEvent: '.$event->id.' | Date: '.$event->id_assaig_actuacio.' | Titol: '.$oldTitol.' -> '.$newTitle.' ';
                $events[$index]->titol = $newTitle;
            }
        }

        if (! empty($duplicates)) {
            $maxlenmessage = max(array_map('strlen', $eventsMessage));
            $eventsMessageLength = max(strlen($this->duplicatedEvent), $maxlenmessage);
            $this->error(str_repeat(' ', $eventsMessageLength));
            $this->error(str_pad($this->duplicatedEvent, $eventsMessageLength));
            $this->error(str_repeat(' ', $eventsMessageLength));
            foreach ($eventsMessage as $eventsMessag) {
                $this->error(str_pad($eventsMessag, $eventsMessageLength));
            }
            $this->error(str_repeat(' ', $eventsMessageLength));
            $this->info(str_repeat(' ', $eventsMessageLength));

        } else {
            $this->printCorrectInfo(' No duplicated Events found... ');
        }

        return $events;
    }

    private function validateAttendance(StdClass $collaInfo): array
    {

        $attendances = DB::connection('old_db')->select('select * from fp_assistencia where id_colla = :id', ['id' => $collaInfo->id]);

        $attendancesList = [];
        $duplas = [];
        $attendanceMessage = [];

        foreach ($attendances as $index => $attendance) {
            if (! array_key_exists($attendance->id_asAc, $attendancesList) || ! in_array($attendance->id_casteller, $attendancesList[$attendance->id_asAc])) {
                $attendancesList[$attendance->id_asAc][] = $attendance->id_casteller;
                $duplas[strval($attendance->id_asAc).'-'.strval($attendance->id_casteller)] = $index;
            } else {
                $attendanceMessage[] = '- DUPLICATED REMOVED: IdAttendance: '.$attendance->id.'  IdEvent: '.$attendance->id_asAc.'  IdCasteller: '.$attendance->id_casteller.' ';
                $duplicates[] = strval($attendance->id_asAc).'-'.strval($attendance->id_casteller);
                unset($attendances[$index]);
            }
        }

        if (! empty($duplicates)) {

            foreach ($duplicates as $duplicate) {
                $index = $duplas[$duplicate];
                unset($attendances[$index]);
            }

            $maxlenmessage = max(array_map('strlen', $attendanceMessage));
            $attendanceMessageLength = max(strlen($this->duplicatedEvent), $maxlenmessage);
            $this->error(str_repeat(' ', $attendanceMessageLength));
            $this->error(str_pad($this->duplicatedAttendance, $attendanceMessageLength));
            $this->error(str_repeat(' ', $attendanceMessageLength));
            foreach ($attendanceMessage as $attendanceMessag) {
                $this->error(str_pad($attendanceMessag, $attendanceMessageLength));
            }
            $this->error(str_repeat(' ', $attendanceMessageLength));
        } else {
            $this->printCorrectInfo(' No duplicated Attendances found... ');
        }

        return $attendances;

    }

    /*
    * TRANSFORM DB VALUES TO PARAMETERBAG DATA TO BE IMPORTED
    */

    private function getCollaParameters(StdClass $collaInfo): ParameterBag
    {

        $parametersBag = new ParameterBag();

        $paramsArray = [
            'id_colla_external' => $collaInfo->id,
            'name' => $collaInfo->nombre,
            'shortname' => $collaInfo->shortname,
            'email' => ($collaInfo->email) ?: '',
        ];

        $parametersBag->add($paramsArray);

        return $parametersBag;

    }

    private function getCastellerParameters(StdClass $castellerInfo, array $newPositionsArray): ParameterBag
    {

        $parametersBag = new ParameterBag();

        switch ($castellerInfo->actiu) {
            case -1:
                $status = 3;
                break;
            case 0:
                $status = 4;
                break;
            case 1:
                $status = 2;
                break;
            case 2:
                $status = 1;
                break;
            default:
                $status = 1;
                break;
        }

        $paramsArray = [
            'id_casteller_external' => $castellerInfo->id_g,
            'alias' => $castellerInfo->nom,
            'position' => $newPositionsArray[$castellerInfo->perfil],
            'status' => $status,
        ];

        $parametersBag->add($paramsArray);

        return $parametersBag;

    }

    private function getPositionParameters(int $position_id, string $paraula): ParameterBag
    {

        $parametersBag = new ParameterBag();

        $paramsArray = [
            'id_tag_external' => $position_id,
            'name' => $paraula,
            'value' => Str::slug($paraula),
            'type' => 'POSITIONS',
        ];

        $parametersBag->add($paramsArray);

        return $parametersBag;

    }

    private function getCastellerPersonalParameters(StdClass $castellerInfo): ParameterBag
    {

        $parametersBag = new ParameterBag();

        switch ($castellerInfo->genere) {
            case 'm':
                $gender = 1;
                break;
            case 'f':
                $gender = 0;
                break;
            default:
                $gender = 3;
                break;
        }

        $paramsArray = [
            'id_casteller_external' => $castellerInfo->id_g,
            'gender' => $gender,
            'email' => $castellerInfo->email,
            'phone' => $castellerInfo->id_movil,
            'height' => (float) $castellerInfo->altura,
            'weight' => (float) $castellerInfo->pes,
        ];

        $parametersBag->add($paramsArray);

        return $parametersBag;

    }

    private function getEventParameters(StdClass $eventInfo): ParameterBag
    {

        $parametersBag = new ParameterBag();

        $paramsArray = [
            'id_event_external' => $eventInfo->id,
            'name' => substr($eventInfo->titol, 0, 110),
            'address' => $eventInfo->ubicacio,
            'comments' => $eventInfo->cos_noticia,
            'start_date' => $eventInfo->id_assaig_actuacio,
            'open_date' => $eventInfo->data_inici,
            'close_date' => $eventInfo->data_final,
            'type' => $eventInfo->tipus,
            'duration' => 60,
            'visibility' => 1,
            'companions' => ($eventInfo->maxim_participants > 0) ? 1 : 0,
        ];

        $parametersBag->add($paramsArray);

        return $parametersBag;

    }

    private function getAttendanceParameters(StdClass $attendanceInfo, ?Collection $castellers, ?Collection $events): ?ParameterBag
    {

        $parametersBag = new ParameterBag();

        switch ($attendanceInfo->assisteix) {
            case -1:
                $status = 2;
                break;
            case 0:
                $status = 3;
                break;
            case 1:
                $status = 1;
                break;
            case 2:
                $status = 1;
                break;
            default:
                $status = 3;
                break;
        }

        $casteller = $castellers->where('id_casteller_external', $attendanceInfo->id_casteller)->first();
        $event = $events->where('id_event_external', $attendanceInfo->id_asAc)->first();

        if (! is_null($casteller) && ! is_null($event)) {
            $paramsArray = [
                'id_attendance_external' => $attendanceInfo->id,
                'casteller_id' => $casteller->getId(),
                'event_id' => $event->getId(),
                'status' => $status,
            ];

            $parametersBag->add($paramsArray);

            return $parametersBag;

        }

        return null;

    }

    /*
    * HELPER FUNCTIONS
    */

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
