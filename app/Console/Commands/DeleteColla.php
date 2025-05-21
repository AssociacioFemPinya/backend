<?php

namespace App\Console\Commands;

use App\Attendance;
use App\Board;
use App\Casteller;
use App\Colla;
use App\Event;
use App\Notification;
use App\Tag;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteColla extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fempinya:delete-colla {id_colla} {shortname} {--confirm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete demo. confirm optional flag';

    protected string $parameterNotNumeric = " The parameter is NOT a numeric value. You must use Colla's ID ";

    protected string $collaDoesNotExist = ' The selected Colla does not exist. Are you sure you have used the correct ID? ';

    protected string $confirmDoesNotExist = ' The selected CONFIRM does not exist. Are you sure you have used the correct ID? ';

    protected bool $confirmOption = false;

    protected bool $shortnameOption = false;

    protected bool $allOption = false;

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

        // TODO
        // aquesta funcio esta feta a partir de les creades per Llorenç Import i CreateDemo
        // --confirm hauria d'enviar un mail amb token de confirmació a la colla que ha demanat esborrar

        // ID OF THE COLLA FROM WHICH THE DATA SHOULD BE DELETED.
        $collaParameter = $this->argument('id_colla');
        $shortnameParameter = $this->argument('shortname');
        //  $confirmOption = $this->argument('confirm');
        if (! $collaParameter && $shortnameParameter) {
            $this->printError($this->collaDoesNotExist);
        }

        // if( $this->argument('confirm') ){
        //     $this->printError($this->confirmDoesNotExist);
        // }

        $this->validateParameter($collaParameter);
        $this->validateOptions($this->options());

        $id_colla = intval($collaParameter);

        $this->info('We start the command!');

        // Comprovar Colla Existeix
        $colla = Colla::query()->find($id_colla);
        if (empty($colla)) {

            $this->printError($this->collaDoesNotExist);

        } else {

            if ($colla->getShortname() != $shortnameParameter) {
                $this->newLine();
                $this->line('----------------------------');
                $this->line('ERROR SHORNAME COLLA '.$shortnameParameter.'');
                $this->line('----------------------------');
                exit;
            }
            $this->startdelete($id_colla);

        }

        if (is_null($id_colla)) {

            $this->printError($this->collaDoesNotExist);
        }

        if ($colla->getId() == 1) {
            $this->newLine();
            $this->line('----------------------------');
            $this->line('IMPOSSIBLE DELETE '.$id_colla.' COLLA PROTEGIDA.');
            $this->line('----------------------------');
            exit;
        }
        $this->newLine();
        $this->info(' COLLA ELIMINADA!');
        $this->newLine();

    }

    /**
     * Execute star delete.
     */
    private function startDelete(int $id_colla)
    {

        $colla = Colla::query()->find($id_colla);
        $this->printInfo(' Colla "'.$colla->name.'" Exists in the DB... ');

        $this->newLine();
        $this->line('----------------------------');
        $this->line('DELETING COLLA id:'.$id_colla.' Name: '.$colla->getName().' ...');
        $this->line('----------------------------');

        if ($this->allOption || $this->confirmOption) {

            $this->deleteMedia($colla);
            $this->deleteCastellers($colla->getId());
            $this->deleteEvents($id_colla);
            $this->deleteTags($id_colla);
            $this->deleteBoards($id_colla);
            $this->deleteNotifications($id_colla);
            $this->deleteColles($id_colla);
            $this->deleteUsers($id_colla);

        }
    }

    private function validateOptions(array $options)
    {
        $this->confirmOption = $options['confirm'] ?? 0;

        $this->shortnameOption = $options['shortname'] ?? 0;

        if (! $this->allOption && ! $this->confirmOption && ! $this->shortnameOption) {

            $this->printError(' No parameter used, nothing will be imported. Please, use one of the existing options ');

        }
        $this->printCorrectInfo(' Correct Options... ');

    }

    /** Delete  castellers
     * and registres relacionats
     */
    private function deleteCastellers($id_colla)
    {

        $castellers = Casteller::query()->where('colla_id', $id_colla)->get();

        $total = $castellers->count();
        $total_attendance = 0;
        $total_casteller_tag = 0;
        $total_casteller_config = 0;
        $total_casteller_telegram = 0;
        $total_casteller_relationship = 0;
        $total_casteller_web = 0;
        $total_board_position = 0;

        $this->newLine();
        $this->line('----------------------------');
        $this->line('DELETING '.$total.' CASTELLERS...');
        $this->line('----------------------------');

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($castellers as $casteller) {

            $attendance = Attendance::query()->where('casteller_id', $casteller->getId());
            $subtotal = $attendance->count();
            if ($attendance != null) {
                $total_attendance = $total_attendance + $subtotal;
                $attendance->delete();
            }

            $casteller_tag = DB::table('casteller_tag')->where('casteller_id', $casteller->getId());
            $subtotal_1 = $casteller_tag->count();
            if ($casteller_tag != null) {
                $total_casteller_tag = $total_casteller_tag + $subtotal_1;
                $casteller_tag->delete();
            }

            $casteller_config = DB::table('casteller_config')->where('casteller_id', $casteller->getId());
            $subtotal_2 = $casteller_config->count();
            if ($casteller_config != null) {
                $total_casteller_config = $total_casteller_config + $subtotal_2;
                $casteller_config->delete();
            }

            $casteller_telegram = DB::table('casteller_telegram')->where('casteller_id', $casteller->getId());
            $subtotal_3 = $casteller_telegram->count();
            if ($casteller_telegram != null) {
                $total_casteller_telegram = $total_casteller_telegram + $subtotal_3;
                $casteller_telegram->delete();
            }

            $casteller_relationship = DB::table('casteller_relationship')->where('casteller_id', $casteller->getId());
            $subtotal_4 = $casteller_relationship->count();
            if ($casteller_relationship != null) {
                $total_casteller_relationship = $total_casteller_relationship + $subtotal_4;
                $casteller_relationship->delete();
            }

            $casteller_web = DB::table('casteller_web')->where('casteller_id', $casteller->getId());
            $subtotal_5 = $casteller_web->count();
            if ($casteller_web != null) {
                $total_casteller_web = $total_casteller_web + $subtotal_5;
                $casteller_web->delete();
            }

            $board_position = DB::table('board_position')->where('casteller_id', $casteller->getId());
            $subtotal_6 = $board_position->count();
            if ($board_position != null) {
                $total_board_position = $total_board_position + $subtotal_6;
                $board_position->delete();
            }
            $casteller->delete();
            $bar->advance();

        }

        $this->newLine();
        $this->line(' Deleted  "'.$total_attendance.'" registres attendances ');

        $this->line(' Deleted "'.$total_casteller_tag.'" registres casteller_tag ');

        $this->line(' Deleted "'.$total_casteller_config.'" registres casteller_config ');

        $this->line(' Deleted "'.$total_casteller_telegram.'" registres casteller_telegram ');

        $this->line(' Deleted "'.$total_casteller_relationship.'" registres castellers relationsship ');

        $this->line(' Deleted "'.$total_casteller_web.'" registres casteller_web ');

        $this->line(' Deleted "'.$total_board_position.'" registres board_position ');

        $bar->finish();

        $this->newLine(1);

    }

    /** Delete  events
     * and registres relacionats
     */
    private function deleteEvents($id_colla)
    {

        $events = Event::query()->where('colla_id', $id_colla)->get();

        $total = $events->count();
        $total_event_tag = 0;
        $total_event_attendance_tag = 0;
        $total_board_event = 0;

        $this->newLine();
        $this->line('----------------------------');
        $this->line('DELETING '.$total.' Events...');
        $this->line('----------------------------');

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($events as $event) {

            $event_tag = DB::table('event_tag')->where('event_id', $event->getId());
            $subtotal_1 = $event_tag->count();
            if ($event_tag != null) {
                $total_event_tag = $total_event_tag + $subtotal_1;
                $event_tag->delete();
            }

            $event_attendance_tag = DB::table('event_attendance_tag')->where('event_id', $event->getId());
            $subtotal_2 = $event_attendance_tag->count();
            if ($event_attendance_tag != null) {
                $total_event_attendance_tag = $total_event_attendance_tag + $subtotal_2;
                $event_attendance_tag->delete();
            }

            $board_event = DB::table('board_event')->where('event_id', $event->getId());
            $subtotal_1 = $board_event->count();
            if ($board_event != null) {
                $total_board_event = $total_board_event + $subtotal_1;
                $board_event->delete();
            }

            $event->delete();
        }

        $this->newLine();
        $this->line(' Deleted  "'.$total_event_tag.'" registres event_tag ');
        $this->line(' Deleted   "'.$total_event_attendance_tag.'" registres event_attendance_tag ');
        $this->line(' Deleted   "'.$total_board_event.'" registres total_board_event ');

        $bar->finish();

        $this->newLine(1);

    }

    /** Eliminar USERS
     * MOLTA ATENCIÓ
     * si un super-admin esta fent servir  logueat, aquesta colla,
     * esborra al super-admin i tots els users de la colla principal.
     * Per aquesta raó nomes s'informa
     */
    private function deleteUsers($id_colla)
    {

        $users = User::query()->where('colla_id', $id_colla)->get();

        foreach ($users as $user) {
            /**  Atenció, si un super-admin esta fent servir aquesta colla, esborra al super-admin */
            $this->newLine();
            $this->line('NO DELETE User: '.$user->name.'');
        }

        $this->newLine(1);
    }

    /** Eliminar BOARD
     * i registres relacionats
     */
    private function deleteBoards($id_colla)
    {

        $boards = Board::query()->where('colla_id', $id_colla)->get();
        $total = $boards->count();
        $total_board_position = 0;
        $total_board_event = 0;
        $total_board_tags = 0;
        $total_rows = 0;
        $total_boards = 0;

        $this->newLine();
        $this->line('----------------------------');
        $this->line('DELETING '.$total.' Boards..');
        $this->line('----------------------------');

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($boards as $board) {

            $board_event = DB::table('board_event')->where('board_id', $board->getId());
            $subtotal_1 = $board_event->count();
            $this->line('DELETING '.$board->getId().' Boards..');
            if ($board_event != null) {
                $total_board_event = $total_board_event + $subtotal_1;
                $board_event->delete();
            }

            $board_position = DB::table('board_position')->where('board_id', $board->getId());
            $subtotal_2 = $board_position->count();
            if ($board_position != null) {
                $total_board_position = $total_board_position + $subtotal_2;
                $board_position->delete();
            }

            $board_tags = DB::table('board_tags')->where('board_id', $board->getId());
            $subtotal_3 = $board_tags->count();
            if ($board_tags != null) {
                $total_board_tags = $total_board_tags + $subtotal_3;
                $board_tags->delete();
            }

            $rows = DB::table('rows')->where('board_id', $board->getId());
            $subtotal_4 = $rows->count();
            if ($rows != null) {
                $total_rows = $total_rows + $subtotal_4;
                $rows->delete();
            }

            if ($board != null) {
                $board->delete();
            }

        }

        $this->newLine();
        $this->line(' Deleted  "'.$total_board_event.'" registres board_event');
        $this->line(' Deleted  "'.$total_board_position.'" registres board_positions ');
        $this->line(' Deleted  "'.$total_board_tags.'" registres board poitions_tags ');
        $this->line(' Deleted  "'.$total_rows.'" registres rows');

        $bar->finish();

        $this->newLine(1);
    }

    /** Eliminar TAGS    */
    private function deleteTags($id_colla)
    {

        $etiquetes = Tag::query()->where('colla_id', $id_colla)->get();

        $total = $etiquetes->count();
        $total_user_tag = 0;
        $total_user_attendance_tag = 0;

        $this->newLine();
        $this->line('----------------------------');
        $this->line(' DELETING '.$total.' Tags');
        $this->line('----------------------------');
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($etiquetes as $etiquete) {
            $etiquete->delete();
        }

        $bar->finish();

        $this->newLine(1);

    }

    /** Eliminar COLLA    */
    private function deleteColles($id_colla)
    {
        $colles = Colla::query()->where('id_colla', $id_colla)->get();

        foreach ($colles as $colle) {
            $colle->delete();
        }

        $this->newLine();
        $this->line('----------------------------');
        $this->line('DELETED COLLA ');
        $this->line('----------------------------');

    }

    /** delete media  */
    private function deleteMedia($colla)
    {

        if (public_path('media/colles/'.$colla->getShortname()).'') {
            $dir = public_path('media/colles/'.$colla->getShortname()).'';
            if (is_dir($dir)) {
                $this->rmdir_recursive($dir);
                $this->line('DELETED dir '.$dir.' ...');
                if (is_dir($dir)) {
                    rmdir($dir);
                    $this->line('DELETeDIR '.$dir.' ');
                }
            } else {
                $this->line('NO EXITS, NO DELETED'.$dir.' ');
            }

        }

    }

    /** delete notifications  */
    private function deleteNotifications($id_colla)
    {

        $notifications = Notification::query()->where('colla_id', $id_colla)->get();
        $total_notifications = 0;
        $subtotal = $notifications->count();
        foreach ($notifications as $notification) {
            $total_notifications = $total_notifications + $subtotal;
            $notification->delete();

        }

        $this->newLine();
        $this->line('----------------------------');
        $this->line('DELETED '.$subtotal.'  NOTIFICATIONS ');

    }

    /** Elminar fitxer i directories de forma recursiva */
    public function rmdir_recursive($dir)
    {
        $files = scandir($dir);
        array_shift($files);     // remove '.' from array
        array_shift($files);     // remove '.' from array
        foreach ($files as $file) {
            $file = $dir.'/'.$file;
            if (is_dir($file)) {
                $this->rmdir_recursive($file);
                rmdir($file);
            } else {
                unlink($file);
            }
            if (is_dir($dir)) {
                // rmdir($dir);
                $this->line('DELETe '.$dir.' ');
            }

        }

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
