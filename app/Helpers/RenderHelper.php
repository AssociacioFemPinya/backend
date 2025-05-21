<?php

namespace App\Helpers;

use App\Casteller;
use App\Enums\ScaledAttendanceStatus;

class RenderHelper
{
    /**
     * Returns Switchery Field rendered
     */
    public static function fieldSwitcher(bool $status, ?string $dataIdName = null, ?string $dataIdValue = null, ?string $name = null, ?string $id = null, ?string $additionalClass = null, ?string $value = null): string
    {
        $dataInfo = ($dataIdName && $dataIdValue) ? $dataIdName.'="'.$dataIdValue.'"' : '';
        $fieldName = ($name) ? 'name="'.$name.'"' : ' ';
        $fieldId = ($id) ? 'id="'.$id.'"' : ' ';
        $fieldValue = ($value) ? 'value="'.$value.'"' : ' ';

        if ($status == 1) {
            $checked = 'checked';
            $checkedClass = 'active';
        } else {
            $checked = '';
            $checkedClass = 'inactive';
        }

        return '<input type="checkbox" '.$dataInfo.' '.$fieldName.' '.$fieldValue.' '.$fieldId.' class="js-switchery '.$checkedClass.' '.$additionalClass.'" '.$checked.' >';
    }

    /**
     * Returns a Input Field rendered
     */
    public static function fieldInput(string $value, ?string $type = 'text', ?bool $readonly = false, ?bool $disabled = false, ?string $dataIdName = null, ?string $dataIdValue = null, ?string $name = null, ?string $id = null, ?string $additionalClass = null, ?string $additionalDataIdName = null, ?string $additionalDataIdValue = null, ?int $min = 0, ?int $max = 0): string
    {
        $dataInfo = ($dataIdName && $dataIdValue) ? $dataIdName.'="'.$dataIdValue.'"' : '';
        $dataInfo .= ($additionalDataIdName && $additionalDataIdValue) ? $additionalDataIdName.'="'.$additionalDataIdValue.'"' : '';
        $fieldName = ($name) ? 'name="'.$name.'"' : ' ';
        $fieldId = ($id) ? 'id="'.$id.'"' : ' ';
        $fieldReadonly = ($readonly) ? ' readonly="readonly" ' : '';
        $fielfDisabled = ($disabled) ? 'disabled' : '';
        $minField = ($type === 'number' && ! is_null($min)) ? 'min="'.$min.'" ' : '';
        $maxField = ($type === 'number' && ! is_null($max)) ? 'max="'.$max.'" ' : '';

        return '<input type="'.$type.'" '.$minField.' '.$maxField.' '.$dataInfo.' '.$fieldName.' '.$fieldId.' class="form-control '.$additionalClass.'" '.$fieldReadonly.' '.$fielfDisabled.' value="'.$value.'">';
    }

    /**
     * Returns a Input Field rendered
     */
    public static function fieldTextarea(?string $text = '', ?int $cols = 10, ?int $rows = 5, ?bool $readonly = false, ?bool $disabled = false, ?string $dataIdName = null, ?string $dataIdValue = null, ?string $name = null, ?string $id = null, ?string $additionalClass = null): string
    {
        $dataInfo = ($dataIdName && $dataIdValue) ? $dataIdName.'="'.$dataIdValue.'"' : '';
        $fieldName = ($name) ? 'name="'.$name.'"' : ' ';
        $fieldId = ($id) ? 'id="'.$id.'"' : ' ';
        $fieldReadonly = ($readonly) ? ' readonly="readonly" ' : '';
        $fielfDisabled = ($disabled) ? 'disabled' : '';

        return '<textarea cols="'.$cols.'" rows="'.$rows.'" '.$dataInfo.' '.$fieldName.' '.$fieldId.' class="form-control '.$additionalClass.'" '.$fieldReadonly.' '.$fielfDisabled.'>'.$text.'</textarea>';
    }

    /**
     * Returns a Status input field
     *
     * @param  string|null  $dataIdName
     * @param  string|null  $dataIdValue
     * @param  string|null  $buttonClass
     * @param  string|null  $iClass
     * @param  string|null  $name
     * @param  string|null  $id
     */
    public static function profileImage(?string $photo = null, ?string $size = '32'): string
    {
        return '<img src="'.$photo.'" class="img-avatar img-avatar'.$size.'" alt="">';
    }

    /**
     * Returns a Status input field
     */
    public static function fieldbutton(?string $dataIdName = null, ?string $dataIdValue = null, ?string $buttonClass = 'btn', ?string $iClass = '', ?string $name = null, ?string $id = null, ?bool $disabled = false): string
    {
        $dataInfo = ($dataIdName && $dataIdValue) ? $dataIdName.'="'.$dataIdValue.'"' : '';
        $fieldName = ($name) ? 'name="'.$name.'"' : ' ';
        $fieldId = ($id) ? 'id="'.$id.'"' : ' ';
        $disabled = $disabled ? 'disabled' : ' ';

        return '<button '.$fieldName.' '.$fieldId.' '.$dataInfo.' class="'.$buttonClass.'"'.$disabled.'><i class="'.$iClass.'"></i></button>';
    }

    /**
     * Returns a Status input field
     */
    public static function fieldSelect(?array $values = [], ?array $options = [], ?string $name = null, ?string $id = null, ?bool $multiple = false, ?bool $disabled = false, ?string $additionalClass = '', ?string $dataIdName = null, ?string $dataIdValue = null): string
    {
        $dataInfo = ($dataIdName && $dataIdValue) ? $dataIdName.'="'.$dataIdValue.'"' : '';
        $fieldName = ($name) ? 'name="'.$name.'[]"' : ' ';
        $fieldId = ($id) ? 'id="'.$id.'"' : ' ';
        $fielfDisabled = ($disabled) ? 'disabled' : '';
        $fielfMultiple = ($multiple) ? 'multiple' : '';

        $string = '<select '.$fieldName.' '.$fieldId.' '.$dataInfo.' class="selectize2 form-control '.$additionalClass.'" style="width: 100%" '.$fielfMultiple.' '.$fielfDisabled.'>';
        foreach ($options as $id => $value) {
            if (in_array($id, $values)) {
                $string .= '<option value="'.$id.'" selected>'.$value.'</option>';
            } else {
                $string .= '<option value="'.$id.'">'.$value.'</option>';
            }
        }
        $string .= '</select>';

        return $string;

    }

    public static function profileActiveTab(string $activeTab): array
    {

        $tabs = [

            'li_profile' => '',
            'div_profile' => '',
            'li_users' => '',
            'div_users' => '',
            'li_config' => '',
            'div_config' => '',
            'li_periods' => '',
            'div_periods' => '',

        ];

        switch ($activeTab) {

            case 'profile':
                $tabs['li_profile'] = 'active';
                $tabs['div_profile'] = 'show active';
                break;
            case 'users':
                $tabs['li_users'] = 'active';
                $tabs['div_users'] = 'show active';
                break;
            case 'config':
                $tabs['li_config'] = 'active';
                $tabs['div_config'] = 'show active';
                break;
            case 'periods':
                $tabs['li_periods'] = 'active';
                $tabs['div_periods'] = 'show active';
                break;
            default:
                $tabs['li_profile'] = 'active';
                $tabs['div_periods'] = 'show active';
                break;
        }

        return $tabs;

    }

    public static function getAttendanceIconColor(?string $attendanceStatus)
    {

        switch ($attendanceStatus) {
            case ScaledAttendanceStatus::YESVERIFIED:
                $color = 'text-success ';
                break;
            case ScaledAttendanceStatus::YES:
                $color = 'text-success ';
                break;
            case ScaledAttendanceStatus::UNKNOWN:
                $color = 'text-warning ';
                break;
            case ScaledAttendanceStatus::NO:
                $color = 'text-danger ';
                break;
            default:
                $color = 'text-warning ';
                break;
        }

        return $color;
    }

    public static function getAttendanceIcon(?string $attendanceStatus, bool $withColor = true): string
    {
        $icon = '';

        $color = ($withColor) ? self::getAttendanceIconColor($attendanceStatus) : '';

        switch ($attendanceStatus) {
            case ScaledAttendanceStatus::YESVERIFIED:
                $icon = $color.'fa-solid fa-check-double';
                break;
            case ScaledAttendanceStatus::YES:
                $icon = $color.'fa-solid fa-check';
                break;
            case ScaledAttendanceStatus::UNKNOWN:
                $icon = $color.'fa-solid fa-question';
                break;
            case ScaledAttendanceStatus::NO:
                $icon = $color.'fa-solid fa-xmark';
                break;
            default:
                $icon = $color.'fa-solid fa-question';
                break;
        }

        return $icon;

    }

    public static function getAttendanceIconEditor(?string $attendanceStatus, bool $withColor = true): string
    {
        $icon = '';

        $color = ($withColor) ? self::getAttendanceIconColor($attendanceStatus) : 'text-muted ';

        switch ($attendanceStatus) {
            case ScaledAttendanceStatus::YESVERIFIED:
                $icon = $color.'fa-solid fa-check-double';
                break;
            case ScaledAttendanceStatus::YES:
                $icon = $color.'fa-solid fa-check-circle';
                break;
            case ScaledAttendanceStatus::UNKNOWN:
                $icon = $color.'fa-solid fa-question-circle';
                break;
            case ScaledAttendanceStatus::NO:
                $icon = $color.'fa-solid fa-xmark-circle';
                break;
            default:
                $icon = $color.'fa-solid fa-question-circle';
                break;
        }

        return $icon;

    }

    public static function renderCastellerButton(Casteller $casteller, ScaledAttendanceStatus $status, string $tooltipTxt, bool $positioned = false): string
    {
        switch ($status) {
            case ScaledAttendanceStatus::YESVERIFIED():
                $class = 'btn-success';
                break;
            case ScaledAttendanceStatus::YES():
                $class = 'btn-success';
                break;
            case ScaledAttendanceStatus::UNKNOWN():
                $class = 'btn-warning';
                break;
            case ScaledAttendanceStatus::NO():
                $class = 'btn-danger';
                break;
            default:
                $class = 'btn-warning';
        }

        if ($positioned) {
            return '<button
            class="btn positioned text-left pr-1 pl-1"
            data-id_casteller="'.$casteller->getId().'"
            data-id_row="'.$casteller->boardPosition[0]->getRow()->getDivId().'"
            data-toggle="tooltip"
            data-placement="top"
            data-html="true"
            title="'.$tooltipTxt.'">
            <span class="pl-1 pr-1"><i class="'.RenderHelper::getAttendanceIconEditor($status, false).' fa-lg"></i></span><span class="span-name-positioned"> '.$casteller->getDisplayName().'</span>
            </button>';
        } else {
            return '<button
            class="btn '.$class.' btn-casteller text-left pr-1 pl-1"
            data-id_casteller="'.$casteller->getId().'"
            data-toggle="tooltip"
            data-placement="top"
            data-html="true"
            title="'.$tooltipTxt.'">
            <span class="pl-1 pr-1"><i class="'.RenderHelper::getAttendanceIconEditor($status).' fa-lg"></span></i><span class="span-name"> '.$casteller->getDisplayName().'</span>
            </button>';
        }
    }
}
