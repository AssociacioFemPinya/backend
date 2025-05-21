<?php

namespace Tests\Unit\EventsFilter;

use App\Colla;
use App\Enums\FilterSearchTypesEnum;
use App\Event;
use App\Services\Filters\EventsFilter;
use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPastEventsByTagsTest extends TestCase
{
    use RefreshDatabase;

    public function testGetPastEventsByTags()
    {
        $colla = Colla::factory()->create();
        $set_colla = [
            'colla_id' => $colla->getId(),
        ];

        $tags = Tag::factory()->state($set_colla)->event()->count(3)->create();
        $events = Event::factory()->state($set_colla)->past()->count(3)->hasAttached($tags[0], [])->create();

        // These events are not from the past, so testGetPastEventsByTags shouldn't return it
        Event::factory()->state($set_colla)->open()->hasAttached($tags[0], [])->create();
        Event::factory()->state($set_colla)->future()->hasAttached($tags[0], [])->create();

        $events[1]->tags()->attach($tags[1]->getId());
        $events[2]->tags()->attach($tags[1]->getId());
        $events[2]->tags()->attach($tags[2]->getId());

        // When we don't add tags, we need to return all
        $eventsResponse = (new EventsFilter($colla))->withTags([], FilterSearchTypesEnum::AND)->past()->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing(array_column($events->toArray(), 'id_event'), array_column($eventsResponse, 'id_event'));
        $eventsResponse = (new EventsFilter($colla))->withTags([], FilterSearchTypesEnum::OR)->past()->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing(array_column($events->toArray(), 'id_event'), array_column($eventsResponse, 'id_event'));

        // Filtering with AND & OR with only one tag should return only events with that tag
        $eventsResponse = (new EventsFilter($colla))->withTags([$tags[1]->getId()], FilterSearchTypesEnum::AND)->past()->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing(array_column(array_slice($events->toArray(), 1, 2), 'id_event'), array_column($eventsResponse, 'id_event'));
        $eventsResponse = (new EventsFilter($colla))->withTags([$tags[1]->getId()], FilterSearchTypesEnum::OR)->past()->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing(array_column(array_slice($events->toArray(), 1, 2), 'id_event'), array_column($eventsResponse, 'id_event'));

        // Test AND filter
        $eventsResponse = (new EventsFilter($colla))->withTags([$tags[0]->getId(), $tags[2]->getId()], FilterSearchTypesEnum::AND)->past()->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing([$events[2]['id_event']], array_column($eventsResponse, 'id_event'));

        // Test OR filter
        $eventsResponse = (new EventsFilter($colla))->withTags([$tags[0]->getId(), $tags[2]->getId()], FilterSearchTypesEnum::OR)->past()->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing(array_column($events->toArray(), 'id_event'), array_column($eventsResponse, 'id_event'));
    }
}
