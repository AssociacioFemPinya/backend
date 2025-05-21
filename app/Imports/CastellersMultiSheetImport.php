<?php

namespace App\Imports;

use App\Managers\CastellersManager;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CastellersMultiSheetImport implements WithMultipleSheets
{
    public function __construct(CastellersManager $castellersManager)
    {
        $this->castellersManager = $castellersManager;
    }

    public function sheets(): array
    {
        return [
            0 => new CastellersImport($this->castellersManager),
        ];
    }
}
