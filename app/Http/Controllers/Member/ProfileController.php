<?php

namespace App\Http\Controllers\Member;

use App\Casteller;
use App\Colla;
use App\Enums\Gender;
use App\Enums\TypeNationalId;
use App\Http\Controllers\Controller;
use App\Managers\CastellersManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\ParameterBag;

class ProfileController extends Controller
{
    /** get member's profile */
    public function getProfile(Request $request)
    {
        $casteller = Auth::user()->casteller;

        $colla = Colla::getCurrent();
        $collaConfig = Colla::getCurrent()->getConfig();

        return view('members.profile', compact('casteller', 'colla', 'collaConfig'));

    }

    /** Update member/casteller */
    public function postUpdateCasteller(CastellersManager $castellersManager, Request $request, Casteller $casteller): RedirectResponse
    {

        $data_content['casteller'] = $casteller;
        $colla = Colla::getCurrent();

        // From the casteller's profile it can only be edited if the gang has getMemberEditPersonalData() = true
        $collaConfig = Colla::getCurrent()->getConfig()->getMemberEditPersonalData();
        if (! $collaConfig) {
            abort(401);
        }

        $attributes = $request->except(['_token', 'subscription_date', 'num_soci']);

        // From the casteller profile we want to validate only some fields
        // Also we want to be sure only the desired fields are accepted on the form request
        $casteller = Auth::user()->casteller;

        $request->merge(['alias' => $casteller->getAlias()]);
        $request->validate([
            'num_soci' => 'nullable|max:20|min:1',
            'national_id_number' => 'nullable|max:50|min:1',
            'nationality' => 'nullable|max:50|min:3',
            'national_id_type' => 'nullable|max:8|min:3|in:'.implode(',', TypeNationalId::getTypes()),
            'gender' => 'nullable|digits_between:0,3|in:'.implode(',', Gender::getTypes()),
            'name' => 'nullable|max:150|min:1',
            'last_name' => 'nullable|max:150|min:1',
            'birthdate' => 'nullable|date_format:d/m/Y',
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
            'photo' => 'nullable|file|image|mimes:jpeg,png|max:10240',
        ]);

        $bag = new ParameterBag($attributes);

        $castellersManager->updateCasteller($casteller, $bag);
        Session::flash('status_ok', trans('casteller.casteller_updated'));

        return redirect()->route('member.profile');

    }
}
