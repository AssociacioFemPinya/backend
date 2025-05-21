<?php

namespace App\Services;

use App\Attendance;
use App\Casteller;
use App\Colla;
use App\Enums\TypeTags;
use App\Managers\AttendanceManager;
use App\Managers\CastellersManager;
use App\Managers\CollesManager;
use App\Managers\EventsManager;
use App\Managers\TagsManager;
use App\Tag;
use Symfony\Component\HttpFoundation\ParameterBag;

class ImportService
{
    private CollesManager $collesManager;

    private CastellersManager $castellersManager;

    private TagsManager $tagsManager;

    private EventsManager $eventsManager;

    private AttendanceManager $attendanceManager;

    public function __construct(CollesManager $collesManager, CastellersManager $castellersManager, TagsManager $tagsManager, EventsManager $eventsManager, AttendanceManager $attendanceManager)
    {
        $this->collesManager = $collesManager;
        $this->castellersManager = $castellersManager;
        $this->tagsManager = $tagsManager;
        $this->eventsManager = $eventsManager;
        $this->attendanceManager = $attendanceManager;

    }

    public function importColla(ParameterBag $infoColla)
    {

        $colla = Colla::getCollaByIdExternal($infoColla->get('id_colla_external'));
        if ($colla) {
            $colla = $this->collesManager->updateColla($colla, $infoColla);
        } else {
            $colla = $this->collesManager->createColla($infoColla);
        }

        return $colla;

    }

    public function importCastellers(array $infoCastellers, Colla $colla)
    {

        $existingCastellers = $colla->getCastellers();

        foreach ($infoCastellers as $infoCasteller) {

            $casteller = $existingCastellers->where('id_casteller_external', $infoCasteller->get('id_casteller_external'))->first();

            if ($casteller) {
                $casteller = $this->castellersManager->updateCasteller($casteller, $infoCasteller);
            } else {
                $casteller = $this->castellersManager->createCasteller($colla, $infoCasteller);
            }

        }

    }

    public function importPositions(array $infoPositions, Colla $colla)
    {

        $existingTags = Tag::currentTags(TypeTags::POSITIONS, $colla);

        foreach ($infoPositions as $infoPosition) {

            $tag = $existingTags->where('id_tag_external', $infoPosition->get('id_tag_external'))->where('type', TypeTags::POSITIONS)->first();

            if ($tag) {
                $tag = $this->tagsManager->updateTag($tag, $infoPosition);
            } else {
                $tag = $this->tagsManager->createTag($colla, $infoPosition);
            }

        }

    }

    public function importEvents(array $infoEvents, Colla $colla)
    {

        $existingEvents = $colla->getEvents();

        foreach ($infoEvents as $infoEvent) {

            $event = $existingEvents->where('id_event_external', $infoEvent->get('id_event_external'))->first();

            if ($event) {
                $event = $this->eventsManager->updateEvent($event, $infoEvent);
            } else {
                $event = $this->eventsManager->createEvent($colla, $infoEvent);
            }

        }

    }

    public function importAttendances(array $infoAttendances, Colla $colla)
    {

        $existingAttendance = Attendance::getAttendanceColla($colla);

        foreach ($infoAttendances as $infoAttendance) {

            if (! is_null($infoAttendance)) {

                $attendance = $existingAttendance->where('id_attendance_external', $infoAttendance->get('id_attendance_external'))->first();

                if ($attendance) {
                    $attendance = $this->attendanceManager->updateAttendance($attendance, $infoAttendance);
                } else {

                    // We look for attendances for the same casteller on the same event having different ID (so not linked)
                    // This is because on the old version each change on the attendances creates a new object
                    $duplicate = $existingAttendance
                        ->where('casteller_id', $infoAttendance->get('casteller_id'))
                        ->where('event_id', $infoAttendance->get('event_id'))
                        ->first();

                    if (! $duplicate) {
                        $attendance = $this->attendanceManager->createAttendance($infoAttendance->get('casteller_id'), $infoAttendance->get('event_id'), $infoAttendance);
                    }
                }
            }

        }

    }
}
