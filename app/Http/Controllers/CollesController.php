<?php

namespace App\Http\Controllers;

use App\Colla;
use App\Managers\CollesManager;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\ParameterBag;

class CollesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function getList()
    {

        $user = Auth::user();

        if (! $user->hasRole('Super-Admin')) {
            abort(404);
        }

        $colles = Colla::get();

        $data_content['colles'] = $colles;

        return view('admin.colles.list', $data_content);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function getAddCollaModal()
    {
        $user = Auth::user();

        if (! $user->hasRole('Super-Admin')) {
            abort(404);
        }

        return view('admin.colles.modal-add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function postStoreColla(CollesManager $collesManager, Request $request)
    {

        $user = Auth::user();

        if (! $user->hasRole('Super-Admin')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:50|min:3',
            'email' => 'required|email:rfc',
            'phone' => 'required|numeric',
            'country' => 'required|max:100|min:3',
            'city' => 'required|max:100|min:3',
            'shortname' => 'required|max:20|min:3|unique:colles,shortname',
            'logo' => 'sometimes|file|image|mimes:jpeg,png|max:10240',
            'max_members' => 'required|numeric',
            'banner' => 'sometimes|file|image|mimes:jpeg,png|max:10240',
        ]);

        $shortname = $request->input('shortname');

        $colles_shortname = Colla::query()->where('shortname', $shortname)->get();

        if (count($colles_shortname) > 0) {
            Session::flash('status_ko', trans('admin.error_same_shortname'));

            return redirect(route('admin.colles'));
        } else {

            $bag = new ParameterBag($request->except('_token'));
            $colla = $collesManager->createColla($bag);

            Session::flash('status_ok', trans('admin.colla_added'));

            return redirect(route('admin.colles'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Factory|View
     */
    public function getEditCollaModalAjax(colla $colla)
    {

        $user = Auth::user();

        if (! $user->hasRole('Super-Admin')) {
            abort(404);
        }

        $data_content['colla'] = $colla;

        return view('admin.colles.modal-add', $data_content);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function postUpdateColla(CollesManager $collesManager, Request $request, colla $colla)
    {
        $user = Auth::user();

        if (! $user->hasRole('Super-Admin')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:50|min:3',
            'email' => 'required|email:rfc',
            'phone' => 'required|numeric',
            'country' => 'required|max:100|min:3',
            'city' => 'required|max:100|min:3',
            'logo' => 'sometimes|file|image|mimes:jpeg,png|max:10240',
            'max_members' => 'required|numeric',
            'banner' => 'sometimes|file|image|mimes:jpeg,png|max:10240',
        ]);

        $bag = new ParameterBag($request->except('_token'));
        $collesManager->updateColla($colla, $bag);

        Session::flash('status_ok', trans('admin.colla_updated'));

        return redirect(route('admin.colles'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse|Redirector
     *
     * @throws \Exception
     */
    public function postDestroyColla(colla $colla)
    {
        // Aquesta funcio s'ha traslladat a console/commands/DeleteColla
    }
}
