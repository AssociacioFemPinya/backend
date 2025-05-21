<?php

namespace Tests\Unit;

use App\Casteller;
use App\Colla;
use App\Enums\FilterSearchTypesEnum;
use App\Services\Filters\CastellersFilter;
use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CastellersFilterTest extends TestCase
{
    use RefreshDatabase;

    public function testWithTags()
    {
        $colla = Colla::factory()->create();
        $set_colla = [
            'colla_id' => $colla->getId(),
        ];

        $tags = Tag::factory()->state($set_colla)->casteller()->count(3)->create();
        $castellers = Casteller::factory()->state($set_colla)->count(3)->hasAttached($tags[0], [])->create();

        $castellers[1]->tags()->attach($tags[1]->getId());
        $castellers[2]->tags()->attach($tags[1]->getId());
        $castellers[2]->tags()->attach($tags[2]->getId());

        // When we don't add tags, we need to return all
        $castellersResponse = (new CastellersFilter($colla))->withTags([], FilterSearchTypesEnum::AND)->eloquentBuilder()->get()->toArray();
        $this->assertEquals(array_column($castellers->toArray(), 'id_casteller'), array_column($castellersResponse, 'id_casteller'));
        $castellersResponse = (new CastellersFilter($colla))->withTags([], FilterSearchTypesEnum::OR)->eloquentBuilder()->get()->toArray();
        $this->assertEquals(array_column($castellers->toArray(), 'id_casteller'), array_column($castellersResponse, 'id_casteller'));

        // Filtering with AND & OR with only one tag should return only castellers with that tag
        $castellersResponse = (new CastellersFilter($colla))->withTags([$tags[1]->getId()], FilterSearchTypesEnum::AND)->eloquentBuilder()->get()->toArray();
        $this->assertEquals(array_column(array_slice($castellers->toArray(), 1, 2), 'id_casteller'), array_column($castellersResponse, 'id_casteller'));
        $castellersResponse = (new CastellersFilter($colla))->withTags([$tags[1]->getId()], FilterSearchTypesEnum::OR)->eloquentBuilder()->get()->toArray();
        $this->assertEquals(array_column(array_slice($castellers->toArray(), 1, 2), 'id_casteller'), array_column($castellersResponse, 'id_casteller'));

        // Test AND filter
        $castellersResponse = (new CastellersFilter($colla))->withTags([$tags[0]->getId(), $tags[2]->getId()], FilterSearchTypesEnum::AND)->eloquentBuilder()->get()->toArray();
        $this->assertEquals([$castellers[2]['id_casteller']], array_column($castellersResponse, 'id_casteller'));

        // Test OR filter
        $castellersResponse = (new CastellersFilter($colla))->withTags([$tags[0]->getId(), $tags[2]->getId()], FilterSearchTypesEnum::OR)->eloquentBuilder()->get()->toArray();
        $this->assertEquals(array_column($castellers->toArray(), 'id_casteller'), array_column($castellersResponse, 'id_casteller'));
    }

    public function testWithoutTags()
    {
        $colla = Colla::factory()->create();
        $set_colla = [
            'colla_id' => $colla->getId(),
        ];

        $tags = Tag::factory()->state($set_colla)->casteller()->count(3)->create();
        $castellers = Casteller::factory()->state($set_colla)->count(3)->hasAttached($tags[0], [])->create();

        $castellers[1]->tags()->attach($tags[1]->getId());
        $castellers[2]->tags()->attach($tags[1]->getId());
        $castellers[2]->tags()->attach($tags[2]->getId());

        // When we don't add tags, we need to return all
        $castellersResponse = (new CastellersFilter($colla))->withoutTags([], FilterSearchTypesEnum::AND)->eloquentBuilder()->get()->toArray();
        $this->assertEquals(array_column($castellers->toArray(), 'id_casteller'), array_column($castellersResponse, 'id_casteller'));
        $castellersResponse = (new CastellersFilter($colla))->withoutTags([], FilterSearchTypesEnum::OR)->eloquentBuilder()->get()->toArray();
        $this->assertEquals(array_column($castellers->toArray(), 'id_casteller'), array_column($castellersResponse, 'id_casteller'));

        // Filtering with AND & OR with only one tag should return all castellers that doesn't have that tag
        $castellersResponse = (new CastellersFilter($colla))->withoutTags([$tags[1]->getId()], FilterSearchTypesEnum::AND)->eloquentBuilder()->get()->toArray();
        $this->assertEquals([$castellers[0]['id_casteller']], array_column($castellersResponse, 'id_casteller'));
        $castellersResponse = (new CastellersFilter($colla))->withoutTags([$tags[1]->getId()], FilterSearchTypesEnum::OR)->eloquentBuilder()->get()->toArray();
        $this->assertEquals([$castellers[0]['id_casteller']], array_column($castellersResponse, 'id_casteller'));

        // Filtering with many tags with AND should return the castellers that doesn't have all those tags
        $castellersResponse = (new CastellersFilter($colla))->withoutTags([$tags[1]->getId(), $tags[2]->getId()], FilterSearchTypesEnum::AND)->eloquentBuilder()->get()->toArray();
        $this->assertEquals([$castellers[0]['id_casteller'], $castellers[1]['id_casteller']], array_column($castellersResponse, 'id_casteller'));

        // Filtering with many tags with OR should return the castellers that doesn't have any of those tags
        $castellersResponse = (new CastellersFilter($colla))->withoutTags([$tags[1]->getId(), $tags[2]->getId()], FilterSearchTypesEnum::OR)->eloquentBuilder()->get()->toArray();
        $this->assertEquals([$castellers[0]['id_casteller']], array_column($castellersResponse, 'id_casteller'));
    }
}
