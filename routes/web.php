<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\BoardsController;
use App\Http\Controllers\CastellersController;
use App\Http\Controllers\CollaConfigController;
use App\Http\Controllers\EventBoardController;
use App\Http\Controllers\EventRondesController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\Notifications\NotificationsController;
use App\Http\Controllers\Notifications\RemindersController;
use App\Http\Controllers\Notifications\ScheduledNotificationsController;
use App\Http\Controllers\Notifications\RegisterNotificationsController;


Auth::routes(['register' => false]);

// NON-LOGUED USERS
Route::group(['middleware' => ['auth']], function() {
    Route::get('/', 'HomeController@index')->name('home');
});

// ALL LOGUED USERS -> BASIC PERMISSIONS
Route::group(['middleware' => ['role_or_permission:Super-Admin|dashboard|profile']], function()
{
    Route::get('dashboard/{event?}', 'HomeController@dashboard')->name('dashboard');
    //User profile
    Route::get('profile/user', 'ProfileController@getSetupUser')->name('profile.user');
    Route::post('profile/user', 'ProfileController@postUpdateUserProfile')->name('profile.user.update');
    Route::post('profile/user/update-password', 'ProfileController@postUpdateUserPassword')->name('profile.user.update-password');
});

// VIEW COLLA
Route::group(['middleware' => ['role_or_permission:Super-Admin|Colla-Admin|view colla|edit colla']], function()
{
    Route::get('profile/colla', [ProfileController::class, 'getSetupColla'])->name('profile.colla');
});

// EDIT COLLA
Route::group(['middleware' => ['role_or_permission:Super-Admin|Colla-Admin|edit colla']], function()
{
    //Colla profile
    Route::post('profile/colla/colla', 'ProfileController@postUpdateColla')->name('profile.colla.update');
    Route::post('profile/colla/edit-config', [CollaConfigController::class, 'postUpdateCollaConfig'])->name('profile.colla.update-colla-config');
    Route::post('profile/colla/set-status-ajax', [CollaConfigController::class, 'postSetStatusAjax'])->name('profile.colla.set-status-colla-config');

    Route::post('profile/colla/delete-user/{user}', 'ProfileController@postDestroyUser')->name('profile.colla.delete-user');
    Route::post('profile/colla/add-user', 'ProfileController@postAddUser')->name('profile.colla.add-user');
    Route::get('profile/colla/edit-user-modal/{user}', 'ProfileController@getEditUserModalAjax')->name('profile.colla.edit-user-modal');
    Route::get('profile/colla/add-user-modal', 'ProfileController@getAddUserModalAjax')->name('profile.colla.add-user-modal');
    Route::post('profile/colla/update/{user}', 'ProfileController@postUpdateUser')->name('profile.colla.update-user');

    // events periods
    Route::get('profile/colla/periods/add-period-modal', 'PeriodController@getAddPeriodModal')->name('profile.colla.periods.add-period-modal');
    Route::post('profile/colla/periods/add', 'PeriodController@postStorePeriod')->name('profile.colla.periods.add');
    Route::post('profile/colla/periods/destroy/{period}', 'PeriodController@postDestroyPeriod')->where('period', '[0-9]+')->name('profile.colla.periods.destroy');
    Route::get('profile/colla/periods/edit-period-modal/{period}', 'PeriodController@getEditPeriodModalAjax')->name('profile.colla.periods.edit-period-modal');
    Route::post('profile/colla/periods/update/{period}', 'PeriodController@postUpdatePeriod')->name('profile.colla.periods.update');


});

// MANAGE ALL COLLES AND USERS
Route::group(['middleware' => ['role_or_permission:Super-Admin']], function()
{
    Route::get('admin/colles', 'CollesController@getList')->name('admin.colles');
    Route::get('admin/colles/add-colla-modal', 'CollesController@getAddCollaModal')->name('admin.colles.add-colla-modal');
    Route::post('admin/colles/add', 'CollesController@postStoreColla')->name('admin.colles.add');
    Route::get('admin/colles/edit-colla-modal/{colla}', 'CollesController@getEditCollaModalAjax')->name('admin.colles.edit-colla-modal');
    Route::post('admin/colles/update/{colla}', 'CollesController@postUpdateColla')->name('admin.colles.update');
    Route::get('admin/users', 'UsersController@getList')->name('admin.users');

});

// VIEW CASTELLERS
Route::group(['role_or_permission:Super-Admin|Colla-Admin|view BBDD|edit BBDD'], function()
{
    Route::get('castellers/list', [CastellersController::class, 'getList'])->name('castellers.list');
    Route::post('castellers/list-ajax', [CastellersController::class, 'postListAjax'])->name('castellers.list-ajax');
    Route::get('castellers/edit/{casteller}', [CastellersController::class, 'getCardCasteller'])->where('casteller', '[0-9]+')->name('castellers.edit');
    Route::get('castellers/edit/card-edit-ajax/{casteller}', 'CastellersController@getCardEditCastellerAjax')->where('casteller', '[0-9]+')->name('castellers.edit.card-edit');

    Route::view('castellers/upload-form','fileupload');

    Route::post('castellers/upload-form/fileupload',[CastellersController::class,'postAddCastellerExcel'])->name('uploadcastellers');
    Route::get('castellers/export/', [CastellersController::class, 'castellersExportExcel'])->name('castellers.export');
    Route::get('castellers/exportods/', [CastellersController::class, 'castellersExportOds'])->name('castellers.exportods');

    //EVENTS card
    Route::get('castellers/edit/card-attendance-ajax/{casteller}', 'CastellersController@getCardAttendanceAjax')->where('casteller', '[0-9]+')->name('castellers.edit.card-attendance');
    Route::post('castellers/update/{casteller}', 'CastellersController@postUpdateCasteller')->where('casteller', '[0-9]+')->name('castellers.update');
    Route::post('castellers/edit/card-attendance-events/ajax/{casteller}/{time}', 'CastellersController@postCardAttendanceEventsAjax')->name('castellers.edit.card-attendance-events');

    // TAGS
    Route::get('castellers/tags', [TagsController::class, 'getListTags'])->name('castellers.tags');

});

// EDIT CASTELLERS OR CASTELLER PERSONALS
Route::group(['role_or_permission:Super-Admin|Colla-Admin|edit BBDD|edit casteller personals'], function()
{
    Route::post('castellers/update/{casteller}', 'CastellersController@postUpdateCasteller')->where('casteller', '[0-9]+')->name('castellers.update');
});

// EDIT CASTELLERS

Route::group(['role_or_permission:Super-Admin|Colla-Admin|edit BBDD'], function()
{
    Route::post('castellers/add', 'CastellersController@postAddCasteller')->name('castellers.add');
    Route::post('castellers/delete/{casteller}', 'CastellersController@postDestroyCasteller')->where('casteller', '[0-9]+')->name('castellers.destroy');

    //tags
    Route::get('castellers/tags/add-tag-modal/{type?}', 'TagsController@getAddTagModalAjax')->name('castellers.tags.add-modal');
    Route::post('castellers/tags/add', 'TagsController@postAddCastellerTag')->name('castellers.tags.add');
    Route::get('castellers/tags/edit-tag-modal/{tag}', 'TagsController@getEditTagsModalAjax')->where('tag', '[0-9]+')->name('castellers.tags.edit-tag-modal');
    Route::post('castellers/tags/update/{tag}', 'TagsController@postUpdateTag')->where('tag', '[0-9]+')->name('castellers.tags.update');
    Route::post('castellers/tags/destroy/{tag}', 'TagsController@postDestroyTag')->where('tag', '[0-9]+')->name('castellers.tags.destroy');
    Route::get('castellers/tags/toggle-group/{tag}/{group}', 'TagsController@getToggleGroupAjax')->where('tag', '[0-9]+')->name('castellers.tags.toggle-group');
    Route::post('castellers/position/add', 'TagsController@postAddPositionTag')->name('castellers.position.add');
});

// VIEW CASTELLER CONFIG
Route::group(['role_or_permission:Super-Admin|Colla-Admin|view casteller config|edit casteller config'], function()
{
    Route::get('castellers/config/list', 'CastellerConfigController@getList')->name('castellers.config.list');
    Route::post('castellers/config/list-ajax', 'CastellerConfigController@postListAjax')->name('castellers.config.list-ajax');
    Route::get('castellers/config/credentials-mail-modal/{casteller}', 'CastellerConfigController@getCredentialsMailModalAjax')->name('castellers.config.credentials-mail-modal');
    Route::get('castellers/config/send-credentials-mail/{casteller}', 'CastellerConfigController@sendCredentialsMail')->name('castellers.config.send-credentials-mail');
});

// EDIT CASTELLER CONFIG
Route::group(['role_or_permission:Super-Admin|Colla-Admin|edit casteller config'], function()
{
    Route::post('castellers/config/set-status-ajax', 'CastellerConfigController@postSetStatusAjax')->name('castellers.config.set-status');
});

// VIEW EVENTS
Route::group(['role_or_permission:Super-Admin|Colla-Admin|view events|edit events'], function()
{
    Route::get('events/list', [EventsController::class, 'getList'])->name('events.list');
    Route::post('events/list-ajax/{time}', [EventsController::class, 'postListAjax'])->name('events.list-ajax');
    Route::get('events/tags', 'TagsController@getListEvents')->name('events.tags');
    Route::get('events/answers', 'TagsController@getListAttendance')->name('events.answers');

    // Attendance
    Route::get('event/attendance/{event}', 'EventAttendanceController@getIndex')->where('event', '[0-9]+')->name('event.attendance');
    Route::get('event/attendance/list-attenders-csv/{event}', 'EventAttendanceController@listAttendersCsv')->where('event', '[0-9]+')->name('event.attendance.list-attenders-csv');
    Route::post('event/attendance/list-attenders-ajax/{event}', 'EventAttendanceController@postListAttendersAjax')->where('event', '[0-9]+')->name('event.attendance.list-attenders');
    Route::post('event/attendance/set-status-ajax', 'EventAttendanceController@postSetStatusAjax')->name('event.attendance.set-status');
    Route::post('event/attendance/set-status-verified-ajax', 'EventAttendanceController@postSetStatusVerifiedAjax')->name('event.attendance.set-status-verified');
    Route::post('event/attendance/set-answers-ajax', 'EventAttendanceController@postSetAnswersAjax')->name('event.attendance.set-answers');
    Route::post('event/attendance/set-companions-ajax', 'EventAttendanceController@postSetCompanionsAjax')->name('event.attendance.set-companions');
    Route::get('event/attendance/list-block/{event}', 'EventAttendanceController@getListBlocks')->where('event', '[0-9]+')->name('event.attendance.list-block');
    Route::post('event/attendance/notify-missing-ajax/{event}', 'EventAttendanceController@notifyMissingAjax')->where('event', '[0-9]+')->name('event.attendance.notify_missing');
    Route::get('event/{event}/attendance/verify', 'EventAttendanceController@getVerifyAttendance')->name('event.attendance.verify');
});

// EDIT EVENTS
Route::group(['role_or_permission:Super-Admin|Colla-Admin|edit events'], function()
{
    Route::get('events/create', 'EventsController@getCreate')->name('events.create');
    Route::get('events/create-group', 'EventsController@getCreateGroup')->name('events.create-group');
    Route::post('events/add', 'EventsController@postStoreEvent')->name('events.add');
    Route::post('events/add-group', 'EventsController@postStoreEventGroup')->name('events.add-group');
    Route::get('events/edit/{event}', 'EventsController@getEditEvent')->where('event', '[0-9]+')->name('events.edit');
    Route::post('events/update/{event}', 'EventsController@postUpdateEvent')->where('event', '[0-9]+')->name('events.update');
    Route::post('events/destroy/{event}', 'EventsController@postDestroyEvent')->where('event', '[0-9]+')->name('events.destroy');

    //events tags
    Route::post('events/tags/add', 'TagsController@postAddEventTag')->name('events.tags.add');
    Route::get('events/answers/add-answer-modal/{type}', 'TagsController@getAddTagModalAjax')->name('events.answers.add-modal');
    Route::post('events/answers/add', 'TagsController@postAddAttendanceTag')->name('events.answers.add');

});

// BOARDS [todo]

Route::group(['middleware' => ['role_or_permission:Super-Admin|Colla-Admin|view boards|edit boards']], function()
{


    // Boards
    Route::get('boards/list', [BoardsController::class, 'getList'])->name('boards.list');
    Route::get('boards/tags', [TagsController::class, 'getListBoards'])->name('boards.tags');
    Route::get('boards/preview-board-ajax/{board}', [BoardsController::class, 'getModalPreviewBoard'])->name('boards.preview-board-ajax');
    Route::post('boards/ajax-tag-baix-position/{board}', [BoardsController::class, 'postTagBaixPosition'])->where('board', '[0-9]+')->name('boards.tag-baix-position');
    Route::post('boards/ajax-tag-position/{board}/{map}', [BoardsController::class, 'postTagPosition'])->where('board', '[0-9]+')->name('boards.tag-position');
    Route::get('boards/biblio', [BoardsController::class, 'getBiblio'])->name('boards.biblio');

    //Event Boards
    Route::get('event/board/edit-board-event-modal/{boardEvent?}', [EventBoardController::class, 'getEditBoardEventModalAjax'])->where('boardEvent', '[0-9]+')->name('event.board.edit-board-event-modal');
    Route::get('event/board/{event}/{boardEvent?}', [EventBoardController::class, 'getBoard'])->name('event.board');
    Route::post('event/board/load-position/{boardEvent}', [EventBoardController::class, 'postLoadPositionsAjax'])->where('boardEvent', '[0-9]+')->name('event.board.load-positions-ajax');
    Route::post('event/board/remove-missing/{boardEvent}', [EventBoardController::class, 'postRemoveMissingAjax'])->where('boardEvent', '[0-9]+')->name('event.board.remove-missing-ajax');
    Route::post('event/board/empty-board/{boardEvent}', [EventBoardController::class, 'postEmptyBoardAjax'])->where('boardEvent', '[0-9]+')->name('event.board.empty-board-ajax');
    Route::post('event/board/attach/{event}', [EventBoardController::class, 'postAttachBoard'])->where('event', '[0-9]+')->name('event.board.attach');
    Route::post('event/board/import/{boardEvent}', [EventBoardController::class, 'postImportBoardEvent'])->where('boardEvent', '[0-9]+')->name('event.board.import-board-event');
    Route::post('event/board/edit/{boardEvent}', [EventBoardController::class, 'postEditBoardEventAjax'])->where('boardEvent', '[0-9]+')->name('event.board.edit-board-event');
    Route::post('event/board/put-casteller/{eventBoardId}', [EventBoardController::class, 'postPutCastellerAjax'])->where('eventBoardId', '[0-9]+')->name('event.board.put-casteller-ajax');
    Route::get('event/board/load-map/{boardEvent}/{base}', [EventBoardController::class, 'getLoadMapAjax'])->name('event.board.load-map-ajax');
    Route::get('event/board/load-base/{boardEvent}/{base}', [EventBoardController::class, 'getLoadBase'])->name('event.board.load-base');
    Route::post('event/board/swap-castellers/{eventBoardId}', [EventBoardController::class, 'postSwapCastellersAjax'])->where('eventBoardId', '[0-9]+')->name('event.board.swap-castellers');
    Route::get('event/board/casteller-info/{boardEvent}/{divId}/{base}', [EventBoardController::class, 'getCastellerInfoAjax'])->where('boardEvent', '[0-9]+')->where('$divId', '[0-9]+')->name('event.board.casteller-info');
    Route::post('event/board/empty-row-pinya/{boardEvent}', [EventBoardController::class, 'postAjaxEmptyRow'])->where('boardEvent', '[0-9]+')->name('event-board.empty-row-pinya');
    Route::post('event/board/to-display', [EventBoardController::class, 'postToDisplay'])->name('event.board.to-display');
    Route::post('event/board/add-favourite', [EventBoardController::class, 'postAddFavourite'])->name('event.board.add-favourite');
    Route::post('event/board/destroy/{boardEvent}', [EventBoardController::Class, 'postDestroyBoardEvent'])->where('boardEvent', '[0-9]+')->name('event.board.destroy');
    Route::get('event/rondes/{event}', [EventRondesController::class, 'getList'])->where('event', '[0-9]+')->name('event.rondes');
    Route::post('event/rondes/list-ajax/{event}', [EventRondesController::class, 'postListAjax'])->where('event', '[0-9]+')->name('event.rondes.list-ajax');
    Route::post('event/rondes/add-ronda-ajax/{event}', [EventRondesController::class, 'postAddRondaAjax'])->where('event', '[0-9]+')->name('event.rondes.add-ronda-ajax');
    Route::post('event/rondes/destroy-ronda-ajax/{ronda}', [EventRondesController::class, 'postDestroyRondaAjax'])->where('ronda', '[0-9]+')->name('event.rondes.destroy-ronda-ajax');
    Route::post('event/rondes/update-ronda-ajax/{ronda}', [EventRondesController::class, 'postUpdateRondaAjax'])->where('ronda', '[0-9]+')->name('event.rondes.update-ronda-ajax');


});

Route::group(['middleware' => ['role_or_permission:Super-Admin|Colla-Admin|edit boards']], function()
{
    Route::post('boards/tags/add', [TagsController::class, 'postAddBoardTag'])->name('boards.tags.add');
    Route::get('boards/add-board-modal', [BoardsController::class, 'getAddBoardModalAjax'])->name('boards.add-board-modal');
    Route::post('boards/add', [BoardsController::class, 'postAddBoard'])->name('boards.add');
    Route::post('boards/import', [BoardsController::class, 'postImportBoard'])->name('boards.import');
    Route::post('boards/import-translate', [BoardsController::class, 'postImportTranslateBoard'])->name('boards.import-translate');
    Route::post('boards/ajax-set-public-board', [BoardsController::class, 'postSetPublicBoard'])->name('boards.setPublicBoard');
    Route::post('boards/ajax-upload-svg/{board}', [BoardsController::class, 'postUploadSvg'])->where('board', '[0-9]+')->name('boards.upload-svg');
    Route::get('boards/tag-row-map/{board}/{map}', [BoardsController::class, 'getTagRowMap'])->name('boards.tag-row-map');
    Route::post('boards/ajax-delete-position/{board}/{map}', [BoardsController::class, 'postDeletePosition'])->where('board', '[0-9]+')->name('boards.delete-position');
    Route::get('boards/add-map/{board}/{map}', [BoardsController::class, 'getAddMap'])->where('board', '[0-9]+')->name('boards.add-map');
    Route::get('boards/tag-all-map/{board}/{map}', [BoardsController::class, 'getTagAllMap'])->where('board', '[0-9]+')->name('boards.tag-all-map');
    Route::get('boards/style/{board}/{map}', [BoardsController::class, 'getStyleMap'])->where('board', '[0-9]+')->name('boards.style-map');
    Route::get('boards/modal-finish-import/{boardId}/{base}', [BoardsController::class, 'getModalFinishImport'])->where('boardId', '[0-9]+')->name('boards.modal-finish-import');
    Route::post('boards/style/{board}/{map}', [BoardsController::class, 'postStyleMapAjax'])->where('board', '[0-9]+')->name('boards.style-map-ajax');
    Route::post('boards/destroy/{board}', [BoardsController::class, 'postDestroy'])->where('board', '[0-9]+')->name('boards.destroy');
    Route::post('boards/update/name/{board}', [BoardsController::class, 'postUpdateBoardName'])->where('board', '[0-9]+')->name('boards.update.name');
});

// VIEW NOTIFICATIONS
Route::group(['middleware' => ['role_or_permission:Super-Admin|Colla-Admin|view notifications|edit notifications']], function()
{
    Route::get('notifications/scheduled/list', [ScheduledNotificationsController::class, 'getList'])->name('notifications.scheduled_notifications.list');
    Route::post('notifications/scheduled/list-ajax', [ScheduledNotificationsController::class, 'postListAjax'])->name('notifications.scheduled_notifications.list-ajax');
    Route::get('notifications/register/list', [RegisterNotificationsController::class, 'getList'])->name('notifications.register.list');
    Route::post('notifications/register/list-ajax/{notification?}', [RegisterNotificationsController::class, 'postListAjax'])->name('notifications.register.list-ajax');
    Route::get('notifications/messages/list', [NotificationsController::class, 'getList'])->name('notifications.messages.list');
    Route::post('notifications/messages/list-ajax', [NotificationsController::class, 'postListAjax'])->name('notifications.messages.list-ajax');
    Route::get('notifications/reminders/list', [RemindersController::class, 'getList'])->name('notifications.reminders.list');
    Route::post('notifications/reminders/list-ajax', [RemindersController::class, 'postListAjax'])->name('notifications.reminders.list-ajax');
    Route::get('notifications/details/{notification}', [NotificationsController::class, 'getDetails'])->name('notifications.details');
    Route::get('notifications/scheduled/details/{scheduledNotification}', [ScheduledNotificationsController::class, 'getDetails'])->name('notifications.scheduled_notifications.details');
});

// EDIT NOTIFICATIONS
Route::group(['middleware' => ['role_or_permission:Super-Admin|Colla-Admin|edit notifications']], function()
{
    Route::get('notifications/scheduled/create', [ScheduledNotificationsController::class, 'getCreate'])->name('notifications.scheduled_notifications.create');
    Route::post('notifications/scheduled/add', [ScheduledNotificationsController::class, 'postStoreNotification'])->name('notifications.scheduled_notifications.add');
    Route::get('notifications/scheduled/edit/{scheduledNotification}', [ScheduledNotificationsController::class, 'getEditNotification'])->name('notifications.scheduled_notifications.edit');
    Route::post('notifications/scheduled/update/{scheduledNotification}', [ScheduledNotificationsController::class, 'postUpdateNotification'])->name('notifications.scheduled_notifications.update');
    Route::post('notifications/scheduled/destroy/{scheduledNotification}', [ScheduledNotificationsController::class, 'postDestroyNotification'])->name('notifications.scheduled_notifications.destroy');
});


Route::match(['get', 'post'], '/'.env('TELEGRAM_CALLBACK_PATH', '/telegram_callback'), 'BotManController@handle')->name('botman.handle');
Route::match(['get', 'post'], '/'.env('TELEGRAM_CALLBACK_PATH', '/telegram_callback').'/tinker', 'BotManController@tinker');
