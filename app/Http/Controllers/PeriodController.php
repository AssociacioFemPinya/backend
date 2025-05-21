<?php

namespace App\Http\Controllers;

use App\Colla;
use App\Helpers\RenderHelper;
use App\Managers\PeriodManager;
use App\Period;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\ParameterBag;

class PeriodController extends Controller
{
    public function getAddPeriodModal(): View
    {
        $user = $this->user();

        if (! $user->can('edit colla')) {
            abort(404);
        }

        return view('profile.periods.modals.modal-add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function postStorePeriod(PeriodManager $periodManager, Request $request)
    {
        $user = Auth::user();

        if (! $user->can('edit colla')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:50|min:3',
            'start_period' => 'required',
            'end_period' => 'required',
        ]);

        $startPeriod = str_replace('/', '-', $request->start_period);
        $startPeriod = date('Y-m-d', strtotime($startPeriod)).' 00:00:00';

        $endPeriod = str_replace('/', '-', $request->end_period);
        $endPeriod = date('Y-m-d', strtotime($endPeriod)).' 23:59:59';

        $colla = Colla::getCurrent();
        $bag = new ParameterBag($request->except('_token'));
        $bag->set('start_period', $startPeriod);
        $bag->set('end_period', $endPeriod);

        $period = $periodManager->createPeriod($colla, $bag);

        $data_content['active'] = RenderHelper::profileActiveTab('periods');

        Session::flash('status_ok', trans('period.period_added'));

        return redirect(route('profile.colla', $data_content));
    }

    /** post delete of Periods*/
    public function postDestroyPeriod(PeriodManager $periodManager, Period $period): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->can('edit colla')) {
            abort(404);
        }

        $periodManager->deletePeriod($period);

        $data_content['active'] = RenderHelper::profileActiveTab('periods');

        Session::flash('status_ok', trans('period.period_destroyed'));

        return redirect()->route('profile.colla', $data_content);
    }

    /** Show the form for editing the specified resource.*/
    public function getEditPeriodModalAjax(Period $period)
    {

        $user = Auth::user();
        $colla = Colla::getCurrent();

        if (! $user->can('edit colla')) {
            abort(404);
        }

        $data_content['period'] = $period;

        return view('profile.periods.modals.modal-add', $data_content);
    }

    /** Update Event
     */
    public function postUpdatePeriod(Period $period, PeriodManager $periodManager, Request $request): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit colla')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:100|min:3',
            'start_period' => 'required',
            'end_period' => 'required',
        ]);

        $startPeriod = str_replace('/', '-', $request->start_period);
        $startPeriod = date('Y-m-d', strtotime($startPeriod)).' 00:00:00';

        $endPeriod = str_replace('/', '-', $request->end_period);
        $endPeriod = date('Y-m-d', strtotime($endPeriod)).' 23:59:59';

        $bag = new ParameterBag();
        $bag->set('name', $request->name);
        $bag->set('start_period', $startPeriod);
        $bag->set('end_period', $endPeriod);

        $periodManager = $periodManager->updatePeriod($period, $bag);

        $data_content['active'] = RenderHelper::profileActiveTab('periods');

        Session::flash('status_ok', trans('period.period_update'));

        return redirect()->route('profile.colla', $data_content);
    }
}
