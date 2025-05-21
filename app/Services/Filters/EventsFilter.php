<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\Colla;
use App\Enums\EventTypeEnum;
use App\Enums\FilterSearchTypesEnum;
use App\Event;
use App\Period;
use App\Tag;
use App\Traits\DatatablesFilterTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class EventsFilter extends BaseFilter
{
    private Builder $eloquentBuilder;

    use DatatablesFilterTrait;

    public function __construct(Colla $colla)
    {
        parent::__construct($this->eloquentBuilder = Event::query()
            ->where('events.colla_id', $colla->getId())
            ->select('events.*'));
    }

    public function upcoming(): self
    {
        $this->eloquentBuilder
            ->where('start_date', '>', Carbon::now());

        return $this;
    }

    public function liveOrUpcoming(): self
    {
        $currentTime = Carbon::now();
        $this->eloquentBuilder
            ->whereRaw("DATE_ADD(start_date, INTERVAL duration MINUTE) > '{$currentTime}'");

        return $this;
    }

    public function past(): self
    {
        $this->eloquentBuilder
            ->where('start_date', '<', Carbon::now());

        return $this;
    }

    public function open(): self
    {
        $this->eloquentBuilder
            ->where('open_date', '<', Carbon::now())
            ->where('close_date', '>', Carbon::now());

        return $this;
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
            ->joinSub($this->getEventsIDByTags($includedTags, $includedSearchType), 'events_id_by_tags', function ($join) {
                $join->on('events.id_event', '=', 'events_id_by_tags.event_id');
            });

        return $this;
    }

    public function withoutTags(array $excludedTags, string $excludedSearchType = FilterSearchTypesEnum::AND): self
    {
        if (empty($excludedTags)) {
            return $this;
        }
        $this->eloquentBuilder
            ->leftJoinSub($this->getEventsIDByTags($excludedTags, $excludedSearchType), 'events_id_by_tags', function ($join) {
                $join->on('events.id_event', '=', 'events_id_by_tags.event_id');
            })->whereNull('events_id_by_tags.event_id');

        return $this;
    }

    public function withCastellerTags(array $includedTags, string $includedSearchType = FilterSearchTypesEnum::OR): self
    {

        $this->eloquentBuilder
            ->joinSub($this->getEventsIDByCastellerTags($includedTags, $includedSearchType), 'events_id_by_tags', function ($join) {
                $join->on('events.id_event', '=', 'events_id_by_tags.id_event');
            });

        return $this;
    }

    public function withPeriod(?Period $period = null): self
    {
        if (! $period) {
            return $this;
        }

        $this->eloquentBuilder
            ->where('start_date', '>', $period->getStartPeriod())
            ->where('start_date', '<', $period->getEndPeriod());

        return $this;
    }

    public function withType(int $status): self
    {
        switch ($status) {
            case EventTypeEnum::ASSAIG:
                $this->eloquentBuilder->where('events.type', EventTypeEnum::Assaig()->value());
                break;
            case EventTypeEnum::ACTUACIO:
                $this->eloquentBuilder->where('events.type', EventTypeEnum::Actuacio()->value());
                break;
            case EventTypeEnum::ACTIVITAT:
                $this->eloquentBuilder->where('events.type', EventTypeEnum::Activitat()->value());
                break;
        }

        return $this;
    }

    public function beforeDate(string $date): self
    {
        $this->eloquentBuilder->where('start_date', '<', $date);

        return $this;
    }

    public function afterDate(string $date): self
    {
        $this->eloquentBuilder->where('start_date', '>', $date);

        return $this;
    }

    public function today(): self
    {
        $this->eloquentBuilder
            ->where('start_date', '>=', Carbon::now()->startOfDay())
            ->where('start_date', '<=', Carbon::now()->endOfDay());

        return $this;
    }

    private function getEventsIDByTags(array $includedTags, string $includedSearchType): QueryBuilder
    {
        $event_tags = DB::table('event_tag')
            ->leftJoin('tags', 'event_tag.tag_id', '=', 'tags.id_tag')
            ->whereIn('tags.id_tag', $includedTags)
            ->select(DB::raw('event_tag.event_id'))
            ->groupBy('event_tag.event_id');

        if ($includedSearchType === FilterSearchTypesEnum::AND) {
            $event_tags->having(DB::raw('count(tags.id_tag)'), '=', count($includedTags));
        }

        return $event_tags;
    }

    private function getEventsIDByCastellerTags(array $includedTags, string $includedSearchType): QueryBuilder
    {
        $event_tags = DB::table('events')
            ->leftJoin('event_casteller_tag', 'events.id_event', '=', 'event_casteller_tag.event_id')
            ->leftJoin('tags', 'event_casteller_tag.tag_id', '=', 'tags.id_tag')
            ->whereNull('tags.id_tag')
            ->orWhereIn('tags.id_tag', $includedTags)
            ->select(DB::raw('events.id_event'))
            ->groupBy('events.id_event');

        if ($includedSearchType === FilterSearchTypesEnum::AND) {
            $event_tags->having(DB::raw('count(tags.id_tag)'), '=', count($includedTags));
        }

        return $event_tags;
    }
}
