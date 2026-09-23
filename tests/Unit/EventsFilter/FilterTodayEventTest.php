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
        Event::factory()->for($colla)->today()->count(2)->create();
        Event::factory()->for($colla)->past()->count(1)->create();
        $eventsResponse = (new EventsFilter($colla))->today()->eloquentBuilder()->count();
        $this->assertEquals(2, $eventsResponse);
    }
}
