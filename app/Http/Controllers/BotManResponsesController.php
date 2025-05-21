<?php

namespace App\Http\Controllers;

use BotMan\BotMan\Messages\Incoming\Answer;

class BotManResponsesController extends Controller
{
    /**
     * Response to the non-registered users
     */
    public function responseNotRegistered($bot, $message): array
    {
        $response = [];
        $response[] = trans('botman.user_is_not_registered');

        return $this->sendReply($bot, $response);
    }

    /**
     * Response by default (unknown string)
     */
    public static function responseDefault($bot, $message): array
    {
        $msg = [];
        $msg[] = trans('botman.i_don_t_know_what_to_do_with').'<b>"'.$message.'"</b>';
        $msg[] = trans('botman.ask_for_help');

        return $msg;
    }

    /**
     * Response for "ajuda"
     */
    public static function responseAjuda($bot): array
    {
        $msg = [];
        $msg[] = trans('botman.help');

        return $msg;
    }

    /**
     * Response for "event"
     */
    public static function responseEvent($bot): array
    {
        $msg = [];
        $msg[] = trans('botman.calendar');

        return $msg;
    }

    /**
     * Response for "name"
     */
    public static function responseName($bot): array
    {
        $bot->ask(
            trans('botman.what_s_your_name').'',
            function (Answer $answer) {
                $name = $answer->getText();
                $this->say(trans('botman.nice_to_meet_you').' '.$name.' ');

            }
        );

    }

    /**
     * Builds the Response with the proper EOF depending on the Driver used
     */
    private function sendReply($bot, $responseMessage)
    {

        $eof = $this->getEof($bot);
        $formattedResponseMessage = '';

        if (! empty($responseMessage)) {
            foreach ($responseMessage as $row) {
                $formattedResponseMessage .= $row.$eof;
            }
        }
        $bot->Reply($formattedResponseMessage, ['parse_mode' => 'HTML']);

    }

    /**
     * Returns Line Break depending on the Driver
     *
     * @return string
     */
    private static function getEof($bot)
    {

        if ($bot->getDriver()->getName() === 'Telegram') {
            return " \n";
        } else {
            return ' <br> ';
        }
    }
}
