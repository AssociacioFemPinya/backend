<?php

namespace App\Exports;

use App\Colla;
use App\Enums\TypeTags;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PositionsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $colla_id;

    protected $attributes = [
        'id_tag', 'name',
    ];

    public function __construct(int $colla_id)
    {
        $this->colla_id = $colla_id;
    }

    public function headings(): array
    {
        return $this->attributes;
    }

    public function collection()
    {
        $positionsTags = Colla::find($this->colla_id)->getTags(TypeTags::POSITIONS);

        return $positionsTags;

    }

    public function map($tag): array
    {
        return $tag->only($this->attributes);

    }

    public function title(): string
    {
        return 'Positions';
    }
}
