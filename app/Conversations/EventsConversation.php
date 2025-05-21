<?php

namespace App\Conversations;

use App\Attendance;
use App\Casteller;
use App\Enums\AttendanceStatus;
use App\Enums\EventTypeEnum;
use App\Enums\EventTypeNameEnum;
use App\Enums\TelegramInteractionType;
use App\Event;
use App\Helpers\BotmanEmojisHelper;
use App\Helpers\BotmanHelper;
use App\Services\BotmanService;
use App\Services\TOTPService;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Database\Eloquent\Collection;

class EventsConversation extends Conversation
{
    protected Casteller $casteller;

    protected string $lb;

    protected DriverInterface $botmanDriver;

    protected string $option;

    protected BotmanEmojisHelper $botmanEmojisHelper;

    protected int $interactionType;

    public function __construct(DriverInterface $botmanDriver, Casteller $casteller, string $option = '')
    {

        $this->lb = BotmanHelper::getLbTag($botmanDriver);
        $this->botmanDriver = $botmanDriver;
        $this->casteller = $casteller;
        $this->option = $option;
        $this->botmanEmojisHelper = new BotmanEmojisHelper();
    }

    public function setCasteller(Casteller $casteller)
    {
        $this->casteller = $casteller;
    }

    public function run()
    {
        switch ($this->option) {
            case $this->botmanEmojisHelper->getEmoji('eventsActuacio'):
            case __('botman.eventsActuacio'):
                $this->askOfferEvents(EventTypeNameEnum::Actuacio()->value(), $this->option, 3);
                break;
            case $this->botmanEmojisHelper->getEmoji('eventsAssaig'):
            case __('botman.eventsAssaig'):
                $this->askOfferEvents(EventTypeNameEnum::Assaig()->value(), $this->option, 3);
                break;
            case $this->botmanEmojisHelper->getEmoji('eventsActivitat'):
            case __('botman.eventsActivitat'):
                $this->askOfferEvents(EventTypeNameEnum::Activitat()->value(), $this->option, 3);
                break;
            case $this->botmanEmojisHelper->getEmoji('warning'):
            case __('botman.eventsUnanswered'):
                $this->askUnansweredEvents($this->option, 3);
                break;
            case $this->botmanEmojisHelper->getEmoji('eventsVerifyAttendance'):
            case __('botman.eventsVerifyAttendance'):
                $this->verifyAttendance($this->option, 3);
                break;
            default:
                $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuHome'));
        }
    }

    private function askOfferEvents(string $event_type, string $option, $countByRow = 3)
    {

        $column_order = 'start_date';
        $dir = 'ASC';
        //$this->open_date < $date && $this->close_date > $date

        $max_viewed_events = $this->casteller->getColla()->getConfig()->getMaxEvents($event_type);

        $tagsCasteller = $this->casteller->tagsArray('id_tag');

        $events = Event::filter($this->casteller->getColla())
            ->upcoming()
            ->visible()
            ->withCastellerTags($tagsCasteller)->eloquentBuilder()
            ->where('type', '=', Event::getTypeId($event_type))
            ->take($max_viewed_events)
            ->orderBy($column_order, $dir)
            ->get();

        if ($events->isEmpty()) {

            $this->getBot()->reply(__('botman.conversation_events_ask_offer_events_no_events'));

            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuEvents'));

        } else {

            $attendance = Attendance::getAttendanceCasteller($this->casteller->getId());

            $eventsFormated = BotmanHelper::getEventsFormatted($events, $this->casteller->getColla(), $this->botmanDriver, $countByRow, $attendance, $this->botmanEmojisHelper);

            $this->getBot()->reply($eventsFormated['message'], ['parse_mode' => 'Markdown']);

            // $this->getBot()->reply($eventsFormated['message'], ['parse_mode' => 'Markdown']);

            $botmanService = new BotmanService($this->botmanDriver);

            $modifyAllAttendanceMenu = $botmanService->getAttendanceAllEventsMenu($this->casteller);

            $rows[] = $modifyAllAttendanceMenu;

            $rows = array_merge($rows, $eventsFormated['rows']);

            $eventsMenu = $botmanService->getEventsMenu($this->casteller);
            $mainMenu = $botmanService->getMainMenu($this->casteller);

            $rows[] = $eventsMenu;
            $rows[] = $mainMenu;
            $questionText = __('botman.conversation_events_ask_offer_events_question');

            if ($this->botmanDriver instanceof TelegramDriver) {
                $buttonsQuestion = [];
                $buttonsKeyboard = $rows;
            } else {
                $buttonsQuestion = $rows;
                $buttonsKeyboard = [];
            }

            $this->ask(
                $botmanService->getQuestion($questionText, $buttonsQuestion),
                function (Answer $answer) use ($botmanService, $eventsMenu, $mainMenu, $events, $eventsFormated, $attendance, $modifyAllAttendanceMenu, $option) {
                    $answer = $botmanService->getAnswer($answer);
                    if (in_array($answer, $mainMenu)) {
                        return $this->manageMainMenu($answer);
                    } elseif (in_array($answer, $eventsMenu)) {
                        return $this->manageCurrentMenu($answer);
                    } elseif (in_array($answer, $modifyAllAttendanceMenu)) {
                        return $this->askSetAttendanceAllEvents($events, $answer, $option);
                    } elseif (in_array($answer, $eventsFormated['options'])) {
                        $index = array_search($answer, $eventsFormated['options']);
                        $event = $events->get($index);
                        $eventAttendance = $attendance->where('event_id', $event->getId())->first();

                        return $this->askSetAttendanceEvent($event, $eventAttendance, $option);
                    } else {
                        return $this->repeat(__('botman.general_use_menu'));
                    }
                },
                $botmanService->getKeyBoard($buttonsKeyboard)
            );
        }
    }

    private function askUnansweredEvents(string $option, $countByRow = 3)
    {

        $column_order = 'start_date';
        $dir = 'ASC';
        //$this->open_date < $date && $this->close_date > $date

        $tagsCasteller = $this->casteller->tagsArray('id_tag');

        $eventsAssajos = Event::filter($this->casteller->getColla())->upcoming()->visible()->withCastellerTags($tagsCasteller)->withType(EventTypeEnum::ASSAIG)->eloquentBuilder()
            ->take($max_viewed_events = $this->casteller->getColla()->getConfig()->getMaxAssaigs())
            ->orderBy($column_order, $dir)
            ->get();

        $eventsActuacions = Event::filter($this->casteller->getColla())->upcoming()->visible()->withCastellerTags($tagsCasteller)->withType(EventTypeEnum::ACTUACIO)->eloquentBuilder()
            ->take($max_viewed_events = $this->casteller->getColla()->getConfig()->getMaxActuacions())
            ->orderBy($column_order, $dir)
            ->get();

        $eventsActivitats = Event::filter($this->casteller->getColla())->upcoming()->visible()->withCastellerTags($tagsCasteller)->withType(EventTypeEnum::ACTIVITAT)->eloquentBuilder()
            ->take($max_viewed_events = $this->casteller->getColla()->getConfig()->getMaxActivitats())
            ->orderBy($column_order, $dir)
            ->get();

        $events = $eventsAssajos->merge($eventsActuacions)->merge($eventsActivitats);

        $attendance = Attendance::getAttendanceCasteller($this->casteller->getId());

        $eventsAnswered = $attendance->map->getEvent();

        $eventsUnanswered = $events->diff($eventsAnswered)->filter->isOpen();

        if ($eventsUnanswered->isEmpty()) {

            $this->getBot()->reply(__('botman.conversation_events_ask_offer_events_no_unanswered_events'));

            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuEvents'));

        } else {

            $attendance = Attendance::getAttendanceCasteller($this->casteller->getId());

            $eventsFormated = BotmanHelper::getEventsFormatted($eventsUnanswered, $this->casteller->getColla(), $this->botmanDriver, $countByRow, $attendance, $this->botmanEmojisHelper);

            $this->getBot()->reply($eventsFormated['message'], ['parse_mode' => 'Markdown']);

            $botmanService = new BotmanService($this->botmanDriver);

            $modifyAllAttendanceMenu = $botmanService->getAttendanceAllEventsMenu($this->casteller);

            $rows[] = $modifyAllAttendanceMenu;

            $rows = array_merge($rows, $eventsFormated['rows']);

            $eventsMenu = $botmanService->getEventsMenu($this->casteller);
            $mainMenu = $botmanService->getMainMenu($this->casteller);

            $rows[] = $eventsMenu;
            $rows[] = $mainMenu;
            $questionText = __('botman.conversation_events_ask_offer_events_question');

            if ($this->botmanDriver instanceof TelegramDriver) {
                $buttonsQuestion = [];
                $buttonsKeyboard = $rows;
            } else {
                $buttonsQuestion = $rows;
                $buttonsKeyboard = [];
            }

            $this->ask(
                $botmanService->getQuestion($questionText, $buttonsQuestion),
                function (Answer $answer) use ($botmanService, $eventsMenu, $mainMenu, $eventsUnanswered, $eventsFormated, $attendance, $modifyAllAttendanceMenu, $option) {
                    $answer = $botmanService->getAnswer($answer);
                    if (in_array($answer, $mainMenu)) {
                        return $this->manageMainMenu($answer);
                    } elseif (in_array($answer, $eventsMenu)) {
                        return $this->manageCurrentMenu($answer);
                    } elseif (in_array($answer, $modifyAllAttendanceMenu)) {
                        return $this->askSetAttendanceAllEvents($eventsUnanswered, $answer, $option);
                    } elseif (in_array($answer, $eventsFormated['options'])) {
                        $index = array_search($answer, $eventsFormated['options']);
                        $event = $eventsUnanswered->get($index);
                        $eventAttendance = $attendance->where('event_id', $event->getId())->first();

                        return $this->askSetAttendanceEvent($event, $eventAttendance, $option);
                    } else {
                        return $this->repeat(__('botman.general_use_menu'));
                    }
                },
                $botmanService->getKeyBoard($buttonsKeyboard)
            );
        }
    }

    private function verifyAttendance(string $option, $countByRow = 3)
    {

        $tagsCasteller = $this->casteller->tagsArray('id_tag');

        $events = Event::filter($this->casteller->getColla())->today()->visible()->withCastellerTags($tagsCasteller)->eloquentBuilder()
            ->take($max_viewed_events = $this->casteller->getColla()->getConfig()->getMaxAssaigs())
            ->get();

        if ($events->isEmpty()) {

            $this->getBot()->reply(__('botman.conversation_events_ask_offer_events_no_today_events'));

            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuEvents'));

        } else {

            $eventsFormated = BotmanHelper::getEventsFormatted($events, $this->casteller->getColla(), $this->botmanDriver, $countByRow, null, $this->botmanEmojisHelper);

            $this->getBot()->reply($eventsFormated['message'], ['parse_mode' => 'Markdown']);

            $botmanService = new BotmanService($this->botmanDriver);

            $rows = $eventsFormated['rows'];
            $eventsMenu = $botmanService->getEventsMenu($this->casteller);
            $mainMenu = $botmanService->getMainMenu($this->casteller);

            $rows[] = $eventsMenu;
            $rows[] = $mainMenu;
            $questionText = __('botman.conversation_events_ask_verify_event');

            if ($this->botmanDriver instanceof TelegramDriver) {
                $buttonsQuestion = [];
                $buttonsKeyboard = $rows;
            } else {
                $buttonsQuestion = $rows;
                $buttonsKeyboard = [];
            }

            $this->ask(
                $botmanService->getQuestion($questionText, $buttonsQuestion),
                function (Answer $answer) use ($botmanService, $eventsMenu, $mainMenu, $events, $eventsFormated) {
                    $answer = $botmanService->getAnswer($answer);
                    if (in_array($answer, $mainMenu)) {
                        return $this->manageMainMenu($answer);
                    } elseif (in_array($answer, $eventsMenu)) {
                        return $this->manageCurrentMenu($answer);
                    } elseif (in_array($answer, $eventsFormated['options'])) {
                        $index = array_search($answer, $eventsFormated['options']);
                        $event = $events->get($index);

                        return $this->askVerifyAttendanceEvent($event);
                    } else {
                        return $this->repeat(__('botman.general_use_menu'));
                    }
                },
                $botmanService->getKeyBoard($buttonsKeyboard)
            );
        }
    }

    private function askSetAttendanceEvent(Event $event, ?Attendance $eventAttendance, string $option)
    {

        $eventFormatted = BotmanHelper::getEventFormatted($event, $eventAttendance, $this->botmanDriver, $this->botmanEmojisHelper);

        $this->getBot()->reply($eventFormatted, ['parse_mode' => 'Markdown']);

        // SERVICE
        $botmanService = new BotmanService($this->botmanDriver);

        // OPTIONS OF THE QUESTION
        $questionText = __('botman.general_choose_option');

        // MAIN MENU
        if (! empty($event->getStartDate())) {
            if ($event->isOpen()) {
                $attendance = Attendance::getAttendanceCastellerEvent($this->casteller->getId(), $event->getId());
                $attendanceAnswers = (isset($attendance) && $attendance->getStatus() == AttendanceStatus::YES && $event->hasAttendanceAnswers()) ? true : false;
                $companions = (isset($attendance) && $attendance->getStatus() == AttendanceStatus::YES && $event->getCompanions()) ? true : false;

                $eventsAttendanceEventMenu = $botmanService->getAttendanceEventMenu($this->casteller, $attendanceAnswers, $companions);

                $rows[] = $eventsAttendanceEventMenu;
            }
        }
        $eventsMenu = $botmanService->getEventsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $eventsMenu;
        $rows[] = $mainMenu;

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }
        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService, $event, $eventsMenu, $mainMenu, $option) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $eventsMenu)) {
                    return $this->manageCurrentMenu($answer);
                } else {
                    switch ($answer) {
                        case $this->botmanEmojisHelper->getEmoji('eventsAttendanceOk'):
                        case __('botman.eventsAttendanceOk'):
                            Attendance::setStatus($this->casteller->getId(), $event->getId(), Attendance::getStatusId('ok'), Attendance::getSourceId('telegram'));
                            $this->getBot()->reply(__('botman.conversation_events_ask_set_attendance_updated').' ');

                            return $this->askOtherAttendanceOptions($event, $option);
                        case $this->botmanEmojisHelper->getEmoji('eventsAttendanceNok'):
                        case __('botman.eventsAttendanceNok'):
                            Attendance::setStatus($this->casteller->getId(), $event->getId(), Attendance::getStatusId('nok'), Attendance::getSourceId('telegram'));
                            $this->getBot()->reply(__('botman.conversation_events_ask_set_attendance_updated'));

                            return $this->manageCurrentMenu($option);
                        case $this->botmanEmojisHelper->getEmoji('eventsAttendanceUnknown'):
                        case __('botman.eventsAttendanceUnknown'):
                            Attendance::setStatus($this->casteller->getId(), $event->getId(), Attendance::getStatusId('unknown'), Attendance::getSourceId('telegram'));
                            $this->getBot()->reply(__('botman.conversation_events_ask_set_attendance_updated'));

                            return $this->manageCurrentMenu($option);
                        case $this->botmanEmojisHelper->getEmoji('eventsAnswers'):
                        case __('botman.answers'):
                            return $this->askOptions($event, $option);
                        case $this->botmanEmojisHelper->getEmoji('eventsCompanions'):
                        case __('botman.companions'):
                            return $this->askCompanions($event, $option);
                        default:
                            return $this->repeat(__('botman.general_use_menu'));
                    }
                }
            },
            $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askSetAttendanceAllEvents(Collection $events, string $answerAllAttendance, string $option)
    {

        $rows[] = [__('general.yes'), __('general.no')];
        $questionText = $this->botmanEmojisHelper->getEmoji('warning').__('botman.conversation_events_ask_set_all_attendance_question');

        // SERVICE
        $botmanService = new BotmanService($this->botmanDriver);

        // MAIN MENU
        $eventsMenu = $botmanService->getEventsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $eventsMenu;
        $rows[] = $mainMenu;

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
            function (Answer $answer) use ($botmanService, $eventsMenu, $mainMenu, $events, $option, $answerAllAttendance) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $eventsMenu)) {
                    return $this->manageCurrentMenu($answer);
                } else {
                    $interactionType = $this->casteller->getInteractionType();
                    switch ($answer) {
                        case __('general.yes'):
                            if ($interactionType === TelegramInteractionType::ICON) {
                                $newAttendanceStatus = $this->botmanEmojisHelper->getStatusFromEmojiAllAttendance($answerAllAttendance);
                            } else {
                                if ($answerAllAttendance === __('botman.eventsAttendanceAllOk')) {
                                    $newAttendanceStatus = 'ok';
                                }
                                if ($answerAllAttendance === __('botman.eventsAttendanceAllNok')) {
                                    $newAttendanceStatus = 'nok';
                                }
                                if ($answerAllAttendance === __('botman.eventsAttendanceAllUnknown')) {
                                    $newAttendanceStatus = 'unknown';
                                }
                            }

                            foreach ($events as $event) {
                                if (! empty($event->getStartDate())) {
                                    if ($event->isOpen()) {
                                        Attendance::setStatus($this->casteller->getId(), $event->getId(), Attendance::getStatusId($newAttendanceStatus), Attendance::getSourceId('telegram'));
                                    }
                                }
                            }
                            $this->getBot()->reply(__('botman.conversation_events_ask_set_attendance_updated'));

                            return $this->manageCurrentMenu($option);
                        case __('general.no'):
                            return $this->manageCurrentMenu($option);
                        default:
                            return $this->repeat(__('botman.general_use_menu'));
                    }
                }
            },
            $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askOtherAttendanceOptions(Event $event, string $option)
    {

        // SERVICE
        $botmanService = new BotmanService($this->botmanDriver);
        $questionText = __('botman.conversation_events_ask_options_question');

        // MAIN MENU
        //      otherAttendanceOptionsMenu com botmatService no funciona
        $otherAttendanceOptionsMenu = $botmanService->getOtherAttendanceOptionsMenu($this->casteller, $event);

        $eventsMenu = $botmanService->getEventsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $otherAttendanceOptionsMenu;
        $rows[] = $eventsMenu;
        $rows[] = $mainMenu;
        if (count($otherAttendanceOptionsMenu) >= 1) {
            $questionText = __('botman.conversation_events_ask_options_question');
        } else {
            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuEvents'));
        }

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }
        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService, $event, $eventsMenu, $mainMenu, $option) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $eventsMenu)) {
                    return $this->manageCurrentMenu($answer);
                } else {
                    switch ($answer) {
                        case __('botman.answers'):
                        case $this->botmanEmojisHelper->getEmoji('eventsAnswers'):
                            return $this->askOptions($event, $option);
                        case __('botman.companions'):
                        case $this->botmanEmojisHelper->getEmoji('eventsCompanions'):
                            return $this->askCompanions($event, $option);
                        case __('botman.back'):
                        case $this->botmanEmojisHelper->getEmoji('eventsTop'):
                            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuEvents'));
                        default:
                    }
                }
            },
            $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askCompanions(Event $event, string $option)
    {

        // SERVICE
        $botmanService = new BotmanService($this->botmanDriver);

        // MAIN MENU
        $companionsEventsMenu = $botmanService->getCompanionsEventsMenu();
        $eventsMenu = $botmanService->getEventsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $companionsEventsMenu;
        $rows[] = $eventsMenu;
        $rows[] = $mainMenu;
        $questionText = __('botman.conversation_events_ask_companions_question');

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }
        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService, $event, $eventsMenu, $mainMenu, $option, $companionsEventsMenu) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $eventsMenu)) {
                    return $this->manageCurrentMenu($answer);
                } elseif (in_array($answer, $companionsEventsMenu)) {
                    $index = array_search($answer, $companionsEventsMenu);
                    Attendance::setCompanions($this->casteller->getId(), $event->getId(), $index, Attendance::getSourceId('telegram'));
                    $this->getBot()->reply(__('botman.your_companions').$index);

                    return $this->askOtherAttendanceOptions($event, $option);
                } else {
                    return $this->repeat(__('botman.general_use_menu'));
                }
            },
            $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askOptions(Event $event, string $option)
    {

        $answersOptions = $event->getAttendanceAnswersOptions();
        $attendance = Attendance::getAttendanceCastellerEvent($this->casteller->getId(), $event->getId());
        $customAnswersCasteller = $attendance->getOptionsNames();

        if (! empty($answersOptions)) {
            $answersOptions[0] = __('general.none');
        }

        [$answersOptionsFormated, $numAnswer] = BotmanHelper::getCustomAnswersFormatted($answersOptions, $customAnswersCasteller, $this->botmanEmojisHelper);
        // SERVICE
        $botmanService = new BotmanService($this->botmanDriver);
        // MAIN MENU
        $eventsMenu = $botmanService->getEventsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);
        $rows = BotmanHelper::splitMenuByRows(array_merge($numAnswer, $botmanService->getBackButton($this->casteller)), 6);
        $rows[] = $eventsMenu;
        $rows[] = $mainMenu;

        $answersOptionsFormatedMessage = BotmanHelper::getConcatenatedKeyValueArrayList($answersOptionsFormated, $this->botmanDriver);

        $this->getBot()->reply(__('botman.answers').':'.$this->lb.$this->lb.$answersOptionsFormatedMessage);

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
            function (Answer $answer) use ($botmanService, $event, $eventsMenu, $mainMenu, $option, $answersOptions, $answersOptionsFormated, $numAnswer) {
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $eventsMenu)) {
                    return $this->manageCurrentMenu($answer);
                } elseif (in_array($answer, $numAnswer)) {
                    $answer = str_replace($this->botmanEmojisHelper->getEmoji('check').' ', '', $botmanService->getAnswer($answer));
                    $answer = $answersOptionsFormated[intval($answer) - 1];
                    $answer = str_replace($this->botmanEmojisHelper->getEmoji('check').' ', '', $answer);
                    if ($answer == __('general.none')) {
                        Attendance::setAnswers($this->casteller->getId(), $event->getId(), [], Attendance::getSourceId('telegram'));
                        $this->getBot()->reply(__('botman.your_answers').__('general.none'));
                    } else {
                        $attendance = Attendance::getAttendanceCastellerEvent($this->casteller->getId(), $event->getId());
                        $optionsArray = [array_search($answer, $answersOptions)];

                        if ($attendance && ! is_null($attendance->getOptions())) {
                            if (in_array($optionsArray[0], $attendance->getOptions())) {
                                $optionsArray = array_diff($attendance->getOptions(), $optionsArray);
                            } else {
                                $optionsArray = array_merge($optionsArray, $attendance->getOptions());
                            }
                        }

                        Attendance::setAnswers($this->casteller->getId(), $event->getId(), array_values($optionsArray), Attendance::getSourceId('telegram'));

                        $attendance = Attendance::getAttendanceCastellerEvent($this->casteller->getId(), $event->getId());
                        $this->getBot()->reply(__('botman.your_answers').$this->lb.BotmanHelper::getOptionNames($attendance, $this->botmanDriver));
                    }

                    return $this->askOptions($event, $option);
                } elseif ($answer == __('botman.back') | $answer == $this->botmanEmojisHelper->getEmoji('eventsTop')) {
                    $attendance = Attendance::getAttendanceCasteller($this->casteller->getId());
                    $eventAttendance = $attendance->where('event_id', $event->getId())->first();

                    return $this->askSetAttendanceEvent($event, $eventAttendance, $option);
                } else {
                    return $this->repeat(__('botman.general_use_menu'));
                }
            },
            $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askVerifyAttendanceEvent(Event $event)
    {
        $eventFormatted = BotmanHelper::getEventFormatted($event, null, $this->botmanDriver, $this->botmanEmojisHelper);

        $this->getBot()->reply($eventFormatted, ['parse_mode' => 'Markdown']);

        $botmanService = new BotmanService($this->botmanDriver);

        $eventsMenu = $botmanService->getEventsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $eventsMenu;
        $rows[] = $mainMenu;

        $questionText = __('botman.conversation_events_ask_verification_code');

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($event, $mainMenu, $eventsMenu) {
                $token = $answer->getText();
                $verification = TOTPService::verifyCode($event, $token);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $eventsMenu)) {
                    return $this->manageCurrentMenu($answer);
                } elseif ($verification == true) {
                    Attendance::setStatusVerified($this->casteller->getId(), $event->getId(), Attendance::getStatusId('ok'), Attendance::getSourceId('telegram'));
                    $this->getBot()->reply(__('tokentotp.success_verified', ['event' => $event->getName()]));

                    return $this->manageCurrentMenu($answer);
                } elseif ($verification == false) {
                    $this->getBot()->reply(__('tokentotp.invalid_code'));

                    return $this->askVerifyAttendanceEvent($event);
                } else {
                    return $this->repeat(__('botman.general_use_menu'));
                }
            }
        );
    }

    private function manageMainMenu(string $answer = '')
    {
        return $this->getBot()->startConversation(new FempinyaConversation($this->botmanDriver, $this->casteller, $answer));
    }

    private function manageCurrentMenu(string $answer = '')
    {
        return $this->getBot()->startConversation(new EventsConversation($this->botmanDriver, $this->casteller, $answer));
    }
}
