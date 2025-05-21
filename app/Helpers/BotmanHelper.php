<?php

namespace App\Helpers;

use App\Attendance;
use App\Casteller;
use App\Colla;
use App\Event;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\Drivers\Web\WebDriver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Emoji\Emoji;

class BotmanHelper
{
    /**
     * Returns Line Break depending on the Driver
     */
    public static function getLbTag(DriverInterface $driver): string
    {

        if ($driver instanceof TelegramDriver) {
            return " \n";
        } elseif ($driver instanceof WebDriver) {
            return ' <br> ';
        } else {
            return '';
        }
    }

    /**
     * Returns Bold string depending on the Driver
     */
    public static function setBold(DriverInterface $driver, string $string): string
    {
        if ($driver instanceof TelegramDriver) {
            return '*'.$string.'*';
        } elseif ($driver instanceof WebDriver) {
            return '<b>'.$string.'</b>';
        } else {
            return $string;
        }
    }

    /**
     * Returns a link string depending on the Driver
     */
    public static function setLink(DriverInterface $driver, string $string, string $link): string
    {
        if ($driver instanceof TelegramDriver) {
            return '['.$string.']('.$link.')';
        } elseif ($driver instanceof WebDriver) {
            return "<a href=\"$link\">".$string.'</a>';
        } else {
            return $string;
        }
    }

    /**
     * Returns List of Linked Castellers Formatted for Botman
     */
    public static function getLinkedCastellersFormatted(Collection $castellers, DriverInterface $driver, bool $addNumbers = false): array
    {
        $return = [
            'message' => '',
            'options' => [],
        ];

        foreach ($castellers as $index => $casteller) {
            $optionCasteller = '';
            $optionNumber = '';

            if ($addNumbers) {
                $number = 'keycap'.$index + 1;
                $optionCasteller = Emoji::$number().' ';
                $optionNumber = Emoji::$number();
            }

            $optionCasteller .= $casteller->getDisplayName().' ('.$casteller->getColla()->getName().')';

            if ($casteller->getCastellerConfig()->getTelegramEnabled() === 0) {
                $optionCasteller .= ' | '.strtoupper(BotmanHelper::setBold($driver, __('general.deshabilitat')));
            }

            $return['message'] .= $optionCasteller.BotmanHelper::getLbTag($driver);
            $return['options'][$index] = ltrim($optionCasteller);
        }

        return $return;
    }

    /**
     * Returns List of Events Formatted for Botman
     *
     * @param  string  $eventType
     */
    public static function getEventsFormatted(Collection $events, Colla $colla, DriverInterface $driver, int $countByRow, ?Collection $attendance, BotmanEmojisHelper $botmanEmojisHelper): array
    {
        $lb = BotmanHelper::getLbTag($driver);

        $return = [
            'message' => '',
            'options' => [],
            'rows' => [],
        ];

        $eventsInRow = [];

        foreach ($events as $index => $event) {
            $eventType = $event->getType();
            $eventTypeName = Event::TYPES[$eventType];

            switch ($eventTypeName) {
                case 'activitat':
                    $isGoogleCalendarEnabled = $colla->getConfig()->getGoogleCalendarEnabledActivitats();
                    break;
                case 'actuacio':
                    $isGoogleCalendarEnabled = $colla->getConfig()->getGoogleCalendarEnabledActuacions();
                    break;
                case 'assaig':
                    $isGoogleCalendarEnabled = $colla->getConfig()->getGoogleCalendarEnabledAssaigs();
                    break;
                default:
                    $isGoogleCalendarEnabled = false;
                    break;
            }

            $urlGoogleCalendar = $event->getUrlGoogleCalendar();

            $eventAttendance = (! is_null($attendance)) ? $attendance->where('event_id', $event->getId())->first() : null;

            $msgDate = '';
            if (! empty($event->getStartDate())) {
                setlocale(LC_ALL, 'ca_ES');
                $start_date = Carbon::parse($event->getStartDate());
                $msgDate = $start_date->isoFormat('DD/MM HH:mm\h');
            }

            if (! empty($event->getStartDate())) {
                if ($event->isOpen()) {
                    $optionEvent = $botmanEmojisHelper->getEmojiEventType($event::TYPES[$event->getType()]).' '.$msgDate;
                } else {
                    $optionEvent = $botmanEmojisHelper->getEmoji('locked').' '.$msgDate;
                }
            }

            $messageEvent = $optionEvent.' '.$event->getName();

            if (! is_null($eventAttendance)) {
                $messageEvent .= ' '.$botmanEmojisHelper->getEmojiAttendance($eventAttendance);
                if (! is_null($eventAttendance->companions)) {
                    $messageEvent .= $botmanEmojisHelper->getEmoji('eventsCompanions').' '.$eventAttendance->companions;
                }

                // Obtenim les respostes personalitzades

                $myAnswersOptions = self::getOptionNames($eventAttendance, $driver, $lb.'  - ');

                if (count($event->getAttendanceAnswersOptions())) {
                    if ($myAnswersOptions != '') {
                        $messageEvent .= $botmanEmojisHelper->getEmoji('eventsAnswers').$lb.'  - '.$myAnswersOptions;
                    } else {
                        $messageEvent .= $botmanEmojisHelper->getEmoji('eventsAnswers');
                    }
                }
            }

            // Añadir el enlace urlGoogleCalendar como un hipervínculo en el mensaje del evento
            if (! empty($urlGoogleCalendar) && $isGoogleCalendarEnabled) {
                $messageEvent .= ' ['.__('event.add_google_calendar').']('.$urlGoogleCalendar.')';
            }

            $messageEvent .= BotmanHelper::getLbTag($driver);
            $eventsInRow[] = $optionEvent;

            // we check if we need to create another row
            if ((($index + 1) % $countByRow === 0)) {
                $return['rows'][] = $eventsInRow;
                $eventsInRow = [];
            }

            $return['message'] .= $messageEvent;
            $return['options'][$index] = ltrim($optionEvent);
        }

        // pending rows still not added
        $return['rows'][] = $eventsInRow;

        return $return;

    }

    /**
     * Returns Event information Formatted for Botman
     */
    public static function getEventFormatted(Event $event, ?Attendance $eventAttendance, DriverInterface $driver, BotmanEmojisHelper $botmanEmojisHelper): string
    {

        $lb = BotmanHelper::getLbTag($driver);

        $msgDate = '';
        if (! empty($event->getStartDate())) {
            setlocale(LC_ALL, 'ca_ES');
            $start_date = Carbon::parse($event->getStartDate());
            $msgDate = $start_date->isoFormat('DD/MM HH:mm\h');
        }
        $msgEvent = '';
        $msgCompanions = '';
        if ($event->getCompanions() === true) {
            $msgEvent .= __('botman.conversation_events_display_event_admit_companions_question').' '.__('general.yes').$lb;
            $numberOfCompanions = (is_null($eventAttendance) || is_null($eventAttendance->getCompanions())) ? 0 : $eventAttendance->getCompanions();
            $msgCompanions .= trans_choice('botman.conversation_events_display_event_number_of_companions', $numberOfCompanions);
        } else {
            $msgEvent .= __('botman.conversation_events_display_event_admit_companions_question').' '.__('general.no').$lb;
        }

        $msgOptions = '';
        if ($event->hasAttendanceAnswers() === true) {
            $msgEvent .= __('botman.conversation_events_display_event_admit_options_question').' '.__('general.yes').$lb;

            // Obtenim les respostes del castellers
            if (! is_null($eventAttendance) && ! is_null($eventAttendance->getOptions())) {
                // Obtenim les respostes personalitzades
                $myAnswersOptions = self::getOptionNames($eventAttendance, $driver);
                $msgOptions .= ($myAnswersOptions === '') ? __('botman.your_answers').$lb.__('general.none') : __('botman.your_answers').$lb.$myAnswersOptions;

            }
        } else {
            $msgEvent .= __('botman.conversation_events_display_event_admit_options_question').' '.__('general.no').$lb;
        }

        if (! empty($event->getStartDate())) {
            if ($event->isOpen()) {
                $msgEvent .= __('botman.conversation_events_display_event_admit_ask_question').' '.__('general.yes').$lb;
            } else {
                $msgEvent .= __('botman.conversation_events_display_event_admit_ask_question').' '.__('general.no').$lb;
            }
        }

        $message = $botmanEmojisHelper->getEmoji('eventsRightArrow').' '.BotmanHelper::setBold($driver, $event->getName()).$lb;
        $message .= $botmanEmojisHelper->getEmoji('eventsDate').' '.$msgDate.$lb;
        if ($event->getAddress()) {
            if ($event->getLocationLink()) {
                $message .= $botmanEmojisHelper->getEmoji('eventsLocation').BotmanHelper::setLink($driver, $event->getAddress(), $event->getLocationLink()).$lb;
            } else {
                $message .= $botmanEmojisHelper->getEmoji('eventsLocation').$event->getAddress().$lb;
            }
        }
        if ($event->getComments()) {
            $message .= $botmanEmojisHelper->getEmoji('eventsInfo').$event->getComments().$lb;
        }

        $message .= $lb.$msgEvent;
        // Presents the values of the assistance of a human tower
        $message .= $lb.'*'.__('botman.you_have_chosen').'*'.$lb;
        $message .= ' '.__('attendance.attendance').': '.$botmanEmojisHelper->getEmojiAttendance($eventAttendance).$lb;
        if ($event->getCompanions() === true) {
            $message .= $botmanEmojisHelper->getEmoji('eventsCompanions').' '.$msgCompanions.$lb;
        }
        if ($msgOptions != '') {
            $message .= $botmanEmojisHelper->getEmoji('eventsAnswers').' '.$msgOptions.$lb;
        }

        $message .= $lb;

        return $message;

    }

    public static function getEventTecnicaFormatted(Event $event, DriverInterface $driver, BotmanEmojisHelper $botmanEmojisHelper): string
    {

        $lb = BotmanHelper::getLbTag($driver);

        $msgDate = '';
        if (! empty($event->getStartDate())) {
            setlocale(LC_ALL, 'ca_ES');
            $start_date = Carbon::parse($event->getStartDate());
            $msgDate = $start_date->isoFormat('DD/MM HH:mm\h');
        }

        $message = $botmanEmojisHelper->getEmoji('eventsRightArrow').' '.BotmanHelper::setBold($driver, $event->getName()).$lb;
        $message .= $botmanEmojisHelper->getEmoji('eventsDate').' '.$msgDate.$lb;
        $message .= $botmanEmojisHelper->getEmoji('tecnicaAttendance').' '.__('attendance.attendance').$lb;
        $message .= $botmanEmojisHelper->getEmoji('eventsAttendanceOk').' '.$event->countAttenders()['ok'].$lb;
        $message .= $botmanEmojisHelper->getEmoji('eventsAttendanceNok').' '.$event->countAttenders()['nok'].$lb;
        $message .= $botmanEmojisHelper->getEmoji('eventsAttendanceUnknown').' '.$event->countAttenders()['unknown'].$lb;
        $message .= $botmanEmojisHelper->getEmoji('eventsCompanions').' '.$event->countAttenders()['companions'].$lb;

        $message .= $lb;

        return $message;

    }

    public static function getRemindersFormatted(Event $event, ?Casteller $casteller, DriverInterface $driver, BotmanEmojisHelper $botmanEmojisHelper): string
    {

        $lb = BotmanHelper::getLbTag($driver);

        $msgDate = '';
        if (! empty($event->getStartDate())) {
            setlocale(LC_ALL, 'ca_ES');
            $start_date = Carbon::parse($event->getStartDate());
            $msgDate = $start_date->isoFormat('DD/MM HH:mm\h');
        }

        $message = $botmanEmojisHelper->getEmoji('warning').' '.__('botman.reminders').$lb;
        $message .= __('botman.reminders_message', ['name' => $casteller->getDisplayName()]).$lb.$lb;

        $message .= $botmanEmojisHelper->getEmoji('eventsRightArrow').' '.BotmanHelper::setBold($driver, $event->getName()).$lb;
        $message .= $botmanEmojisHelper->getEmoji('eventsDate').' '.$msgDate.$lb;
        $message .= $botmanEmojisHelper->getEmoji('tecnicaAttendance').' '.__('attendance.attendance').$lb;
        $message .= $botmanEmojisHelper->getEmoji('eventsCompanions').' '.$casteller->getDisplayName().$lb;

        $message .= $lb;

        return $message;

    }

    public static function getCastellerFormatted(?casteller $casteller, DriverInterface $driver, BotmanEmojisHelper $botmanEmojisHelper): string
    {

        $lb = BotmanHelper::getLbTag($driver);
        $casteller_config = $casteller->getCastellerConfig();
        $message = '';

        if ($casteller->getDisplayName()) {

            $message .= ''.$casteller->getDisplayName().' ';
        }

        if ($casteller_config->getTelegramEnabled() && $casteller_config->getTelegramToken()) {

            $message .= $lb.__('botman.conversation_display_member_telegram_token').' ';
            $message .= BotmanHelper::setBold($driver, $casteller_config->getTelegramToken());

        } else {
            $message .= $lb.__('botman.conversation_display_telegram_not_available_for_member').' ';
        }

        if ($casteller_config->getAuthTokenEnabled()) {

            $url = $casteller_config->getWebUrl();
            $message .= $lb.__('botman.conversation_display_url_member_click');
            $message .= $lb.BotmanHelper::setLink($driver,
                $url,
                $url);
        } else {
            $message .= $lb.__('botman.conversation_display_web_not_available_for_member').' ';
        }

        $message .= $lb;

        return $message;
    }

    public static function getRondesFormatted(Event $event, Collection $rondes, DriverInterface $driver, BotmanEmojisHelper $botmanEmojisHelper): array
    {

        $lb = BotmanHelper::getLbTag($driver);

        $return = [
            'message' => '',
            'options' => [],
            'rows' => [],
        ];

        $msgDate = '';
        $optionEvent = '';

        if (! empty($event->getStartDate())) {
            setlocale(LC_ALL, 'ca_ES');
            $start_date = Carbon::parse($event->getStartDate());
            $msgDate = $start_date->isoFormat('DD/MM HH:mm\h');
        }

        if (! empty($event->getStartDate())) {
            if ($event->isOpen()) {
                $optionEvent = $botmanEmojisHelper->getEmojiEventType($event::TYPES[$event->getType()]).' '.$msgDate;
            } else {
                $optionEvent = $botmanEmojisHelper->getEmoji('locked').' '.$msgDate;
            }
        }

        $messageEvent = $optionEvent.' '.$event->getName();
        $messageEvent .= $lb.$lb;
        $return['message'] .= $messageEvent;

        $eventsInRow = [];

        if ($rondes->isEmpty()) {
            $return['message'] .= __('botman.rondes_empty');
        } else {
            foreach ($rondes as $index => $ronda) {

                $messageRonda = ucwords(__('rondes.ronda')).' '.$ronda->getRonda().': '.$ronda->getBoardEvent()->getDisplayName().$lb;

                $return['rows'][] = $ronda->getBoardEvent()->getDisplayName();
                $return['message'] .= $messageRonda;
                $return['options'][$index] = ltrim($messageRonda);
            }
        }

        $return['message'] .= $lb;

        return $return;

    }

    public static function getRondesFormattedWithLink(Casteller $casteller, Event $event, Collection $rondes, DriverInterface $driver, BotmanEmojisHelper $botmanEmojisHelper): array
    {

        $lb = BotmanHelper::getLbTag($driver);

        $return = [
            'message' => '',
            'options' => [],
            'rows' => [],
        ];

        $msgDate = '';
        $optionEvent = '';

        if (! empty($event->getStartDate())) {
            setlocale(LC_ALL, 'ca_ES');
            $start_date = Carbon::parse($event->getStartDate());
            $msgDate = $start_date->isoFormat('DD/MM HH:mm\h');
        }

        if (! empty($event->getStartDate())) {
            if ($event->isOpen()) {
                $optionEvent = $botmanEmojisHelper->getEmojiEventType($event::TYPES[$event->getType()]).' '.$msgDate;
            } else {
                $optionEvent = $botmanEmojisHelper->getEmoji('locked').' '.$msgDate;
            }
        }

        $messageEvent = $optionEvent.' '.$event->getName();
        $messageEvent .= $lb.$lb;
        $return['message'] .= $messageEvent;
        $castellerId = $casteller->getId();

        $eventsInRow = [];

        if ($rondes->isEmpty()) {
            $return['message'] .= __('botman.rondes_empty');
        } else {

            $rondesSorted = $rondes->sortBy('ronda');

            foreach ($rondesSorted as $index => $ronda) {

                $link = $ronda->getBoardEvent()->getPublicUrl($castellerId);
                $messageLink = self::setLink($driver, ucwords(__('rondes.ronda')).' '.$ronda->getRonda(), $link);
                $messageRonda = $messageLink.': '.$ronda->getBoardEvent()->getDisplayName().$lb;

                $return['rows'][] = $ronda->getBoardEvent()->getDisplayName();
                $return['message'] .= $messageRonda;
                $return['options'][$index] = ltrim($messageRonda);
            }
        }

        $return['message'] .= $lb;

        return $return;

    }

    public static function getHelpFormatted(int $rol, DriverInterface $driver, BotmanEmojisHelper $botmanEmojisHelper): string
    {
        $lb = BotmanHelper::getLbTag($driver);

        // All castellers

        $message = $botmanEmojisHelper->getEmoji('mainMenuHome').' '.__('botman.general_menu_home').$lb;

        $message .= $lb.$botmanEmojisHelper->getEmoji('mainMenuEvents').' '.__('botman.general_menu_events').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('eventsActuacio').' '.__('botman.help_actuacions').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('eventsAssaig').' '.__('botman.help_assajos').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('eventsActivitat').' '.__('botman.help_activitats').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('warning').' '.__('botman.help_unanswered').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('eventsAttendanceOk').' '.__('botman.help_events_attendance_yes').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('eventsAttendanceNok').' '.__('botman.help_events_attendance_no').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('eventsAttendanceUnknown').' '.__('botman.help_events_attendance_Unknown').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('eventsAttendanceAllOk').' '.__('botman.help_events_attendance_AllYes').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('eventsAttendanceAllNok').' '.__('botman.help_events_attendance_AllNo').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('eventsAttendanceAllUnknown').' '.__('botman.help_events_attendance_AllUnknown').$lb;

        $message .= $lb.$botmanEmojisHelper->getEmoji('mainMenuOptions').' '.__('botman.general_menu_options').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('optionsLinkingCastellers').' '.__('botman.general_menu_linked_castellers').$lb;
        // $message .= '    '.$botmanEmojisHelper->getEmoji('optionsLanguage').' '.__('botman.general_menu_languages').$lb;

        $message .= '        '.$botmanEmojisHelper->getEmoji('optionsSwitchCasteller').' '.__('botman.help_select_castellers').$lb;
        $message .= '        '.$botmanEmojisHelper->getEmoji('optionsLinkCasteller').' '.__('botman.help_link_casteller').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('optionsSwitchTextEmojis').' '.__('botman.help_text_emoji').$lb;
        $message .= '    '.$botmanEmojisHelper->getEmoji('optionsLogOut').' '.__('botman.help_logout').$lb;

        // $message .= ''.$botmanEmojisHelper->getEmoji('optionsLanguage').' '.__('botman.general_menu_languages').$lb;

        $message .= $lb.$botmanEmojisHelper->getEmoji('mainMenuHelp').' '.__('botman.general_menu_help').$lb;

        // only tecnica

        if ($rol === 1) {
            $message .= ''.$lb.$lb;
            $message .= ''.$botmanEmojisHelper->getEmoji('mainMenuTecnica').' '.__('botman.general_menu_tecnica').$lb;
            $message .= '    '.$botmanEmojisHelper->getEmoji('tecnicaSearch').' '.__('botman.help_tecnica_search').$lb;
            $message .= '    '.$botmanEmojisHelper->getEmoji('tecnicaAttendance').' '.__('botman.help_tecnica_attendance').$lb;
            $message .= '    '.$botmanEmojisHelper->getEmoji('tecnicaReminders').' '.__('botman.help_tecnica_reminders').$lb;
            $message .= '        '.$botmanEmojisHelper->getEmoji('tecnicaSendRemindersEvents').' '.__('botman.help_tecnica_events_reminders').$lb;
            $message .= '            '.$botmanEmojisHelper->getEmoji('tecnicaSendRemindersExpres').' '.__('botman.help_tecnica_send_reminders_expres').$lb;
            $message .= '            '.$botmanEmojisHelper->getEmoji('tecnicaSendRemindersTags').' '.__('botman.help_tecnica_send_reminders_tags').$lb;
            $message .= '        '.$botmanEmojisHelper->getEmoji('Email').' '.__('botman.help_tecnica_general_reminders').$lb;

        }

        return $message;
    }

    public static function getCustomAnswersFormatted(array $answersOptions, array $customAnswersCasteller, BotmanEmojisHelper $botmanEmojisHelper): array
    {
        $i = 1;
        $answersOptionsFormated = [];

        foreach ($answersOptions as $ao) {
            if (in_array($ao, $customAnswersCasteller)) {
                $answersOptionsFormated[] = $botmanEmojisHelper->getEmoji('check').' '.$ao;
                $numAnswer[] = $botmanEmojisHelper->getEmoji('check').' '.$i;
            } else {
                $answersOptionsFormated[] = $ao;
                $numAnswer[] = strval($i);
            }

            $i += 1;
        }

        return [$answersOptionsFormated, $numAnswer];

    }

    public static function getConcatenatedKeyValueArrayList(array $list, DriverInterface $driver): string
    {
        $lb = BotmanHelper::getLbTag($driver);

        $listFormated = implode($lb, array_map(
            function ($v, $k) {
                return sprintf('%s -> %s', $k, $v);
            },
            $list,
            range(1, count($list))
        ));

        return $listFormated;
    }

    public static function getOptionNames(Attendance $attendance, DriverInterface $driver, string $separator = ''): string
    {

        $lb = BotmanHelper::getLbTag($driver);

        if ($separator === '') {
            return implode($lb, $attendance->getOptionsNames());
        } else {
            return implode($separator, $attendance->getOptionsNames());
        }

    }

    public static function splitMenuByRows(array $menuArray, int $maxItemsByRow): array
    {
        $numRows = intdiv(count($menuArray), $maxItemsByRow) + 1;

        foreach (range(1, $numRows) as $row) {
            $rows[] = array_slice($menuArray, ($row - 1) * $maxItemsByRow, $maxItemsByRow);
        }

        return $rows;
    }
}
