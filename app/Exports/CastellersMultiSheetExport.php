<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CastellersMultiSheetExport implements WithMultipleSheets
{
    protected $colla_id;

    public function __construct(int $colla_id)
    {
        $this->colla_id = $colla_id;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new CastellersExport($this->colla_id);
        $sheets[] = new TagsExport($this->colla_id);
        $sheets[] = new PositionsExport($this->colla_id);

        return $sheets;
    }
}
