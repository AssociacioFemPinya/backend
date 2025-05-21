<?php

namespace App\Imports;

use App\Casteller;
use App\Colla;
use App\Enums\Gender;
use App\Enums\TypeNationalId;
use App\Enums\TypeTags;
use App\Managers\CastellersManager;
use App\Rules\tags_exist;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Symfony\Component\HttpFoundation\ParameterBag;

class CastellersImport implements SkipsEmptyRows, SkipsOnError, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array  $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private CastellersManager $castellersManager;

    use SkipsErrors;

    public function __construct(CastellersManager $castellersManager)
    {
        $this->castellersManager = $castellersManager;
    }

    public function model(array $row)
    {

        $colla = Colla::getCurrent();
        if ($row['id_tags'] != '') {
            $row['tags'] = $tags = preg_split('/,|\./', $row['id_tags']);
        }
        if ($row['id_position'] != '') {
            $row['position'] = (int) $row['id_position'];
        }

        $parameterBag = new ParameterBag($row);
        $casteller = Casteller::getCastellerByAlias($parameterBag->get('alias'), $colla->getId());
        if ($casteller) {
            $casteller = $this->castellersManager->updateCasteller($casteller, $parameterBag);
        } else {
            $casteller = $this->castellersManager->createCasteller($colla, $parameterBag);
        }
    }

    public function rules(): array
    {
        $colla = Colla::getCurrent();

        return [
            'num_soci' => 'nullable|max:20|min:1',
            'national_id_number' => 'nullable|max:50|min:7',
            'nationality' => 'nullable|max:50|min:3',
            'national_id_type' => 'nullable|max:8|min:3|in:'.implode(',', TypeNationalId::getTypes()),
            'gender' => 'nullable|digits_between:0,3|in:'.implode(',', Gender::getTypes()),
            'name' => 'nullable|max:150|min:1',
            'last_name' => 'nullable|max:150|min:1',
            'alias' => 'required|max:150|min:2',
            'birthdate' => 'nullable|date_format:d/m/Y',
            'subscription_date' => 'nullable|date_format:d/m/Y',
            'family' => 'nullable|max:150|min:2',
            'email' => 'nullable|email:rfc',
            'email2' => 'nullable|email:rfc',
            'phone' => 'nullable|max:20|min:6',
            'emergency_phone' => 'nullable|max:20|min:6',
            'mobile_phone' => 'nullable|max:20|min:6',
            'address' => 'nullable|max:255|min:3',
            'country' => 'nullable|max:100|min:3',
            'city' => 'nullable|max:100|min:3',
            'comarca' => 'nullable|max:100|min:3',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'shoulder_height' => 'nullable|numeric',
            'id_tags' => [
                'nullable',
                new tags_exist,
            ],
            'id_position' => [
                'nullable',
                'numeric',
                Rule::exists('tags', 'id_tag')->where(function ($query) {
                    $query
                        ->where('type', TypeTags::POSITIONS)
                        ->where('colla_id', Colla::getCurrent()->getId());
                }),
            ],
            'status' => 'required',
        ];
    }
}
