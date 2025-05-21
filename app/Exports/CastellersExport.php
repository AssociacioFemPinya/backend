<?php

namespace App\Exports;

use App\Casteller;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class CastellersExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $colla_id;

    protected $attributes = [
        'num_soci', 'nationality', 'national_id_number', 'national_id_type', 'name', 'last_name', 'alias', 'gender',
        'birthdate', 'subscription_date', 'email', 'email2', 'phone', 'mobile_phone', 'emergency_phone', 'address',
        'postal_code', 'city', 'comarca', 'province', 'country', 'comments', 'height', 'weight', 'shoulder_height', 'status',
        'id_tags', 'id_position',
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
        return Casteller::where('colla_id', $this->colla_id)->get();
    }

    public function map($casteller): array
    {
        $mappedAttributes = [];

        foreach ($this->attributes as $attribute) {
            $value = $casteller->$attribute;

            switch ($attribute) {
                case 'birthdate':
                    $mappedAttributes[$attribute] = $value ? $value->format('d/m/Y') : '';
                    break;

                case 'id_tags':
                    $tags = $casteller->getTags();
                    $mappedAttributes[$attribute] = $tags->isNotEmpty() ? $tags->pluck('id_tag')->implode(',') : '';
                    break;

                case 'id_position':
                    $position = $casteller->getPosition();
                    $mappedAttributes[$attribute] = ! empty($position) ? $position->id_tag : '';
                    break;

                default:
                    $mappedAttributes[$attribute] = $value;
                    break;
            }
        }

        return $mappedAttributes;
    }

    public function title(): string
    {
        return 'Castellers';
    }
}
