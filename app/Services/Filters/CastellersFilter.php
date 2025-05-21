<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\Casteller;
use App\Colla;
use App\Enums\CastellersStatusEnum;
use App\Enums\FilterSearchTypesEnum;
use App\Event;
use App\Tag;
use App\Traits\DatatablesFilterTrait;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class CastellersFilter extends BaseFilter
{
    // Remove this trait once ConfigApps datatable is also using the datatables component
    use DatatablesFilterTrait;

    public function __construct(Colla $colla)
    {
        parent::__construct($this->eloquentBuilder = Casteller::query()
            ->where('castellers.colla_id', $colla->getId())
            ->select('castellers.*'));
    }

    /**
     * Return the IDs of the matching Castellers in an array.
     */
    public function getCastellerIds(): array
    {
        return array_column($this->eloquentBuilder()->get()->toArray(), 'id_casteller');
    }

    public function withEventAttendance(Event $event, array $attendanceStatuses, string $attendanceType = 'status'): self
    {
        $this->eloquentBuilder
            ->whereIn('id_casteller', $event->attendances->whereIn($attendanceType, $attendanceStatuses)->pluck('casteller_id')->toArray());

        return $this;
    }

    public function withMissingAttendance(Event $event, string $attendanceType = 'status'): self
    {
        $this->eloquentBuilder
            ->whereNotIn('id_casteller', $event->attendances->whereNotNull($attendanceType)->pluck('casteller_id')->toArray()
            );

        return $this;
    }

    public function withTags(array $includedTags, string $includedSearchType = FilterSearchTypesEnum::AND): self
    {
        if (in_array(Tag::TAG_ALL, $includedTags) || empty($includedTags)) {

            return $this;
        }

        $this->eloquentBuilder
            ->joinSub($this->getCastellersIdByTags($includedTags, $includedSearchType), 'castellers_id_by_tags', function ($join) {
                $join->on('castellers.id_casteller', '=', 'castellers_id_by_tags.casteller_id');
            });

        return $this;
    }

    public function withoutTags(array $excludedTags, string $excludedSearchType = FilterSearchTypesEnum::AND): self
    {
        if (empty($excludedTags)) {

            return $this;
        }

        $this->eloquentBuilder
            ->leftJoinSub($this->getCastellersIdByTags($excludedTags, $excludedSearchType), 'castellers_id_by_tags', function ($join) {
                $join->on('castellers.id_casteller', '=', 'castellers_id_by_tags.casteller_id');
            })->whereNull('castellers_id_by_tags.casteller_id');

        return $this;
    }

    public function withStatus(CastellersStatusEnum $status): self
    {
        switch ($status->value()) {
            case CastellersStatusEnum::NOOB:
                $this->eloquentBuilder->where('castellers.status', CastellersStatusEnum::Noob()->value());
                break;
            case CastellersStatusEnum::ACTIVE:
                $this->eloquentBuilder->where('castellers.status', CastellersStatusEnum::Active()->value());
                break;
            case CastellersStatusEnum::INACTIVE:
                $this->eloquentBuilder->where('castellers.status', CastellersStatusEnum::Inactive()->value());
                break;
            case CastellersStatusEnum::INJURED:
                $this->eloquentBuilder->where('castellers.status', CastellersStatusEnum::Injured()->value());
                break;
            case CastellersStatusEnum::ACTIVE_PINYA:
                $this->eloquentBuilder->where(function ($q) {
                    $q->orWhere('castellers.status', CastellersStatusEnum::Active()->value());
                    $q->orWhere('castellers.status', CastellersStatusEnum::Noob()->value());
                });
                break;
            case CastellersStatusEnum::ACTIVE_ALL:
                $this->eloquentBuilder->where(function ($q) {
                    $q->orWhere('castellers.status', CastellersStatusEnum::Active()->value());
                    $q->orWhere('castellers.status', CastellersStatusEnum::Noob()->value());
                    $q->orWhere('castellers.status', CastellersStatusEnum::Injured()->value());
                });
                break;
            case CastellersStatusEnum::ALL:
                $this;
                break;
        }

        return $this;
    }

    public function withName(?string $search = null): self
    {
        if (! $search) {

            return $this;
        }

        $this->eloquentBuilder->where(function ($q) use ($search) {
            $q->orWhere('castellers.name', 'LIKE', '%'.$search.'%');
            $q->orWhere('castellers.last_name', 'LIKE', '%'.$search.'%');
            $q->orWhere(DB::raw('concat(castellers.name," ",castellers.last_name)'), 'LIKE', '%'.$search.'%');
            $q->orWhere('castellers.alias', 'LIKE', '%'.$search.'%');
            $q->orWhere('castellers.email', 'LIKE', '%'.$search.'%');
            $q->orWhere('castellers.email2', 'LIKE', '%'.$search.'%');
        });

        return $this;
    }

    public function withAlias(?string $search = null): self
    {
        if (! $search) {

            return $this;
        }

        $this->eloquentBuilder->where(function ($q) use ($search) {
            $q->orWhere('castellers.alias', 'LIKE', '%'.$search.'%');
        });

        return $this;
    }

    private function getCastellersIdByTags(array $includedTags, string $includedSearchType): QueryBuilder
    {
        $casteller_tags = DB::table('casteller_tag')
            ->leftJoin('tags', 'casteller_tag.tag_id', '=', 'tags.id_tag')
            ->whereIn('tags.id_tag', $includedTags)
            ->select(DB::raw('casteller_tag.casteller_id'))
            ->groupBy('casteller_tag.casteller_id');

        if ($includedSearchType === FilterSearchTypesEnum::AND) {
            $casteller_tags->having(DB::raw('count(tags.id_tag)'), '=', strval(count($includedTags)));
        }

        return $casteller_tags;
    }
}
