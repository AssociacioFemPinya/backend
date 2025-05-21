<?php

namespace Tests\Unit;

use App\Attendance;
use App\Casteller;
use App\Colla;
use App\Enums\AttendanceStatus;
use App\Event;
use App\Services\AttendanceStats;
use App\Services\Filters\CastellersFilter;
use App\Services\Filters\EventsFilter;
use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceStatsTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAttendancePercentage()
    {
        $colla = Colla::factory()->create();
        $set_colla = [
            'colla_id' => $colla->getId(),
        ];

        $events = Event::factory()->state($set_colla)->count(5)->create();
        $castellers = Casteller::factory()->state($set_colla)->count(5)->create();

        // This tags will be used to test that we events and castellers filters are used correctly
        // This means that casteller[4] should not appear in the result list
        // and event[4] should not be accounted for the percentages
        $tagEvent = Tag::factory()->state($set_colla)->event()->create();
        $tagCasteller = Tag::factory()->state($set_colla)->casteller()->create();
        $events[4]->tags()->attach($tagEvent->getId());
        $castellers[4]->tags()->attach($tagCasteller->getId());
        $eventsFilter = (new EventsFilter($colla))->withoutTags([$tagEvent->getId()]);
        $castellerFilter = (new CastellersFilter($colla))->withoutTags([$tagCasteller->getId()]);

        $attendanceConfirmer = function (Casteller $casteller, array $events) {
            foreach ($events as $event) {
                Attendance::factory()->create(['status' => AttendanceStatus::YES, 'event_id' => $event->getId(), 'casteller_id' => $casteller->getId()]);
            }
        };

        // castellers[0] should have attendance=0, as it doesn't have any attendance
        // castellers[1] should have attendance=1/4 (25%)
        $attendanceConfirmer($castellers[1], [$events[0]]);
        // castellers[2] should have attendance=2/4 (50%)
        $attendanceConfirmer($castellers[2], [$events[0], $events[1]]);
        // castellers[3] should have attendance=4/4 (100%)
        $attendanceConfirmer($castellers[3], [$events[0], $events[1], $events[2], $events[3]]);

        $attendanceStats = new AttendanceStats($eventsFilter, $castellerFilter);

        $this->assertEquals([
            ['casteller_id' => $castellers[0]->getId(), 'percentage' => '0.0000'],
            ['casteller_id' => $castellers[1]->getId(), 'percentage' => '0.2500'],
            ['casteller_id' => $castellers[2]->getId(), 'percentage' => '0.5000'],
            ['casteller_id' => $castellers[3]->getId(), 'percentage' => '1.0000'],
        ], $attendanceStats->getAttendancePercentage()->get()->toArray());
    }
}
