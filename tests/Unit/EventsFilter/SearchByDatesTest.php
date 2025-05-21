<?php

namespace Tests\Unit\EventsFilter;

use App\Colla;
use App\Event;
use App\Services\Filters\EventsFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchByDatesTest extends TestCase
{
    use RefreshDatabase;

    public function testSearchByDates()
    {
        $colla = Colla::factory()->create();
        $event_start_dates = [
            '2022-2-28 23:59:59', '2022-5-1 00:00:00', '2022-6-1 00:00:00', '2022-8-1 00:00:00',
            '2023-2-28 23:59:59', '2023-5-1 00:00:00', '2023-6-1 00:00:00', '2023-8-1 00:00:00',
        ];
        $events = [];
        foreach ($event_start_dates as $event_start_date) {
            $event = Event::factory()->state([
                'colla_id' => $colla->getId(),
                'start_date' => date($event_start_date),
            ])->create();
            array_push($events, $event);
        }

        // After date should not return events before that date
        $eventsResponse = (new EventsFilter($colla))->afterDate('2022-4-1 00:00:00')->eloquentBuilder()->get()->toArray();
        $this->assertEquals(7, count($eventsResponse));
        $this->assertNotContains($events[0]['id_event'], array_column($eventsResponse, 'id_event'));

        // Before date should not return events after that date
        $eventsResponse = (new EventsFilter($colla))->beforeDate('2023-7-1 00:00:00')->eloquentBuilder()->get()->toArray();
        $this->assertEquals(7, count($eventsResponse));
        $this->assertNotContains($events[7]['id_event'], array_column($eventsResponse, 'id_event'));

        // Before date should not return events after that date
        $eventsResponse = (new EventsFilter($colla))->afterDate('2022-4-1 00:00:00')->beforeDate('2023-7-1 00:00:00')->eloquentBuilder()->get()->toArray();
        $this->assertEquals(6, count($eventsResponse));
        $this->assertNotContains($events[0]['id_event'], array_column($eventsResponse, 'id_event'));
        $this->assertNotContains($events[7]['id_event'], array_column($eventsResponse, 'id_event'));
    }
}
