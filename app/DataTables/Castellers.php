<?php

namespace App\DataTables;

use App\Casteller;
use App\Helpers\Humans;
use App\Helpers\RenderHelper;
use App\User;
use Illuminate\Support\Facades\DB;

class Castellers extends BaseDataTable
{
    const route = 'castellers.list-ajax';

    /**
     * Castellers constructor
     *
     * @return Castellers
     */
    public function __construct(User $user)
    {
        $this
            ->setId('castellers')
            ->setUrl(route(self::route))
            ->setTitle(trans('general.castellers'))
            ->setOrder(1)
            ->setPostAjaxData(['tags', 'status', 'filter_search_type'])
            ->setSearcheableColumns([DB::raw('concat(castellers.name," ",castellers.last_name)'), 'castellers.alias'])
            ->setEmbedded(true);

        $boards_enabled = $user->getColla()->getConfig()->getBoardsEnabled();

        switch ($user->can('view casteller personals')) {
            case true:
                $this->addColumn(name: 'photo', title: '', width: 0);
                $this->addColumn(name: 'alias', title: trans('casteller.alias'), orderable: true, width: 15);
                $this->addColumn(name: 'name', title: trans('general.name_last_name'), orderable: true, width: 20);
                $this->addColumn(name: 'status', title: trans('casteller.status'), orderable: true, width: 10);
                if ($boards_enabled) {
                    $this->addColumn(name: 'position', title: trans('casteller.position'));
                }
                $this->addColumn(name: 'tags', title: trans('general.tags'), width: 25);
                $this->addColumn(name: 'gender', title: trans('casteller.gender'));
                break;

            case false:
                $this->addColumn(name: 'photo', title: '', width: 0);
                $this->addColumn(name: 'alias', title: trans('casteller.alias'), orderable: true, width: 20);
                $this->addColumn(name: 'status', title: trans('casteller.status'), orderable: true, width: 15);
                if ($boards_enabled) {
                    $this->addColumn(name: 'position', title: trans('casteller.position'), width: 15);
                }
                $this->addColumn(name: 'tags', title: trans('general.tags'), width: $boards_enabled ? 25 : 40);
                break;
        }
    }

    public function render($user, $request, $castellersFilter): \stdClass
    {
        $params = [
            'user_can_view_casteller_personals' => $user->can('view casteller personals'),
        ];
        $castellersFilter->with('tags')->with('colla');

        return $this->renderRows(function (Casteller $casteller, array $params) {
            $name = $casteller->getName().' '.$casteller->getLastName();

            return [
                'photo' => RenderHelper::profileImage($casteller->getProfileImage('xs')),
                'alias' => $casteller->getDisplayName(),
                'name' => $params['user_can_view_casteller_personals'] ? $name : '',
                'status' => $casteller->getStatusName(),
                'position' => $casteller->getPosition() ? $casteller->getPosition()->getName() : '',
                'tags' => Humans::readCastellerColumn($casteller, 'tags', 'right'),
                'gender' => $params['user_can_view_casteller_personals'] ? Humans::readCastellerColumn($casteller, 'gender') : '',
                'id_casteller' => $casteller->getId(),
            ];
        }, $request, $castellersFilter, $params);
    }
}
