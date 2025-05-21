<?php

namespace App\Helpers;

use App\Attendance;
use Spatie\Emoji\Emoji;

class BotmanEmojisHelper
{
    protected array $emojis;

    public function __construct()
    {

        $this->emojis = [

            'mainMenuHome' => Emoji::house(),
            'mainMenuEvents' => Emoji::calendar(),
            'mainMenuOptions' => Emoji::gear(),
            'mainMenuTecnica' => Emoji::hammer(),
            'mainMenuHelp' => Emoji::redQuestionMark(),
            'mainMenuPinyes' => Emoji::pineapple(),

            'optionsLinkingCastellers' => Emoji::bustsInSilhouette(),
            'optionsLanguage' => Emoji::flagsForFlagEuropeanUnion(),
            'optionsLinkCasteller' => Emoji::link(),
            'optionsSwitchCasteller' => Emoji::shuffleTracksButton(),
            'optionsSwitchTextEmojis' => Emoji::inputLatinLetters(),
            'optionsLogOut' => Emoji::mobilePhoneOff(),
            'optionsURLMember' => Emoji::globeWithMeridians(),

            'eventsActuacio' => Emoji::personRaisingHand(),
            'eventsAssaig' => Emoji::personLiftingWeights(),
            'eventsActivitat' => Emoji::shallowPanOfFood(),
            'eventsAttendanceOk' => Emoji::greenCircle(),
            'eventsAttendanceNok' => Emoji::redCircle(),
            'eventsAttendanceUnknown' => Emoji::yellowCircle(),
            'eventsAttendanceAllOk' => Emoji::greenHeart(),
            'eventsAttendanceAllNok' => Emoji::redHeart(),
            'eventsAttendanceAllUnknown' => Emoji::yellowHeart(),
            'eventsNoAttendance' => Emoji::whiteQuestionMark(),
            'eventsCompanions' => Emoji::bustsInSilhouette(),
            'eventsDate' => Emoji::tearOffCalendar(),
            'eventsRightArrow' => Emoji::rightArrow(),
            'eventsLocation' => Emoji::roundPushpin(),
            'eventsInfo' => Emoji::information(),
            'eventsAnswers' => Emoji::stopwatch(),
            //'eventsTop' => Emoji::top_arrow(),
            'eventsTop' => Emoji::back_arrow(),
            'eventsVerifyAttendance' => Emoji::checkMarkButton(),

            'tecnicaAttendance' => Emoji::barChart(),
            'tecnicaReminders' => Emoji::outboxTray(),
            'tecnicaSearch' => Emoji::magnifyingGlassTiltedLeft(),
            'tecnicaSendRemindersExpres' => Emoji::speechBalloon(),
            'tecnicaSendRemindersOnline' => Emoji::thoughtBalloon(),
            'tecnicaSendRemindersTags' => Emoji::rightAngerBubble(),
            'tecnicaSendRemindersEvents' => Emoji::spiralCalendar(),

            'SendReminderEmail' => Emoji::eMail(),

            'Telegram' => Emoji::mobilePhoneWithArrow(),
            'Email' => Emoji::envelope(),
            'hourglass' => Emoji::hourglassDone(),
            'warning' => Emoji::warning(),
            'speaking' => Emoji::speaking_head(),
            'person' => Emoji::person(),

            'locked' => Emoji::locked(),
            'check' => Emoji::check_mark(),

            'pinyesPinya' => Emoji::wheel_of_dharma(),
            'pinyesRondes' => Emoji::registered(),
        ];
    }

    /**
     * Returns Emoji from key
     */
    public function getEmoji(string $key): string
    {
        if (array_key_exists($key, $this->emojis)) {
            return $this->emojis[$key];
        } else {
            return '';
        }
    }

    /**
     * Returns array of Emojis from array of keys
     */
    public function getEmojis(array $keys): array
    {
        $emojis = [];

        if (! empty($keys)) {
            foreach ($keys as $key) {
                $emoji = $this->getEmoji($key);
                if ($emoji !== '') {
                    $emojis[] = $emoji;
                }
            }
        }

        return $emojis;
    }

    /**
     * Returns Key from Emoji
     */
    public function getKey(string $emoji): string
    {
        $key = array_search($emoji, $this->emojis);
        if ($key !== false) {
            return $key;
        } else {
            return '';
        }

    }

    /**
     * Returns Emoji from Attendance
     */
    public function getEmojiAttendance(?Attendance $attendance): string
    {
        if (is_null($attendance)) {
            return $this->getEmoji('eventsNoAttendance');
        } else {
            $key = 'eventsAttendance'.ucwords(strtolower($attendance->getStatusName()));

            return $this->getEmoji($key);
        }
    }

    /**
     * Returns Emoji from EventType
     */
    public function getEmojiEventType(string $eventType): string
    {
        $key = 'events'.ucwords(strtolower($eventType));

        return $this->getEmoji($key);
    }

    /**
     * Returns The Status of the AllAttendance depending on the Emoji used
     */
    public function getStatusFromEmojiAllAttendance(string $emoji): string
    {
        $key = $this->getKey($emoji);
        if ($key === '') {
            return $key;
        } else {
            return strtolower(str_replace('eventsAttendanceAll', '', $key));
        }
    }
}
