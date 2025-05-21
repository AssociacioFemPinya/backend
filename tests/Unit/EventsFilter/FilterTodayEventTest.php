<?php

namespace Tests\Unit\EventsFilter;

use App\Colla;
use App\Event;
use App\Services\Filters\EventsFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterTodayEventTest extends TestCase
{
    use RefreshDatabase;

    public function testFilterTodayEvent()
    {
        $colla = Colla::factory()->create();
        $set_colla = [
            'colla_id' => $colla->getId(),
        ];

        Event::factory()->state($set_colla)->today()->count(2)->create();
        $eventsResponse = (new EventsFilter($colla))->upcoming()->eloquentBuilder()->count();
        $this->assertEquals(2, $eventsResponse);
    }
}
