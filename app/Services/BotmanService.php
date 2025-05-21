<?php

namespace App\Services;

use App\Casteller;
use App\CastellerTelegram;
use App\Enums\TelegramInteractionType;
use App\Helpers\BotmanEmojisHelper;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\BotMan\Interfaces\UserInterface;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\Drivers\Web\WebDriver;
use Illuminate\Support\Facades\Log;
use Spatie\Emoji\Emoji;

class BotmanService
{
    private DriverInterface $botmanDriver;

    private BotmanEmojisHelper $botmanEmojiHelper;

    public function __construct(DriverInterface $botmanDriver)
    {
        $this->botmanDriver = $botmanDriver;
        $this->botmanEmojiHelper = new BotmanEmojisHelper();
    }

    public function getActiveCasteller(UserInterface|int $botmanUser): ?Casteller
    {
        if ($this->botmanDriver instanceof TelegramDriver) {
            $castellerTelegram = CastellerTelegram::getCastellerTelegramByTelegramId($botmanUser->getId());

            return (! is_null($castellerTelegram)) ? Casteller::find($castellerTelegram->getCastellerActiveId()) : null;
        } elseif ($this->botmanDriver instanceof WebDriver) {
            return null;
        } else {
            return null;
        }
    }

    public function getLinkedCasteller(UserInterface|int $botmanUser): ?Casteller
    {
        if ($this->botmanDriver instanceof TelegramDriver) {
            $castellerTelegram = CastellerTelegram::getCastellerTelegramByTelegramId($botmanUser->getId());

            return $castellerTelegram->getCasteller();
        } elseif ($this->botmanDriver instanceof WebDriver) {
            return null;
        } else {
            return null;
        }
    }

    /**
     * Checks if user is valid
     */
    public function getValidUser($botmanDriver, $message)
    {
        if ($botmanDriver instanceof WebDriver) {

            $incomingMessagePayload = $message->getPayload();

            if (! isset($incomingMessagePayload['userId']) || $incomingMessagePayload['userId'] == '0') {
                Log::error($incomingMessagePayload);

                return null;
            }

            return $botmanDriver->getUser($message);

        } elseif ($botmanDriver instanceof TelegramDriver) {

            $incomingMessagePayload = $message->getPayload()->toArray();

            if (! isset($incomingMessagePayload['from']['id']) || $incomingMessagePayload['from']['id'] == '0') {
                Log::error($incomingMessagePayload);

                return null;
            }

            return $botmanDriver->getUser($message);

        } else {

            Log::error('NO DRIVER');

            return null;
        }

    }

    public function getKeyBoard(array $keyboardButtons = []): array
    {

        $keyboardParseMode = ['parse_mode' => 'Markdown'];

        if ($this->botmanDriver instanceof TelegramDriver) {
            $keyboard = Keyboard::create()
                ->oneTimeKeyboard()
                ->type(Keyboard::TYPE_KEYBOARD)
                ->resizeKeyboard();

            if (! empty($keyboardButtons)) {
                foreach ($keyboardButtons as $rows) {
                    $buttons = [];
                    foreach ($rows as $buttonOption) {
                        $buttons[] = KeyboardButton::create($buttonOption);
                    }
                    $keyboard->addRow(...$buttons);
                }
            }

            return array_merge($keyboard->toArray(), $keyboardParseMode);
        } else {
            return $keyboardParseMode;
        }
    }

    public function getQuestion(string $questionText, array $questionButtons = []): Question|string
    {
        if ($this->botmanDriver instanceof TelegramDriver) {
            return $questionText;
        } else {
            $question = Question::create($questionText);
            if (! empty($questionButtons)) {
                $buttons = [];
                foreach ($questionButtons as $rows) {
                    foreach ($rows as $buttonOption) {
                        $buttons[] = Button::create($buttonOption)->value($buttonOption);
                    }
                }
                $question->addButtons($buttons);
            }

            return $question;
        }
    }

    public function getAnswer(Answer $answer): string
    {
        if ($this->botmanDriver instanceof TelegramDriver) {
            return $answer->getMessage()->getPayload()['text'];
        } else {
            return $answer->getValue();
        }
    }

    // MAIN MENU
    public function getMainMenu(Casteller $casteller): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                $emojis = [
                    'mainMenuHome',
                    'mainMenuEvents',
                    'mainMenuOptions',
                ];

                if ($casteller->getColla()->getConfig()->getBoardsEnabled()) {
                    $emojis[] = 'mainMenuPinyes';
                }

                if ($casteller->getCastellerConfig()->getTecnica() == 1) {
                    $emojis[] = 'mainMenuTecnica';
                }
                $emojis[] = 'mainMenuHelp';

                $mainMenu = $this->botmanEmojiHelper->getEmojis(
                    $emojis
                );

                return $mainMenu;
            case TelegramInteractionType::TEXT:
                $row = [];
                $row[] = __('botman.mainMenuHome');
                $row[] = __('botman.mainMenuEvents');
                $row[] = __('botman.mainMenuOptions');

                if ($casteller->getColla()->getConfig()->getBoardsEnabled()) {
                    $row[] = __('botman.mainMenuPinyes');
                }

                if ($casteller->getCastellerConfig()->getTecnica() == 1) {
                    $row[] = __('botman.mainMenuTecnica');
                }

                $row[] = __('botman.mainMenuHelp');

                return $row;
        }
    }

    // EVENTS
    public function getEventsMenu(Casteller $casteller): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                return $this->botmanEmojiHelper->getEmojis(
                    [
                        'eventsActuacio',
                        'eventsAssaig',
                        'eventsActivitat',
                        'warning',
                        'eventsVerifyAttendance',
                    ]
                );
            case TelegramInteractionType::TEXT:
                $row = [];
                $row[] = __('botman.eventsActuacio');
                $row[] = __('botman.eventsAssaig');
                $row[] = __('botman.eventsActivitat');
                $row[] = __('botman.eventsUnanswered');
                $row[] = __('botman.eventsVerifyAttendance');

                return $row;
        }
    }

    public function getEventsMenuTecnica(Casteller $casteller): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                return $this->botmanEmojiHelper->getEmojis(
                    [
                        'eventsActuacio',
                        'eventsAssaig',
                        'eventsActivitat',
                    ]
                );
            case TelegramInteractionType::TEXT:
                $row = [];
                $row[] = __('botman.eventsActuacio');
                $row[] = __('botman.eventsAssaig');
                $row[] = __('botman.eventsActivitat');

                return $row;
        }
    }

    public function getEventsMenuReminders(Casteller $casteller): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                return $this->botmanEmojiHelper->getEmojis(
                    [
                        'tecnicaSendRemindersEvents',
                        'Email',
                    ]
                );
            case TelegramInteractionType::TEXT:
                $row = [];
                $row[] = __('botman.reminder_about_event');
                $row[] = __('botman.reminder_general');

                return $row;
        }
    }

    public function getPinyesMenu(Casteller $casteller): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                return $this->botmanEmojiHelper->getEmojis(
                    [
                        'pinyesPinya',
                        'pinyesRondes',
                    ]
                );
            case TelegramInteractionType::TEXT:
                $row = [];
                $row[] = __('botman.pinyesPinya');
                $row[] = __('botman.pinyesRondes');

                return $row;
        }
    }

    public function getAttendanceEventMenu(Casteller $casteller, bool $customAnswers = false, bool $companions = false): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                $row = [];
                if ($customAnswers) {
                    $row[] = 'eventsAnswers';
                }

                if ($companions) {
                    $row[] = 'eventsCompanions';
                }

                if (! $customAnswers & ! $companions) {
                    $row[] = 'eventsAttendanceOk';
                }

                $row[] = 'eventsAttendanceNok';
                $row[] = 'eventsAttendanceUnknown';

                return $this->botmanEmojiHelper->getEmojis($row);

            case TelegramInteractionType::TEXT:

                $row = [];
                if ($customAnswers) {
                    $row[] = __('botman.answers');
                }

                if ($companions) {
                    $row[] = __('botman.companions');
                }

                if (! $customAnswers & ! $companions) {
                    $row[] = __('botman.eventsAttendanceOk');
                }

                $row[] = __('botman.eventsAttendanceNok');
                $row[] = __('botman.eventsAttendanceUnknown');

                return $row;
        }
    }

    public function getAttendanceEventAnswersMenu(Casteller $casteller): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                return $this->botmanEmojiHelper->getEmojis(
                    [
                        'eventsAnswers',
                    ]
                );
            case TelegramInteractionType::TEXT:
                $row = [];
                $row[] = __('botman.answers');

                return $row;
        }
    }

    public function getAttendanceAllEventsMenu(Casteller $casteller): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                return $this->botmanEmojiHelper->getEmojis(
                    [
                        'eventsAttendanceAllOk',
                        'eventsAttendanceAllNok',
                        'eventsAttendanceAllUnknown',
                    ]
                );
            case TelegramInteractionType::TEXT:
                $row = [];
                $row[] = __('botman.eventsAttendanceAllOk');
                $row[] = __('botman.eventsAttendanceAllNok');
                $row[] = __('botman.eventsAttendanceAllUnknown');

                return $row;
        }
    }

    public function getOtherAttendanceOptionsMenu(Casteller $casteller, $event): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                $emojis = [];

                if ($event->getCompanions() === true) {
                    $emojis[] = 'eventsCompanions';
                }
                if ($event->hasAttendanceAnswers() === true) {
                    $emojis[] = 'eventsAnswers';
                }

                $emojis[] = 'eventsTop';

                $mainMenu = $this->botmanEmojiHelper->getEmojis(
                    $emojis
                );

                return $mainMenu;
            case TelegramInteractionType::TEXT:
                $row = [];
                if ($event->getCompanions() === true) {
                    $row[] = __('botman.companions');
                }
                if ($event->hasAttendanceAnswers() === true) {
                    $row[] = __('botman.answers');
                }
                $row[] = __('botman.back');

                return $row;
        }
    }

    public function getBackButton(Casteller $casteller): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                $emojis = ['eventsTop'];
                $mainMenu = $this->botmanEmojiHelper->getEmojis(
                    $emojis
                );

                return $mainMenu;
            case TelegramInteractionType::TEXT:
                $row = [__('botman.back')];

                return $row;
        }

    }

    public function getCompanionsEventsMenu(): array
    {
        return [Emoji::keycap0(), Emoji::keycap1(), Emoji::keycap2(), Emoji::keycap3(), Emoji::keycap4(), Emoji::keycap5()];
    }

    public function getIndexCustomAnswersMenu(array $answers): array
    {
        $numEmoji = [];
        foreach (array_keys($answers) as $num) {
            $numEmoji[] = Emoji::{'keycap'.$num}();
        }

        return $numEmoji;
    }

    // TECNICA
    public function getTecnicaMenu(Casteller $casteller): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                return $this->botmanEmojiHelper->getEmojis(
                    [
                        'tecnicaAttendance',
                        'tecnicaReminders',
                        'tecnicaSearch',
                    ]
                );
            case TelegramInteractionType::TEXT:
                $row = [];
                $row[] = __('botman.tecnicaAttendance');
                $row[] = __('botman.tecnicaReminders');
                $row[] = __('botman.tecnicaSearch');

                return $row;
        }
    }

    public function getRemindersMenu(Casteller $casteller): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                return $this->botmanEmojiHelper->getEmojis(
                    [
                        'tecnicaSendRemindersExpres',
                        'tecnicaSendRemindersTags',
                    ]
                );
            case TelegramInteractionType::TEXT:
                $row = [];
                $row[] = __('botman.tecnicaSendRemindersExpres');
                $row[] = __('botman.tecnicaSendRemindersTags');

                return $row;
        }
    }

    // OPTIONS
    public function getOptionsMenu(Casteller $casteller): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                return $this->botmanEmojiHelper->getEmojis(
                    [
                        'optionsLinkingCastellers',
                        'optionsURLMember',
                        'optionsSwitchTextEmojis',
                        'optionsLanguage',
                        'optionsLogOut',
                    ]
                );

            case TelegramInteractionType::TEXT:
                $row = [];
                $row[] = __('botman.optionsLinkingCastellers');
                $row[] = __('botman.optionsURLMember');
                $row[] = __('botman.optionsSwitchTextEmojis');
                $row[] = __('botman.optionsLanguage');
                $row[] = __('botman.optionsLogOut');

                return $row;
        }
    }

    public function getLinkingCastellersMenu(Casteller $casteller, bool $linkedCastellers = false): array
    {
        $interactionType = $casteller->getInteractionType();

        switch ($interactionType) {
            case TelegramInteractionType::ICON:
                $keys[] = 'optionsLinkCasteller';
                // we add the options to switch casteller if exist linked castellers
                if ($linkedCastellers) {
                    $keys[] = 'optionsSwitchCasteller';
                }

                return $this->botmanEmojiHelper->getEmojis($keys);
            case TelegramInteractionType::TEXT:
                $row = [];
                $row[] = __('botman.optionsLinkCasteller');
                // we add the options to switch casteller if exist linked castellers
                if ($linkedCastellers) {
                    $row[] = __('botman.optionsSwitchCasteller');
                }

                return $row;
        }
    }

    public static function reloadCasteller(Casteller $casteller): Casteller
    {
        $casteller = Casteller::find($casteller->getId());

        return $casteller;
    }

    /*
     *
     * QUESTION TEMPLATE
     *
     *
    private function welcome()
    {

        // Prepare options

        $rows[] = [Emoji::grinningFace(), 'NOOOfadsfasOOO', __('general.no'), 'yes'];
        $rows[] = ['hehfffehe'];
        $questionText = __('botman.conversation_welcome');

        if($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = array();
            $buttonsKeyboard = $rows;
        }else{
            $buttonsQuestion = $rows;
            $buttonsKeyboard = array();
        }

        $botmanService = new BotmanService($this->botmanDriver);

        $this->ask(
            $botmanService->getQuestion($questionText,$buttonsQuestion),
            function (Answer $answer) use ($botmanService){
                switch ($botmanService->getAnswer($answer)) {
                    case Emoji::grinningFace():
                        return $this->bot->reply('EMOJfadsfasdfaIIffffffIII');
                    case 'NOOOfadsfasOOO':
                        return $this->bot->reply('Síífasdfasdfasdfasdfasdfasdfaíapapííí');
                    case __('general.no'):
                        return $this->bot->reply('Síífasdfasdfasdfasdfasdfasdfaíapapííí');
                    default:
                        return $this->repeat();
                }
            },$botmanService->getKeyBoard($buttonsKeyboard)
        );

    }
    */

}
