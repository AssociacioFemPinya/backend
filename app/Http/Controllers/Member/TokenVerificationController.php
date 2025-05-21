<?php

namespace App\Http\Controllers\Member;

use App\Attendance;
use App\Colla;
use App\Enums\AttendanceStatus;
use App\Event;
use App\Http\Controllers\Controller;
use App\Services\TOTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenVerificationController extends Controller
{
    /**
     * Shows the form for verifying the TOTP token.
     *
     * @return \Illuminate\View\View
     */
    public function showVerifyTokenForm()
    {
        $casteller = Auth::user()->casteller;
        $tagsCasteller = $casteller->tagsArray('id_tag');

        $colla = Colla::getCurrent();

        $events = Event::filter($casteller->getColla())
            ->today()
            ->visible()
            ->withCastellerTags($tagsCasteller)
            ->get();

        return view('members.verify-token-form', compact('events', 'colla'));
    }

    /**
     * Verifies the TOTP token.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyToken(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id_event',
            'token' => 'required|string|size:6',
        ]);

        $eventId = $request->input('event_id');
        $token = $request->input('token');
        $event = Event::findOrFail($eventId);
        $casteller = Auth::user()->casteller;

        if (TOTPService::verifyCode($event, $token) && $event->getColla()->getId() == $casteller->getColla()->getId()) {
            Attendance::setStatusVerified($casteller->getId(), $eventId, AttendanceStatus::YES, 0);

            return redirect()->route('member.verify.token.form')
                ->with('status', __('tokentotp.success_verified', ['event' => $event->name]));
        } else {
            return redirect()->back()
                ->with('error', __('tokentotp.invalid_code'))
                ->withInput();
        }
    }
}
