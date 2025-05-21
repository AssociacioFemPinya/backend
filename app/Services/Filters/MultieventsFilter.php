<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\Colla;
use App\Enums\EventTypeEnum;
use App\Enums\FilterSearchTypesEnum;
use App\Multievent;
use App\Tag;
use App\Traits\DatatablesFilterTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class MultieventsFilter extends BaseFilter
{
    private Builder $eloquentBuilder;

    use DatatablesFilterTrait;

    public function __construct(Colla $colla)
    {
        parent::__construct($this->eloquentBuilder = Multievent::query()
            ->where('multievents.colla_id', $colla->getId())
            ->select('multievents.*'));
    }

    public function visible(): self
    {
        $this->eloquentBuilder
            ->where('visibility', true);

        return $this;
    }

    public function withTags(array $includedTags, string $includedSearchType = FilterSearchTypesEnum::AND): self
    {
        if (in_array(Tag::TAG_ALL, $includedTags) || empty($includedTags)) {
            return $this;
        }

        $this->eloquentBuilder
            ->joinSub($this->getMultieventsIDByTags($includedTags, $includedSearchType), 'multievents_id_by_tags', function ($join) {
                $join->on('multievents.id_multievent', '=', 'multievents_id_by_tags.multievent_id');
            });

        return $this;
    }

    public function withoutTags(array $excludedTags, string $excludedSearchType = FilterSearchTypesEnum::AND): self
    {
        if (empty($excludedTags)) {
            return $this;
        }

        $this->eloquentBuilder
            ->leftJoinSub($this->getMultieventsIDByTags($excludedTags, $excludedSearchType), 'multievents_id_by_tags', function ($join) {
                $join->on('multievents.id_multievent', '=', 'multievents_id_by_tags.multievent_id');
            })->whereNull('multievents_id_by_tags.multievent_id');

        return $this;
    }

    public function withCastellerTags(array $includedTags, string $includedSearchType = FilterSearchTypesEnum::OR): self
    {
        $this->eloquentBuilder
            ->joinSub($this->getMultieventsIDByCastellerTags($includedTags, $includedSearchType), 'multievents_id_by_tags', function ($join) {
                $join->on('multievents.id_multievent', '=', 'multievents_id_by_tags.multievent_id');
            });

        return $this;
    }

    public function withType(int $status): self
    {
        switch ($status) {
            case EventTypeEnum::ASSAIG:
                $this->eloquentBuilder->where('multievents.type', EventTypeEnum::Assaig()->value());
                break;
            case EventTypeEnum::ACTUACIO:
                $this->eloquentBuilder->where('multievents.type', EventTypeEnum::Actuacio()->value());
                break;
            case EventTypeEnum::ACTIVITAT:
                $this->eloquentBuilder->where('multievents.type', EventTypeEnum::Activitat()->value());
                break;
        }

        return $this;
    }

    private function getMultieventsIDByTags(array $includedTags, string $includedSearchType): QueryBuilder
    {
        $multievent_tags = DB::table('multievent_tag')
            ->leftJoin('tags', 'multievent_tag.tag_id', '=', 'tags.id_tag')
            ->whereIn('tags.id_tag', $includedTags)
            ->select(DB::raw('multievent_tag.multievent_id'))
            ->groupBy('multievent_tag.multievent_id');

        if ($includedSearchType === FilterSearchTypesEnum::AND) {
            $multievent_tags->having(DB::raw('count(tags.id_tag)'), '=', count($includedTags));
        }

        return $multievent_tags;
    }

    private function getMultieventsIDByCastellerTags(array $includedTags, string $includedSearchType): QueryBuilder
    {
        $multievent_tags = DB::table('multievents')
            ->leftJoin('multievent_casteller_tag', 'multievents.id_multievent', '=', 'multievent_casteller_tag.multievent_id')
            ->leftJoin('tags', 'multievent_casteller_tag.tag_id', '=', 'tags.id_tag')
            ->whereNull('tags.id_tag')
            ->orWhereIn('tags.id_tag', $includedTags)
            ->select(DB::raw('multievents.id_multievent'))
            ->groupBy('multievents.id_multievent');

        if ($includedSearchType === FilterSearchTypesEnum::AND) {
            $multievent_tags->having(DB::raw('count(tags.id_tag)'), '=', count($includedTags));
        }

        return $multievent_tags;
    }
}
