<?php

namespace App\Conversations;

use App\Attendance;
use App\Casteller;
use App\Enums\EventTypeNameEnum;
use App\Event;
use App\Helpers\BotmanEmojisHelper;
use App\Helpers\BotmanHelper;
use App\Services\BotmanService;
use App\Services\NotificationService;
use App\Tag;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\TelegramDriver;

class TecnicaConversation extends Conversation
{
    const MAXSENTTELEGRAMSEGONS = 29;

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

    public function setCasteller(Casteller $casteller)
    {
        $this->casteller = $casteller;
    }

    public function run()
    {
        switch ($this->option) {
            case $this->botmanEmojisHelper->getEmoji('tecnicaAttendance'):
            case __('botman.tecnicaAttendance'):
                $this->askOfferEventTypes('Attendance', $this->option);
                break;
            case $this->botmanEmojisHelper->getEmoji('tecnicaReminders'):
            case __('botman.tecnicaReminders'):
                $this->askReminderType('Reminders', $this->option);
                break;
            case $this->botmanEmojisHelper->getEmoji('tecnicaSearch'):
            case __('botman.tecnicaSearch'):
                $this->askSearhCastellers($this->option);
                break;
            default:
                $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuHome'));
        }
    }

    private function askSearhCastellers(string $option)
    {
        $questionText = $this->botmanEmojisHelper->getEmoji('tecnicaSearch').' '.__('botman.help_tecnica_search').$this->lb.$this->lb;
        $questionText .= __('botman.conversation_edit_name_search_minim_max');

        // // SERVICE
        $botmanService = new BotmanService($this->botmanDriver);

        $this->ask(
            $botmanService->getQuestion($questionText),
            function (Answer $answer) {
                $searchString = $answer->getText();
                if (strlen($searchString) >= 2 and strlen($searchString) < 100) {
                    // passar query a Casteller Model
                    $colla = $this->casteller->getCollaId();
                    $castellers = Casteller::getCastellersBySearchString($searchString, $colla);
                    if (is_null($castellers)) {
                        $this->getBot()->reply(__('botman.conversation_search_no_casteller_found'));

                        return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuTecnica'));
                    } else {

                        $limit = 20; // limit de resutalts a presentar
                        $count = 0;
                        $recordsTotal = count($castellers);
                        $message = __('botman.conversation_search_fount_person').' '.$this->lb;
                        $message = ' *'.$this->casteller->getColla()->getName().'*'.$this->lb.$this->lb;
                        foreach ($castellers as $casteller) {
                            $count++;
                            if ($count < $limit) {
                                $message .= BotmanHelper::getCastellerFormatted($casteller, $this->botmanDriver, $this->botmanEmojisHelper);
                                $message .= $this->lb;
                            }
                        }
                        $this->getBot()->reply(' '.$message, ['parse_mode' => 'Markdown']);
                        if ($count > $limit) {
                            $message = __('botman.conversation_search_counter', ['count' => $limit, 'total' => $recordsTotal]);
                            $this->getBot()->reply($message, ['parse_mode' => 'Markdown']);

                            return $this->askSearhCastellers($searchString);
                        }

                        return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuTecnica'));
                    }
                } else {
                    $this->getBot()->reply(__('botman.general_message_length_is_incorrect').' '.strlen($searchString).' ');

                    return $this->askSearhCastellers($searchString);
                }
            }
        );
    }

    private function askReminderType(string $option)
    {

        $botmanService = new BotmanService($this->botmanDriver);

        $reminderTypesMenu = $botmanService->getEventsMenuReminders($this->casteller);
        $tecnicaMenu = $botmanService->getTecnicaMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $reminderTypesMenu;
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
            function (Answer $answer) use ($botmanService, $tecnicaMenu, $mainMenu, $reminderTypesMenu, $option) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $tecnicaMenu)) {
                    return $this->manageCurrentMenu($answer);
                } elseif (in_array($answer, $reminderTypesMenu)) {
                    switch ($answer) {
                        case $this->botmanEmojisHelper->getEmoji('tecnicaSendRemindersEvents'):
                        case __('botman.reminder_about_event'):
                            return $this->askOfferEventTypes('Reminders', $this->option);
                        case $this->botmanEmojisHelper->getEmoji('Email'):
                        case __('botman.reminder_general'):
                            return $this->askRemindersMessageTags($event = null, $option);
                        default:
                            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuHome'));
                    }
                } else {
                    return $this->repeat(__('botman.general_use_menu'));
                }
            },
            $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askOfferEventTypes(string $type, string $option)
    {

        $botmanService = new BotmanService($this->botmanDriver);

        $eventTypesMenu = $botmanService->getEventsMenuTecnica($this->casteller);
        $tecnicaMenu = $botmanService->getTecnicaMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $eventTypesMenu;
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
            function (Answer $answer) use ($botmanService, $tecnicaMenu, $mainMenu, $eventTypesMenu, $type) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $tecnicaMenu)) {
                    return $this->manageCurrentMenu($answer);
                } elseif (in_array($answer, $eventTypesMenu)) {
                    switch ($answer) {
                        case $this->botmanEmojisHelper->getEmoji('eventsActuacio'):
                        case __('botman.eventsActuacio'):
                            return $this->askOfferEvents(EventTypeNameEnum::Actuacio()->value(), $this->option, 3, $type);
                        case $this->botmanEmojisHelper->getEmoji('eventsAssaig'):
                        case __('botman.eventsAssaig'):
                            return $this->askOfferEvents(EventTypeNameEnum::Assaig()->value(), $this->option, 3, $type);
                        case $this->botmanEmojisHelper->getEmoji('eventsActivitat'):
                        case __('botman.eventsActivitat'):
                            return $this->askOfferEvents(EventTypeNameEnum::Activitat()->value(), $this->option, 3, $type);
                        default:
                            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuHome'));
                    }
                } else {
                    return $this->repeat(__('botman.general_use_menu'));
                }
            },
            $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askOfferEvents(string $tipus, string $option, int $countByRow = 3, string $type = 'Attendance')
    {

        $column_order = 'start_date';
        $dir = 'ASC';
        //$this->open_date < $date && $this->close_date > $date
        $eventsFilter = Event::filter($this->casteller->getColla());

        $max_viewed_events = $this->casteller->getColla()->getConfig()->getMaxEvents($tipus);

        $events = Event::filter($this->casteller->getColla())
            ->upcoming()
            ->visible()
            ->eloquentBuilder()
            ->where('type', '=', Event::getTypeId($tipus))
            ->take($max_viewed_events)
            ->orderBy($column_order, $dir)
            ->get();

        if ($events->isEmpty()) {

            $this->getBot()->reply(__('botman.conversation_events_ask_offer_events_no_events'));

            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuTecnica'));
        } else {

            $eventsFormated = BotmanHelper::getEventsFormatted($events, $this->casteller->getColla(), $this->botmanDriver, $countByRow, null, $this->botmanEmojisHelper);

            $this->getBot()->reply($eventsFormated['message'], ['parse_mode' => 'Markdown']);

            $botmanService = new BotmanService($this->botmanDriver);

            $rows = $eventsFormated['rows'];

            $eventsMenu = $botmanService->getEventsMenuTecnica($this->casteller);
            $mainMenu = $botmanService->getMainMenu($this->casteller);

            $rows[] = $eventsMenu;
            $rows[] = $mainMenu;
            if ($type === 'Reminders') {
                $questionText = __('botman.conversation_ask_offer_event_reminder');
            } else {
                $questionText = __('botman.conversation_ask_offer_event_attendance');
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
                function (Answer $answer) use ($botmanService, $eventsMenu, $mainMenu, $events, $eventsFormated, $option, $type) {
                    $answer = $botmanService->getAnswer($answer);
                    if (in_array($answer, $mainMenu)) {
                        return $this->manageMainMenu($answer);
                    } elseif (in_array($answer, $eventsMenu)) {
                        // If they choose a different type of Event, offer the Event of that type
                        switch ($answer) {
                            case $this->botmanEmojisHelper->getEmoji('eventsActuacio'):
                            case __('botman.eventsActuacio'):
                                return $this->askOfferEvents(EventTypeNameEnum::Actuacio()->value(), $this->option, 3, $type);
                            case $this->botmanEmojisHelper->getEmoji('eventsAssaig'):
                            case __('botman.eventsAssaig'):
                                return $this->askOfferEvents(EventTypeNameEnum::Assaig()->value(), $this->option, 3, $type);
                            case $this->botmanEmojisHelper->getEmoji('eventsActivitat'):
                            case __('botman.eventsActivitat'):
                                return $this->askOfferEvents(EventTypeNameEnum::Activitat()->value(), $this->option, 3, $type);
                        }
                    } elseif (in_array($answer, $eventsFormated['options'])) {
                        $index = array_search($answer, $eventsFormated['options']);
                        $event = $events->get($index);
                        $eventFormatted = BotmanHelper::getEventTecnicaFormatted($event, $this->botmanDriver, $this->botmanEmojisHelper);
                        $this->getBot()->reply($eventFormatted, ['parse_mode' => 'Markdown']);

                        if ($type === 'Attendance') {
                            return $this->askCastellersAttendanceEvent($event, $option, $type);
                        }
                        if ($type === 'Reminders') {
                            return $this->askCastellersRemindersEvent($event, $option, $type);
                        }
                    } else {
                        return $this->repeat(__('botman.general_use_menu'));
                    }
                },
                $botmanService->getKeyBoard($buttonsKeyboard)
            );
        }
    }

    private function askCastellersAttendanceEvent(Event $event, string $option, string $type = 'Attendance')
    {
        $this->getBot()->reply($event, ['parse_mode' => 'Markdown']);

        $questionText = __('botman.conversation_ask_castellers_attendance_event');

        // SERVICE
        $botmanService = new BotmanService($this->botmanDriver);

        // MAIN MENU
        $eventsAttendanceEventMenu = $botmanService->getAttendanceEventMenu($this->casteller);
        $eventsAttendanceEventAnswersMenu = $botmanService->getAttendanceEventAnswersMenu($this->casteller);
        $eventsMenu = $botmanService->getEventsMenuTecnica($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $eventsAttendanceEventMenu;
        $rows[] = $eventsAttendanceEventAnswersMenu;
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
            function (Answer $answer) use ($botmanService, $eventsAttendanceEventAnswersMenu, $eventsMenu, $mainMenu, $event, $type) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $eventsMenu)) {
                    // If they choose a different type of Event, offer the Event of that type
                    switch ($answer) {
                        case $this->botmanEmojisHelper->getEmoji('eventsActuacio'):
                        case __('botman.eventsActuacio'):
                            return $this->askOfferEvents(EventTypeNameEnum::Actuacio()->value(), $this->option, 3, $type);
                        case $this->botmanEmojisHelper->getEmoji('eventsAssaig'):
                        case __('botman.eventsAssaig'):
                            return $this->askOfferEvents(EventTypeNameEnum::Assaig()->value(), $this->option, 3, $type);
                        case $this->botmanEmojisHelper->getEmoji('eventsActivitat'):
                        case __('botman.eventsActivitat'):
                            return $this->askOfferEvents(EventTypeNameEnum::Activitat()->value(), $this->option, 3, $type);
                    }
                } elseif (in_array($answer, $eventsAttendanceEventAnswersMenu)) {
                    $attendances = Attendance::getAttendanceEvent($event->getId());

                    $attendanceAnswers = [];

                    foreach ($attendances as $attendance) {
                        $attendanceAnswers[] = $attendance->getOptionsNames();
                    }

                    $countedAnswers = array_count_values(array_merge([], ...$attendanceAnswers));

                    foreach ($countedAnswers as $key => $val) {
                        $this->getBot()->reply($key.': '.$val);
                    }

                    return $this->repeat(__('botman.conversation_ask_castellers_attendance_event'));
                } else {
                    $display = '';

                    switch ($answer) {
                        case $this->botmanEmojisHelper->getEmoji('eventsAttendanceOk'):
                        case __('botman.eventsAttendanceOk'):
                            $attendances = Attendance::getAttendanceEventByStatus($event->getId(), 'ok');
                            break;
                        case $this->botmanEmojisHelper->getEmoji('eventsAttendanceNok'):
                        case __('botman.eventsAttendanceNok'):
                            $attendances = Attendance::getAttendanceEventByStatus($event->getId(), 'nok');
                            break;
                        case $this->botmanEmojisHelper->getEmoji('eventsAttendanceUnknown'):
                        case __('botman.eventsAttendanceUnknown'):
                            $attendances = Attendance::getAttendanceEventByStatus($event->getId(), 'unknown');
                            break;
                        default:
                            return $this->repeat(__('botman.general_use_menu'));
                    }
                    $attendanceSenders = [];
                    foreach ($attendances as $attendance) {
                        $qui = $attendance->getCasteller();
                        $attendanceSenders[$qui->getDisplayName()] = $this->botmanEmojisHelper->getEmoji('eventsRightArrow').' '.$qui->getDisplayName().$this->lb;
                        //  limit de caràcters a cada replay
                    }
                    ksort($attendanceSenders, SORT_STRING);
                    $display = '';
                    foreach ($attendanceSenders as $attendanceSender) {
                        $display .= $attendanceSender;
                        if (strlen($display) > 4000) {
                            $this->getBot()->reply(__('attendance.attendance_status').$this->lb.$display);
                            $display = '';
                        }
                    }
                    $this->getBot()->reply(__('attendance.attendance_status').$this->lb.$display);

                    return $this->repeat(__('botman.conversation_ask_castellers_attendance_event'));
                }
            },
            $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    /**
     * presentem opcions per enviar recordatori, amb variants:
     * 1. Recordatori ràpid a les persones actives que encara NO han confirmat,
     *    TODO: El text podia ser plantilles personalitzades per a cada colla en configuracio/colla
     * 2. Recodatori personalitzat seleccionant tags de castellers
     *    TODO: El text podria ser plantilles o editar on line
     */
    private function askCastellersRemindersEvent(?Event $event, string $option, string $type = 'Reminders')
    {
        $questionText = $this->botmanEmojisHelper->getEmoji('tecnicaSendRemindersExpres').' '.__('botman.help_tecnica_send_reminders_expres').$this->lb;
        $questionText .= $this->botmanEmojisHelper->getEmoji('tecnicaSendRemindersTags').' '.__('botman.help_tecnica_send_reminders_tags').$this->lb;

        // SERVICE
        $botmanService = new BotmanService($this->botmanDriver);

        // MAIN MENU
        $getRemindersMenu = $botmanService->getRemindersMenu($this->casteller);
        $eventsMenu = $botmanService->getEventsMenuTecnica($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $getRemindersMenu;
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
            function (Answer $answer) use ($botmanService, $eventsMenu, $mainMenu, $event, $option, $type) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $eventsMenu)) {
                    // If they choose a different type of Event, offer the Event of that type
                    switch ($answer) {
                        case $this->botmanEmojisHelper->getEmoji('eventsActuacio'):
                        case __('botman.eventsActuacio'):
                            return $this->askOfferEvents(EventTypeNameEnum::Actuacio()->value(), $this->option, 3, $type);
                        case $this->botmanEmojisHelper->getEmoji('eventsAssaig'):
                        case __('botman.eventsAssaig'):
                            return $this->askOfferEvents(EventTypeNameEnum::Assaig()->value(), $this->option, 3, $type);
                        case $this->botmanEmojisHelper->getEmoji('eventsActivitat'):
                        case __('botman.eventsActivitat'):
                            return $this->askOfferEvents(EventTypeNameEnum::Activitat()->value(), $this->option, 3, $type);
                    }
                } else {
                    $attendances = Attendance::getAttendanceEvent($event->getId());

                    switch ($answer) {
                        case $this->botmanEmojisHelper->getEmoji('tecnicaSendRemindersExpres'):
                        case __('botman.tecnicaSendRemindersExpres'):
                            $this->getBot()->reply(__('botman.calculating_wait'), ['parse_mode' => 'Markdown']);
                            NotificationService::SendAttendanceReminder($event, $this->casteller);
                            $message = $this->botmanEmojisHelper->getEmoji('tecnicaSendRemindersExpres').$this->lb;
                            $message .= __('botman.reminder_sent').$this->lb.$this->lb;
                            $this->getBot()->reply($message, ['parse_mode' => 'Markdown']);
                            break;

                        case $this->botmanEmojisHelper->getEmoji('tecnicaSendRemindersTags'):
                        case __('botman.tecnicaSendRemindersTags'):
                            $message = $this->botmanEmojisHelper->getEmoji('warning').$this->lb;

                            return $this->askRemindersMessageTags($event, $option);
                            break;

                        default:
                            return $this->repeat(__('botman.general_use_menu'));
                    }

                    return $this->manageCurrentMenu($option);
                }
            },
            $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askRemindersMessageTags(?Event $event, string $option)
    {
        // $this->getBot()->reply($event, ['parse_mode' => 'Markdown']);

        $tags = $this->casteller->getColla()->getTags();

        foreach ($tags as $tag) {
            $tagsNames[] = $tag->getName();
        }

        $tagsNames[] = __('casteller.everybody');

        $rows = BotmanHelper::splitMenuByRows($tagsNames, 6);

        // // SERVICE
        $botmanService = new BotmanService($this->botmanDriver);

        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $mainMenu;

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $questionText = __('botman.conversation_edit_recordatori_tag');

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService, $event, $option, $mainMenu, $tagsNames) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $tagsNames)) {
                    return $this->askRemindersMessage($event, $option, $answer);
                } else {
                    return $this->repeat(__('botman.general_use_menu'));
                }
            },
            $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askRemindersMessage(?Event $event, string $option, ?string $tag = null)
    {

        $questionText = $this->botmanEmojisHelper->getEmoji('tecnicaSendRemindersOnline').' '.__('botman.help_tecnica_send_reminders').$this->lb.$this->lb;
        $questionText .= __('botman.conversation_edit_recordatori_per_enviar_minim_max');

        // // SERVICE
        $botmanService = new BotmanService($this->botmanDriver);

        $this->ask(
            $botmanService->getQuestion($questionText),
            function (Answer $answer) use ($event, $option, $tag) {
                $messageReminder = $answer->getText();
                if (strlen($messageReminder) >= 20 and strlen($messageReminder) <= 4000) {
                    if (isset($tag)) {
                        return $this->askOkMessageReminder($messageReminder, $event, $option, $tag);
                    } else {
                        return $this->askOkMessageReminder($messageReminder, $event, $option);
                    }
                } else {
                    $this->getBot()->reply($this->botmanEmojisHelper->getEmoji('warning').trans_choice('botman.conversation_edit_recordatori_per_enviar_minim_max_warning', strlen($messageReminder)));

                    return $this->askCastellersRemindersEvent($event, $option);
                }
            }
        );
    }

    private function askOkMessageReminder(string $messageReminder, ?Event $event, string $option, ?string $tag = null)
    {

        $rows[] = [__('general.yes'), __('general.no')];
        $questionText = $messageReminder.$this->lb.$this->lb;
        $questionText .= __('botman.conversation_is_ok_message_for_reminder');

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
            function (Answer $answer) use ($botmanService, $event, $option, $messageReminder, $tag) {
                switch ($botmanService->getAnswer($answer)) {
                    case __('general.no'):
                        $this->getBot()->reply(__('botman.conversation_is_no_ok_message_for_reminder'));

                        return $this->askCastellersRemindersEvent($event, $option);
                    case __('general.yes'):
                        $this->getBot()->reply(__('botman.conversation_is_yes_ok_message_for_reminder'));

                        $colla = $this->casteller->getColla();
                        $tags = ($tag == __('casteller.everybody')) ? [] : [Tag::getTagByName($tag, $colla)->getId()];
                        if (isset($event)) {
                            NotificationService::SendAttendanceReminder($event, $this->casteller, $tags, $messageReminder);
                        } else {
                            NotificationService::SendMessage($colla, __('botman.custom_reminder'), $messageReminder, null, $this->casteller->getId(), $tags);
                        }

                        // presentem resum
                        $message = $this->botmanEmojisHelper->getEmoji('tecnicaSendRemindersExpres').$this->lb;
                        $message .= __('botman.reminder_sent').$this->lb.$this->lb;

                        $this->getBot()->reply($message, ['parse_mode' => 'Markdown']);

                        // Tornar a menu principal
                        return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuHome'));
                    default:
                        return $this->repeat(__('botman.general_use_menu'));
                }
            },
            $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function manageMainMenu(string $answer = '')
    {
        return $this->getBot()->startConversation(new FempinyaConversation($this->botmanDriver, $this->casteller, $answer));
    }

    private function manageCurrentMenu(string $answer = '')
    {
        return $this->getBot()->startConversation(new TecnicaConversation($this->botmanDriver, $this->casteller, $answer));
    }
}
