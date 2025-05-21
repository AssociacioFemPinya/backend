<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    private array $columns;

    private array $rows;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
        $this->rows = [];
    }

    public function addRow(array $row): self
    {
        array_push($this->rows, $row);

        return $this;
    }

    public function generate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance.csv"',
        ];

        $columns = $this->columns;
        $rows = $this->rows;
        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, $this->columns);
            foreach ($this->rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
