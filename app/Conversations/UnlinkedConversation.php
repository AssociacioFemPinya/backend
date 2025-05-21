<?php

namespace App\Conversations;

use App\Casteller;
use App\CastellerTelegram;
use App\Helpers\BotmanHelper;
use App\Services\BotmanService;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\TelegramDriver;
use Spatie\Emoji\Emoji;

class UnlinkedConversation extends Conversation
{
    protected string $lb;

    protected DriverInterface $botmanDriver;

    public function __construct(DriverInterface $botmanDriver)
    {
        $this->botmanDriver = $botmanDriver;
        $this->lb = BotmanHelper::getLbTag($botmanDriver);
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->welcome();
    }

    private function welcome()
    {
        $message = __('botman.conversation_welcome', ['nameUser' => $this->bot->getUser()->getFirstName()]).' '.Emoji::grinningFace().' '.$this->lb.$this->lb;
        $message .= __('botman.conversation_unlinked_link').$this->lb;
        $this->bot->reply($message);

        $this->askLinkCode();

    }

    private function askLinkCode()
    {

        $rows[] = [__('general.yes'), __('general.no')];
        $questionText = __('botman.conversation_unlinked_ask_link_code_question');

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $botmanService = new BotmanService($this->botmanDriver);

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService) {
                switch ($botmanService->getAnswer($answer)) {
                    case __('general.no'):
                        return $this->getBot()->reply(__('botman.conversation_unlinked_ask_link_code_answer_no'));
                    case __('general.yes'):
                        return $this->askInsertLinkCode();
                    default:
                        return $this->repeat(__('botman.general_use_menu'));
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );

    }

    private function askInsertLinkCode()
    {
        $this->ask(
            __('botman.conversation_unlinked_ask_insert_link_code_question'),
            function (Answer $answer) {
                $linkCode = $answer->getText();
                $casteller = Casteller::getCastellerByTelegramToken($linkCode);
                if (is_null($casteller)) {
                    $this->getBot()->reply(__('botman.conversation_unlinked_ask_insert_link_code_answer_unknown_code'));

                    return $this->askWhatelse();
                } else {
                    $this->getBot()->reply(__('botman.conversation_unlinked_ask_insert_link_code_answer_correct_code', ['nameCasteller' => $casteller->getDisplayName(), 'nameColla' => $casteller->getColla()->getName()]));

                    return $this->askLinking($casteller);
                }
            }
        );

    }

    private function askLinking(Casteller $casteller)
    {

        $rows[] = [__('general.yes'), __('general.no')];
        $questionText = __('botman.conversation_unlinked_ask_linking_question');

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $botmanService = new BotmanService($this->botmanDriver);

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService, $casteller) {
                switch ($botmanService->getAnswer($answer)) {
                    case __('general.no'):
                        $this->getBot()->reply(__('botman.conversation_unlinked_ask_linking_answer_no'));

                        return $this->askWhatelse();
                    case __('general.yes'):
                        $this->getBot()->reply(__('botman.conversation_unlinked_ask_linking_answer_yes'));
                        $botmanUser = $this->getBot()->getUser();
                        $casteller->linkedCastellers()->attach($casteller);
                        $casteller->save();
                        $castellerTelegram = CastellerTelegram::newCastellerTelegram($casteller, $botmanUser);
                        $castellerTelegram->casteller()->associate($casteller);
                        $castellerTelegram->save();

                        return $this->getBot()->startConversation(new FempinyaConversation($this->getBot()->getDriver(), $casteller));
                    default:
                        return $this->repeat(__('botman.general_use_menu'));
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );

    }

    private function askWhatelse()
    {

        $rows[] = [__('general.yes'), __('general.no')];
        $questionText = __('botman.conversation_unlinked_ask_whatelse_question');

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $botmanService = new BotmanService($this->botmanDriver);

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService) {
                switch ($botmanService->getAnswer($answer)) {
                    case __('general.no'):
                        return $this->getBot()->reply(__('botman.conversation_unlinked_ask_whatelse_answer_no'));
                    case __('general.yes'):
                        return $this->askInsertLinkCode();
                    default:
                        return $this->repeat(__('botman.general_use_menu'));
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }
}
