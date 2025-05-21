<?php

namespace App\Conversations;

use App\Casteller;
use App\Enums\TelegramInteractionType;
use App\Helpers\BotmanEmojisHelper;
use App\Helpers\BotmanHelper;
use App\Services\BotmanService;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\TelegramDriver;
use Carbon\Carbon;

class FempinyaConversation extends Conversation
{
    protected Casteller $casteller;

    protected string $lb;

    protected DriverInterface $botmanDriver;

    protected string $menu;

    protected BotmanEmojisHelper $botmanEmojisHelper;

    protected int $interactionType;

    public function __construct(DriverInterface $botmanDriver, Casteller $casteller, string $menu = '')
    {

        $this->lb = BotmanHelper::getLbTag($botmanDriver);
        $this->botmanDriver = $botmanDriver;
        $this->casteller = $casteller;
        $this->menu = $menu;
        $this->botmanEmojisHelper = new BotmanEmojisHelper();
        if ($this->casteller->hasInteractionType()) {
            $this->interactionType = $this->casteller->getInteractionType();
        }
    }

    /* TODO: Creating missing menus and conversations */

    public function run()
    {
        $this->manageMainMenu($this->menu);
    }

    private function welcome()
    {
        $message = __('botman.conversation_welcome', ['nameUser' => $this->casteller->getDisplayName()]);
        $this->getBot()->reply($message);
        if ($this->casteller->hasInteractionType()) {
            return $this->homeMenu(true);
        } else {
            return $this->askTextEmoji();
        }
    }

    private function askTextEmoji()
    {

        $rows[] = [__('botman.conversation_unlinked_emoji'), __('botman.conversation_unlinked_text')];
        $questionText = __('botman.conversation_unlinked_ask_emoji_text');

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
                $answer = $botmanService->getAnswer($answer);
                if ($answer === __('botman.conversation_unlinked_emoji')) {

                    $this->casteller->setInteractionType(TelegramInteractionType::ICON);
                    $this->casteller->save();
                } elseif ($answer === __('botman.conversation_unlinked_text')) {
                    $this->casteller->setInteractionType(TelegramInteractionType::TEXT);
                    $this->casteller->save();
                }

                return $this->homeMenu(true);
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function homeMenu(bool $firstTime = false)
    {

        $botmanService = new BotmanService($this->botmanDriver);

        $castellerConfig = $this->casteller->getCastellerConfig();
        $castellerConfig->last_access_at = Carbon::now()->toDateTimeString();
        $castellerConfig->save();

        if (! $firstTime) {
            $message = __('botman.conversation_name_reminder', ['nameUser' => $this->casteller->getDisplayName()]);
            $this->getBot()->reply($message);
        }
        $mainMenuOptions = $botmanService->getMainMenu($this->casteller);
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
            function (Answer $answer) use ($botmanService, $mainMenuOptions) {
                $answer = $botmanService->getAnswer($answer);

                if (in_array($answer, $mainMenuOptions)) {
                    return $this->manageMainMenu($answer);
                } else {
                    return $this->repeat(__('botman.general_use_menu'));
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );

    }

    /* TODO: Creating the HELP conversation */
    private function helpMenu()
    {

        $botmanService = new BotmanService($this->botmanDriver);
        $eventsFormated = BotmanHelper::getHelpFormatted($this->casteller->getCastellerConfig()->getTecnica(), $this->botmanDriver, $this->botmanEmojisHelper);
        $this->getBot()->reply($eventsFormated);

        $mainMenuOptions = $botmanService->getMainMenu($this->casteller);
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
            function (Answer $answer) use ($botmanService, $mainMenuOptions) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenuOptions)) {
                    // This should send the user to the HELP Conversation
                    return $this->manageMainMenu($answer);
                } else {
                    return $this->repeat(__('botman.general_use_menu'));
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );

    }

    private function pinyesMenu()
    {

        $botmanService = new BotmanService($this->botmanDriver);

        $pinyesMenu = $botmanService->getPinyesMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $pinyesMenu;
        $rows[] = $mainMenu;
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
            function (Answer $answer) use ($botmanService, $mainMenu, $pinyesMenu) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    $this->manageMainMenu($answer);
                } elseif (in_array($answer, $pinyesMenu)) {
                    $this->bot->startConversation(new PinyesConversation($this->botmanDriver, $this->casteller, $answer));
                } else {
                    $this->repeat(__('botman.general_use_menu'));
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function eventsMenu()
    {

        $botmanService = new BotmanService($this->botmanDriver);

        $eventsMenu = $botmanService->getEventsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $eventsMenu;
        $rows[] = $mainMenu;
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
            function (Answer $answer) use ($botmanService, $mainMenu, $eventsMenu) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    $this->manageMainMenu($answer);
                } elseif (in_array($answer, $eventsMenu)) {
                    $this->bot->startConversation(new EventsConversation($this->botmanDriver, $this->casteller, $answer));
                } else {
                    $this->repeat(__('botman.general_use_menu'));
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function optionsMenu()
    {

        $botmanService = new BotmanService($this->botmanDriver);

        $optionsMenuOptions = $botmanService->getOptionsMenu($this->casteller);
        $mainMenuOptions = $botmanService->getMainMenu($this->casteller);

        $rows[] = $optionsMenuOptions;
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
            function (Answer $answer) use ($botmanService, $mainMenuOptions, $optionsMenuOptions) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenuOptions)) {
                    $this->manageMainMenu($answer);
                } elseif (in_array($answer, $optionsMenuOptions)) {
                    $this->bot->startConversation(new OptionsConversation($this->botmanDriver, $this->casteller, $answer));
                } else {
                    $this->repeat(__('botman.general_use_menu'));
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function tecnicaMenu()
    {

        $botmanService = new BotmanService($this->botmanDriver);

        $tecnicaMenu = $botmanService->getTecnicaMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $tecnicaMenu;
        $rows[] = $mainMenu;
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
            function (Answer $answer) use ($botmanService, $mainMenu, $tecnicaMenu) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    $this->manageMainMenu($answer);
                } elseif (in_array($answer, $tecnicaMenu)) {
                    $this->bot->startConversation(new TecnicaConversation($this->botmanDriver, $this->casteller, $answer));
                } else {
                    $this->repeat(__('botman.general_use_menu'));
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function manageMainMenu(string $answer)
    {

        switch ($answer) {
            case $this->botmanEmojisHelper->getEmoji('mainMenuHome'):
            case __('botman.mainMenuHome'):
                $this->homeMenu();
                break;
            case $this->botmanEmojisHelper->getEmoji('mainMenuEvents'):
            case __('botman.mainMenuEvents'):
                $this->eventsMenu();
                break;
            case $this->botmanEmojisHelper->getEmoji('mainMenuTecnica'):
            case __('botman.mainMenuTecnica'):
                $this->tecnicaMenu();
                break;
            case $this->botmanEmojisHelper->getEmoji('mainMenuPinyes'):
            case __('botman.mainMenuPinyes'):
                $this->pinyesMenu();
                break;
            case $this->botmanEmojisHelper->getEmoji('mainMenuOptions'):
            case __('botman.mainMenuOptions'):
                $this->optionsMenu();
                break;
            case $this->botmanEmojisHelper->getEmoji('mainMenuHelp'):
            case __('botman.mainMenuHelp'):
                $this->helpMenu();
                break;
            default:
                $this->welcome();
                break;
        }
    }
}
