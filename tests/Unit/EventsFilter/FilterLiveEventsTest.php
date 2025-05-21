<?php

namespace Tests\Unit\EventsFilter;

use App\Colla;
use App\Event;
use App\Services\Filters\EventsFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterLiveEventsTest extends TestCase
{
    use RefreshDatabase;

    public function testFilterLiveEvents()
    {
        $colla = Colla::factory()->create();
        $set_colla = [
            'colla_id' => $colla->getId(),
        ];

        Event::factory()->state($set_colla)->future()->count(1)->create();
        Event::factory()->state($set_colla)->live()->count(1)->create();

        $eventsResponse = (new EventsFilter($colla))->liveOrUpcoming()->eloquentBuilder()->count();
        $this->assertEquals(2, $eventsResponse);
    }
}
