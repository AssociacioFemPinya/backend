<?php

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Member\CalendarController;
use App\Http\Controllers\Member\PinyesController;
use App\Http\Controllers\Member\NotificationsController;
use App\Http\Controllers\Member\ProfileController;
use App\Http\Controllers\Member\TokenVerificationController;

Route::middleware('auth:token_auth')->get('/login', 'Auth\TokenAuthController@login');

Route::group(['middleware' => ['auth:member']], function() {
    Route::get('/', function () {
        return redirect()->route('member.calendar');
    });

    # Calendar
    Route::get('/calendar', [CalendarController::class, 'getCalendar'])->name('member.calendar');
    Route::post('/members/get/attendance', [CalendarController::class, 'getEventAttendanceAjax'])->name('member.get.event-attendance');
    Route::get('/members/get/event/{event}', [CalendarController::class, 'getEventInfoModalAjax'])->name('member.get.event-info-modal');
    Route::post('/members/edit/attendanceStatus', [CalendarController::class, 'setEventAttendanceStatusAjax'])->name('member.edit.event-attendance-status');
    Route::post('/members/edit/set-answers-ajax', [CalendarController::class, 'postSetAnswersAjax'])->name('member.edit.event-set-answers');
    Route::post('/members/edit/set-companions-ajax', [CalendarController::class, 'postSetCompanionsAjax'])->name('member.edit.event-set-companions');

    # Pinyes
    Route::get('/pinyes', [PinyesController::class, 'getPinyes'])->name('member.pinyes');
    Route::get('/rondes', [PinyesController::class, 'getRondes'])->name('member.rondes');

    # Notifications
    Route::get('notifications/list', [NotificationsController::class, 'getList'])->name('member.notifications.list');
    Route::post('notifications/list-ajax', [NotificationsController::class, 'postListAjax'])->name('member.notifications.list-ajax');
    Route::get('/notifications/get/{notification}', [NotificationsController::class, 'getNotificationInfoModalAjax'])->name('member.get.notification-info-modal');

    # Profile
    Route::get('/profile', [ProfileController::class, 'getProfile'])->name('member.profile');
    Route::post('update/{casteller}', [ProfileController:: class, 'postUpdateCasteller'])->where('casteller', '[0-9]+')->name('member.update');

    # Token Verification
    Route::get('/verify-token-form', [TokenVerificationController::class, 'showVerifyTokenForm'])->name('member.verify.token.form');
    Route::post('/verify-token', [TokenVerificationController::class, 'verifyToken'])->name('member.verify.token');

    Route::post('/logout', function () {
        Auth::guard('member')->logout();
        return redirect('/');
    })->name('member.logout');
});
