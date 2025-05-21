<?php

// app/DataTables/BaseDataTable.php

namespace App\DataTables;

class Column
{
    public string $name;

    public string $title;

    public bool $orderable;

    public ?int $width;

    public function __construct(string $name, string $title, bool $orderable, ?int $width)
    {
        $this->name = $name;
        $this->title = $title;
        $this->orderable = $orderable;
        $this->width = $width;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrderable(): string
    {
        return $this->orderable;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }
}
