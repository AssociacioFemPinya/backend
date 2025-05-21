<?php

namespace App\Http\Controllers\Member;

use App\Colla;
use App\Event;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PinyesController extends Controller
{
    /** get member's pinyes */
    public function getPinyes(Request $request)
    {
        $casteller = Auth::user()->casteller;
        $url = Colla::getCurrent()->getConfig()->getPublicDisplayUrl($casteller->getId());

        $colla = Colla::getCurrent();

        return view('members.pinyes', compact('url', 'colla'));

    }

    public function getRondes(Request $request)
    {
        $castellerId = Auth::user()->casteller->getId();
        $colla = Colla::getCurrent();

        $event = Event::filter($colla)->liveOrUpcoming()->visible()->eloquentBuilder()
            ->with('rondes')
            ->orderBy('start_date', 'asc')
            ->first();

        $rondes = (is_null($event)) ? null : $event->rondes->sortBy('ronda');

        return view('members.rondes', compact('colla', 'event', 'rondes', 'castellerId'));
    }
}
