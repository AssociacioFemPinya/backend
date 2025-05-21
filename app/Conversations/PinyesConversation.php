<?php

namespace App\Conversations;

use App\Casteller;
use App\Event;
use App\Helpers\BotmanEmojisHelper;
use App\Helpers\BotmanHelper;
use App\Services\BotmanService;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\TelegramDriver;

class PinyesConversation extends Conversation
{
    protected Casteller $casteller;

    protected string $lb;

    protected DriverInterface $botmanDriver;

    protected string $option;

    protected BotmanEmojisHelper $botmanEmojisHelper;

    public function __construct(DriverInterface $botmanDriver, Casteller $casteller, string $option = '')
    {

        $this->lb = BotmanHelper::getLbTag($botmanDriver);
        $this->botmanDriver = $botmanDriver;
        $this->casteller = $casteller;
        $this->option = $option;
        $this->botmanEmojisHelper = new BotmanEmojisHelper();
    }

    private function reloadCasteller()
    {
        $casteller = BotmanService::reloadCasteller($this->casteller);
        $this->casteller = $casteller;
    }

    public function run()
    {
        switch ($this->option) {
            case $this->botmanEmojisHelper->getEmoji('pinyesPinya'):
            case __('botman.pinyesPinya'):
                $this->askPublicDisplayUrl();
                break;
            case $this->botmanEmojisHelper->getEmoji('pinyesRondes'):
            case __('botman.pinyesRondes'):
                $this->askRondes();
                break;
            default:
                $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuHome'));
        }
    }

    private function askPublicDisplayUrl()
    {

        $this->reloadCasteller();

        $collaConfig = $this->casteller->getColla()->getConfig();
        $publicUrl = $collaConfig->getPublicDisplayUrl($this->casteller->getId());
        if ($publicUrl !== '') {
            $message = $this->botmanEmojisHelper->getEmoji('pinyesPinya').' '.__('botman.open_url_pinyes').' '.$this->lb.' '.$this->lb;
            $message .= $publicUrl.$this->lb.' '.$this->lb;
        } else {
            $message = __('boards.public_display_not_enabled').$this->lb.' '.$this->lb;
        }

        $this->getBot()->reply($message, ['parse_mode' => 'Markdown']);

        $this->askDefault();

    }

    private function askRondes()
    {
        $nextEvent = Event::filter($this->casteller->getColla())->liveOrUpcoming()->visible()->eloquentBuilder()
            ->orderBy('start_date', 'asc')
            ->first();

        if (is_null($nextEvent)) {
            $this->getBot()->reply(__('botman.conversation_events_ask_offer_events_no_events'));

            return $this->askDefault();
        }

        $rondes = $nextEvent->getRondes();

        $rondesFormated = BotmanHelper::getRondesFormattedWithLink($this->casteller, $nextEvent, $rondes, $this->botmanDriver, $this->botmanEmojisHelper);

        $this->getBot()->reply($rondesFormated['message'], ['parse_mode' => 'Markdown']);

        $this->askDefault();

    }

    /* Version using Buttons for each Ronda */
    /*
    private function askRondesOld(){

        $eventsFilter = new EventsFilter($this->casteller->getColla());
        $nextEvent = $eventsFilter->upcoming()->visible()->eloquentBuilder()
            ->orderBy('start_date', 'asc')
            ->first();

        if (is_null($nextEvent)) {
            $this->getBot()->reply(__('botman.conversation_events_ask_offer_events_no_events'));
            return $this->askDefault();
        }

        $rondes = $nextEvent->getRondes();

        $rondesFormated = BotmanHelper::getRondesFormatted($nextEvent,$rondes,$this->botmanDriver,  $this->botmanEmojisHelper);
        $this->getBot()->reply($rondesFormated['message']);

        if(empty($rondesFormated['options'])) return $this->askDefault();


        $botmanService = new BotmanService($this->botmanDriver);

        $pinyesMenuOptions = $botmanService->getPinyesMenu($this->casteller);
        $mainMenuOptions = $botmanService->getMainMenu($this->casteller);
        $rondesOptions = $rondesFormated['rows'];

        $rows[] = $rondesFormated['rows'];
        $rows[] = $pinyesMenuOptions;
        $rows[] = $mainMenuOptions;

        $questionText = __('botman.general_choose_option');

        if($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = array();
            $buttonsKeyboard = $rows;
        }else{
            $buttonsQuestion = $rows;
            $buttonsKeyboard = array();
        }

        $this->ask(
            $botmanService->getQuestion($questionText,$buttonsQuestion),
            function (Answer $answer) use ($botmanService,$mainMenuOptions,$pinyesMenuOptions,$rondesOptions,$rondes){
                $answer = $botmanService->getAnswer($answer);
                if(in_array($answer,$mainMenuOptions)){
                    return $this->manageMainMenu($answer);
                } else if (in_array($answer, $pinyesMenuOptions)) {
                    return $this->manageCurrentMenu($answer);
                }else if (in_array($answer, $rondesOptions)) {
                    $index = array_search($answer, $rondesOptions);
                    $ronda = $rondes->get($index);
                    $publicUrl = $ronda->getBoardEvent()->getPublicUrl($this->casteller->getId());
                    $this->getBot()->reply($publicUrl, ['parse_mode' => 'Markdown']);
                    return $this->askDefault();
                }
                else{
                    return $this->repeat(__('botman.general_use_menu'));
                }
            },$botmanService->getKeyBoard($buttonsKeyboard)
        );

    }
*/

    private function askDefault()
    {

        $botmanService = new BotmanService($this->botmanDriver);

        $pinyesMenuOptions = $botmanService->getPinyesMenu($this->casteller);
        $mainMenuOptions = $botmanService->getMainMenu($this->casteller);

        $rows[] = $pinyesMenuOptions;
        $rows[] = $mainMenuOptions;

        $questionText = __('botman.general_choose_option');

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService, $mainMenuOptions, $pinyesMenuOptions) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenuOptions)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $pinyesMenuOptions)) {
                    return $this->manageCurrentMenu($answer);
                } else {
                    return $this->repeat(__('botman.general_use_menu'));
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function manageMainMenu(string $answer = '')
    {
        return $this->getBot()->startConversation(new FempinyaConversation($this->botmanDriver, $this->casteller, $answer));
    }

    private function manageCurrentMenu(string $answer = '')
    {
        return $this->getBot()->startConversation(new PinyesConversation($this->botmanDriver, $this->casteller, $answer));
    }
}
