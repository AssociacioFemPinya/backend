<?php

namespace App\Helpers;

use App\Attendance;
use App\Casteller;
use App\Enums\AttendanceStatus;
use App\Enums\Gender;
use App\Event;
use Carbon\Carbon;

class Humans
{
    public static function readEventColumn($event, $column, $align = 'right')
    {
        if ($column == 'tags') {
            $tags = null;

            foreach ($event->tagsArray('name') as $tag) {
                $tags .= '<span class="badge badge-primary pull-'.$align.'" style="margin-left: 3px; margin-bottom: 3px;">'.$tag.'</span>';
            }

            return $tags;
        } elseif ($column == 'casteller_tags') {

            $tags = null;

            foreach ($event->castellerTagsArray('name') as $tag) {
                $tags .= '<span class="badge badge-primary pull-'.$align.'" style="margin-left: 3px; margin-bottom: 3px;">'.$tag.'</span>';
            }

            return $tags;
        } elseif ($column == 'start_date') {
            setlocale(LC_ALL, 'ca_ES');
            if (empty($event->start_date)) {
                return $event->start_date;
            }
            $start_date = Carbon::parse($event->start_date);

            return self::parseDate($start_date);
        } else {
            return $event->{$column};
        }
    }

    /** Read for humans fields date, age...*/
    public static function readCastellerColumn(Casteller $casteller, string $column, string $align = 'right'): ?string
    {
        if ($column == 'age') {
            $age = $casteller->age();

            if (! $casteller->getBirthdate()) {
                return '';
            }

            if ($age > 0) {
                return $age;
            } else {
                return '0';
            }
        } elseif ($column === 'birthdate') {
            return ($casteller->getBirthdate()) ? $casteller->getBirthdate()->format('d/m/Y') : null;
        } elseif ($column === 'subscription_date') {
            return ($casteller->getSubscriptionDate()) ? $casteller->getSubscriptionDate()->format('d/m/Y') : null;
        } elseif ($column === 'gender') {
            switch ($casteller->getGender()) {
                case Gender::Male()->value():
                    return '<span class="fa-solid fa-mars" style="font-size: 20px;"></span>';
                    break;
                case Gender::Female()->value():
                    return '<span class="fa-solid fa-venus" style="font-size: 20px;"></span>';
                    break;
                case Gender::Noanswer()->value():
                case null:
                    return '<span class="fa-solid fa-genderless" style="font-size: 20px;"></span>';
                    break;
                case Gender::Nobinary()->value():
                    return '<span class="fa-solid fa-mars-and-venus" style="font-size: 20px;"></span>';
                    break;
            }
        } elseif ($column === 'tags') {
            $tags = '';

            foreach ($casteller->getTags() as $tag) { //filterd tag -- paint it blue https://open.spotify.com/track/63T7DJ1AFDD6Bn8VzG6JE8?si=QfAzbKC3Sl2sNxmBj12crA
                $tags .= '<span class="badge badge-primary pull-'.$align.'" style="margin-left: 3px; margin-bottom: 3px;">'.$tag->getName().'</span>';
            }

            return $tags;
        } else {
            return $casteller->{$column};
        }
    }

    /** Read for humans status of an Attendance ..*/
    public static function readAttendanceStatus(Casteller|Event $casteller, ?Attendance $attendance): ?string
    {
        if (is_null($attendance) || is_null($attendance->getStatus())) {
            $status = RenderHelper::fieldbutton('data-id_casteller', (string) $casteller->getId(), 'btn btn-secondary btn-status', RenderHelper::getAttendanceIcon(null, false));
        } else {
            switch ($attendance->getStatus()) {
                case 1:
                    $status = RenderHelper::fieldbutton('data-id_casteller', (string) $casteller->getId(), 'btn btn-success btn-status', RenderHelper::getAttendanceIcon($attendance->getStatus(), false));
                    break;
                case 2:
                    $status = RenderHelper::fieldbutton('data-id_casteller', (string) $casteller->getId(), 'btn btn-danger btn-status', RenderHelper::getAttendanceIcon($attendance->getStatus(), false));
                    break;
                case 3:
                    $status = RenderHelper::fieldbutton('data-id_casteller', (string) $casteller->getId(), 'btn btn-outline-warning btn-status', RenderHelper::getAttendanceIcon($attendance->getStatus(), false));
                    break;
                default:
                    $status = RenderHelper::fieldbutton('data-id_casteller', (string) $casteller->getId(), 'btn btn-secondary btn-status', RenderHelper::getAttendanceIcon(AttendanceStatus::UNKNOWN), false);
                    break;
            }
        }

        return $status;
    }

    /** Read for humans status of an Attendance ..*/
    public static function readEventAttendanceStatus(Event $event, ?Attendance $attendance): ?string
    {
        $status = null;

        if (is_null($attendance) || is_null($attendance->getStatus())) {
            $btn_type = $event->isOpen() ? 'btn btn-secondary btn-status' : 'btn btn-tertiary btn-status';
            $status = RenderHelper::fieldbutton('data-id_event', (string) $event->getId(), $btn_type, RenderHelper::getAttendanceIcon(null, false), null, null, ! $event->isOpen());
        } else {
            switch (AttendanceStatus::getById($attendance->getStatus())) {
                case 'YES':
                    $btn_type = $event->isOpen() ? 'btn btn-success btn-status' : 'btn btn-tertiary btn-status';
                    $status = RenderHelper::fieldbutton('data-id_event', (string) $event->getId(), $btn_type, RenderHelper::getAttendanceIcon($attendance->getStatus(), false), null, null, ! $event->isOpen());
                    break;
                case 'NO':
                    $btn_type = $event->isOpen() ? 'btn btn-danger btn-status' : 'btn btn-tertiary btn-status';
                    $status = RenderHelper::fieldbutton('data-id_event', (string) $event->getId(), $btn_type, RenderHelper::getAttendanceIcon($attendance->getStatus(), false), null, null, ! $event->isOpen());
                    break;
                case 'UNKNOWN':
                    $btn_type = $event->isOpen() ? 'btn btn-warning btn-status' : 'btn btn-tertiary btn-status';
                    $status = RenderHelper::fieldbutton('data-id_event', (string) $event->getId(), $btn_type, RenderHelper::getAttendanceIcon($attendance->getStatus(), false), null, null, ! $event->isOpen());
                    break;
                default:
                    $btn_type = $event->isOpen() ? 'btn btn-secondary btn-status' : 'btn btn-tertiary btn-status';
                    $status = RenderHelper::fieldbutton('data-id_event', (string) $event->getId(), $btn_type, RenderHelper::getAttendanceIcon($attendance->getStatus(), false), null, null, ! $event->isOpen());
                    break;
            }
        }

        return $status;
    }

    /** Read for humans status verified of an Attendance ..*/
    public static function readAttendanceStatusVerified(Casteller|Event $casteller, ?Attendance $attendance): ?string
    {
        if (is_null($attendance) || is_null($attendance->getStatusVerified())) {
            $status_verified = RenderHelper::fieldbutton('data-id_casteller', (string) $casteller->getId(), 'btn btn-secondary btn-status-verified', RenderHelper::getAttendanceIcon(null, false));
        } else {
            switch ($attendance->getStatusVerified()) {
                case 1:
                    $status_verified = RenderHelper::fieldbutton('data-id_casteller', (string) $casteller->getId(), 'btn btn-success btn-status-verified', RenderHelper::getAttendanceIcon($attendance->getStatusVerified(), false));
                    break;
                case 2:
                    $status_verified = RenderHelper::fieldbutton('data-id_casteller', (string) $casteller->getId(), 'btn btn-danger btn-status-verified', RenderHelper::getAttendanceIcon($attendance->getStatusVerified(), false));
                    break;
                default:
                    $status_verified = RenderHelper::fieldbutton('data-id_casteller', (string) $casteller->getId(), 'btn btn-secondary btn-status-verified', RenderHelper::getAttendanceIcon($attendance->getStatusVerified(), false));
                    break;
            }
        }

        return $status_verified;
    }

    public static function readAttendanceAnswersText(Casteller|Event $casteller, ?Attendance $attendance, ?Event $event = null): ?string
    {
        $attendanceOptions = is_null($attendance) || is_null($attendance->getOptions()) ? [] : $attendance->getOptions();
        if (empty($attendanceOptions)) {
            return '';
        }
        if (! $event && $attendance) {
            $event = $attendance->getEvent();
        }

        if (is_array($attendanceOptions) && array_keys($attendanceOptions) !== range(0, count($attendanceOptions) - 1)) {
            $schemaDict = [];
            if ($event && $event->form_schema) {
                foreach ($event->form_schema as $field) {
                    if (isset($field['name']) && isset($field['label'])) {
                        $schemaDict[$field['name']] = $field['label'];
                    }
                }
            }

            $parsedOptions = [];
            foreach ($attendanceOptions as $key => $val) {
                if (is_array($val)) {
                    $val = implode(', ', $val);
                }
                $label = $schemaDict[$key] ?? $key;
                $parsedOptions[] = $label.': '.$val;
            }

            return implode("\n", $parsedOptions);
        }

        return '';
    }

    /** Read for humans answers of an Attendance ..*/
    public static function readAttendanceAnswers(Casteller|Event $casteller, ?Attendance $attendance, ?Event $event = null): ?string
    {
        $attendanceOptions = is_null($attendance) || is_null($attendance->getOptions()) ? [] : $attendance->getOptions();

        if (empty($attendanceOptions)) {
            return '';
        }

        if (! $event && $attendance) {
            $event = $attendance->getEvent();
        }

        // Nuevo formato basado en JSON (FormBuilder)
        $text = '';
        if (is_array($attendanceOptions) && array_keys($attendanceOptions) !== range(0, count($attendanceOptions) - 1)) {
            $schemaDict = [];
            if ($event && $event->form_schema) {
                foreach ($event->form_schema as $field) {
                    if (isset($field['name']) && isset($field['label'])) {
                        $schemaDict[$field['name']] = $field['label'];
                    }
                }
            }

            $parsedOptions = [];
            foreach ($attendanceOptions as $key => $val) {
                if (is_array($val)) {
                    $val = implode(', ', $val);
                }
                $label = $schemaDict[$key] ?? $key;
                $parsedOptions[] = '<b>'.e($label).'</b>: '.e($val);
            }
            $text = implode('<br>', $parsedOptions);
        }

        if (strlen(strip_tags($text)) > 50) {
            $shortText = substr(strip_tags($text), 0, 50).'...';
            $fullTextHtml = htmlentities($text, ENT_QUOTES, 'UTF-8');

            return $shortText.' <a href="#" onclick="event.preventDefault(); showFullTextModal(\'Respostes\', \''.$fullTextHtml.'\');">Veure més</a>';
        }

        return $text;
    }

    /** Read for humans selectable answers of an Attendance ..*/
    public static function readSelectableAttendanceAnswers(Event $event, ?Attendance $attendance): string
    {
        if ($event->form_schema) {
            $butonBtn = $event->isOpen() && $attendance && $attendance->getStatus() == AttendanceStatus::YES ? 'btn-primary' : 'btn-secondary disabled';
            if ($attendance && $attendance->getStatus() == AttendanceStatus::YES) {
                $formUrl = route('member.event.form', $event->getId());

                return '<a href="'.$formUrl.'" class="btn btn-sm '.$butonBtn.' pull-right"><i class="fa fa-list-alt"></i> '.trans('general.form').'</a>';
            }
        }

        return '';
    }

    /** Read for humans companions of an Attendance ..*/
    public static function readAttendanceCompanions(Casteller|Event $casteller, ?Attendance $attendance, Event $event): ?string
    {
        if (is_null($attendance)) {
            $companions = RenderHelper::fieldInput('0', 'number', false, true, 'data-id_casteller', (string) $casteller->getId(), 'companions', 'companions', 'companions', 'data-event_id', (string) $event->getId(), $min = 0, $max = 6);
        } elseif (! is_null($attendance->getCompanions())) {
            $companions = RenderHelper::fieldInput($attendance->getCompanions(), 'number', false, (! $event->getCompanions() || is_null($attendance->getStatus()) || $attendance->getStatus() != 1), 'data-id_casteller', (string) $casteller->getId(), 'companions', 'companions', 'companions', 'data-event_id', (string) $event->getId(), $min = 0, $max = 6);
        } else {
            $companions = RenderHelper::fieldInput(0, 'number', false, (! $event->getCompanions() || is_null($attendance->getStatus()) || $attendance->getStatus() != 1), 'data-id_casteller', (string) $casteller->getId(), 'companions', 'companions', 'companions', 'data-event_id', (string) $event->getId(), $min = 0, $max = 6);
        }

        return $companions;
    }

    /** Read for humans Last Update of an Attendance ..*/
    public static function readAttendanceLastUpdate(?Attendance $attendance): ?string
    {
        if (is_null($attendance) || is_null($attendance->getUpdatedAt()) || empty($attendance->getUpdatedAt())) {
            $last_update = '';
        } else {
            $last_update = $attendance->getUpdatedAtParsed(true);
        }

        return $last_update;
    }

    /** Read for humans the time that has passed... */
    public static function howLong(?Carbon $date = null, bool $credentials = false): string
    {
        if (empty($date)) {
            if ($credentials) {
                return ''.trans('casteller.not_credentials_sent');
            } else {
                return ''.trans('casteller.not_logging');
            }
        }

        return Carbon::parse($date)->diffForHumans();

    }

    /** Replace special characters from a string */
    public static function replaceSpecialCharacters(string $string): ?string
    {
        $unwanted_array = ['Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y'];

        $string = strtr($string, $unwanted_array);

        return preg_replace('/[^a-zA-Z0-9 ]+/', '', $string);
    }

    /** Format date
     *  The variable must be in date format obtained from a field in the DB
     *  If the variable belongs to the field's attribute, the result will be incorrect
     */
    public static function parseDate(?Carbon $date = null, bool $shortDate = false): ?string
    {
        if (empty($date)) {
            return null;
        } else {
            setlocale(LC_ALL, 'ca_ES');
            $dateParsed = Carbon::parse($date);
            if ($shortDate) {
                return $dateParsed->format('d/m/Y H:i');
            } else {
                return $dateParsed->isoFormat('dddd, D MMMM OY, HH:mm');
            }
        }
    }
}
