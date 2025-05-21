<?php

namespace Tests\Unit\EventsFilter;

use App\Colla;
use App\Enums\FilterSearchTypesEnum;
use App\Event;
use App\Services\Filters\EventsFilter;
use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithoutTagsTest extends TestCase
{
    use RefreshDatabase;

    public function testWithoutTags()
    {
        $colla = Colla::factory()->create();
        $set_colla = [
            'colla_id' => $colla->getId(),
        ];

        $tags = Tag::factory()->state($set_colla)->event()->count(3)->create();
        $events = Event::factory()->state($set_colla)->count(3)->hasAttached($tags[0], [])->create();

        $events[1]->tags()->attach($tags[1]->getId());
        $events[2]->tags()->attach($tags[1]->getId());
        $events[2]->tags()->attach($tags[2]->getId());

        // When we don't add tags, we need to return all
        $eventsResponse = (new EventsFilter($colla))->withoutTags([], FilterSearchTypesEnum::AND)->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing(array_column($events->toArray(), 'id_event'), array_column($eventsResponse, 'id_event'));
        $eventsResponse = (new EventsFilter($colla))->withoutTags([], FilterSearchTypesEnum::OR)->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing(array_column($events->toArray(), 'id_event'), array_column($eventsResponse, 'id_event'));

        // Filtering with AND & OR with only one tag should return all events that doesn't have that tag
        $eventsResponse = (new EventsFilter($colla))->withoutTags([$tags[1]->getId()], FilterSearchTypesEnum::AND)->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing([$events[0]['id_event']], array_column($eventsResponse, 'id_event'));
        $eventsResponse = (new EventsFilter($colla))->withoutTags([$tags[1]->getId()], FilterSearchTypesEnum::OR)->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing([$events[0]['id_event']], array_column($eventsResponse, 'id_event'));

        // Filtering with many tags with AND should return the events that doesn't have all those tags
        $eventsResponse = (new EventsFilter($colla))->withoutTags([$tags[1]->getId(), $tags[2]->getId()], FilterSearchTypesEnum::AND)->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing([$events[0]['id_event'], $events[1]['id_event']], array_column($eventsResponse, 'id_event'));

        // Filtering with many tags with OR should return the events that doesn't have any of those tags
        $eventsResponse = (new EventsFilter($colla))->withoutTags([$tags[1]->getId(), $tags[2]->getId()], FilterSearchTypesEnum::OR)->eloquentBuilder()->get()->toArray();
        $this->assertEqualsCanonicalizing([$events[0]['id_event']], array_column($eventsResponse, 'id_event'));
    }
}
