<?php

namespace App\Http\Controllers;

use App\Colla;
use App\Helpers\RenderHelper;
use App\Managers\CollesManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\ParameterBag;

class CollaConfigController extends Controller
{
    /** update collaConfig form edit collaConfig profile*/
    public function postUpdateCollaConfig(CollesManager $manager, Request $request): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit colla')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $collaConfig = $colla->getConfig();

        $request->validate([
            'translation_activitat' => 'max:20',
            'translation_actuacio' => 'max:20',
            'translation_assaig' => 'max:20',
            'max_activitats' => 'numeric|max:50|min:1',
            'max_actuacions' => 'numeric|max:50|min:1',
            'max_assaigs' => 'numeric|max:50|min:1',
            'language' => 'required|size:2',
        ]);

        $bag = new ParameterBag([
            'translation_activitat' => $request->input('translation_activitat'),
            'translation_actuacio' => $request->input('translation_actuacio'),
            'translation_assaig' => $request->input('translation_assaig'),
            'height_baseline' => $request->input('height_baseline'),
            'shoulder_height_baseline' => $request->input('shoulder_height_baseline'),
            'max_activitats' => $request->input('max_activitats'),
            'max_actuacions' => $request->input('max_actuacions'),
            'max_assaigs' => $request->input('max_assaigs'),
            'language' => $request->input('language'),
            'member_edit_personal' => $request->input('member_edit_personal'),
            'google_calendar_activitats' => $request->input('google_calendar_activitats'),
            'google_calendar_actuacions' => $request->input('google_calendar_actuacions'),
            'google_calendar_assaigs' => $request->input('google_calendar_assaigs'),
            'totp_token_expiration' => $request->input('totp_token_expiration'),
        ]);

        $manager->updateCollaConfig($collaConfig, $bag);

        $data_content['active'] = RenderHelper::profileActiveTab('config');

        Session::flash('status_ok', trans('admin.colla_config_updated'));

        return redirect()->route('profile.colla', $data_content);
    }

    /** set CollaConfig status via AJAX*/
    public function postSetStatusAjax(CollesManager $manager, Request $request): JsonResponse
    {
        $user = $this->user();

        if (! $user->can('edit colla')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $collaConfig = $colla->getConfig();

        $bag = new ParameterBag([
            $request->input('fieldname') => (bool) $request->input('status'),
        ]);

        $collaConfig = $manager->updateCollaConfig($collaConfig, $bag);

        return new JsonResponse(['data' => $collaConfig->getPublicDisplayUrl()], Response::HTTP_OK);
    }
}
