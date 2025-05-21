<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Attendance;
use App\Casteller;
use App\Colla;
use App\DataTables\Castellers as CastellersDataTable;
use App\Enums\CastellersStatusEnum;
use App\Enums\FilterSearchTypesEnum;
use App\Enums\Gender;
use App\Enums\TypeNationalId;
use App\Enums\TypeTags;
use App\Event;
use App\Exports\CastellersMultiSheetExport;
use App\Helpers\Humans;
use App\Imports\CastellersMultiSheetImport;
use App\Managers\CastellersManager;
use App\Period;
use App\Tag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;

class CastellersController extends Controller
{
    /** list of castellers */
    public function getList(): View
    {
        $user = $this->user();
        if (! $user->can('view BBDD') && ! $user->can('edit BBDD')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $datatable = new CastellersDataTable($user);

        $data_content['colla'] = $colla;
        $data_content['datatable'] = $datatable;
        $data_content['tags'] = Tag::currentTags();
        $data_content['positions'] = Tag::currentTags('POSITIONS');
        $data_content['statuses'] = Casteller::getStatuses();

        return view('castellers.list', $data_content);
    }

    /** get Castellers filtered list via AJAX */
    public function postListAjax(Request $request): JsonResponse
    {
        $user = $this->user();
        if (! $user->can('view BBDD') && ! $user->can('edit BBDD')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        $tags = $request->input('tags') ?? [Tag::TAG_ALL];
        $status = CastellersStatusEnum::getBy($request->input('status')) ?? [CastellersStatusEnum::ALL];
        $searchType = $request->input('filter_search_type') ?? FilterSearchTypesEnum::OR; //AND or OR
        $castellersFilter = Casteller::filter($colla)
            ->withStatus($status)
            ->withTags($tags, $searchType);

        $datatable = new CastellersDataTable($user);
        $data = $datatable->render($user, $request, $castellersFilter);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /** Store new casteller */
    public function postAddCasteller(CastellersManager $castellersManager, Request $request): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit BBDD')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        $request->validate([
            'num_soci' => 'nullable|max:20|min:1',
            'national_id_number' => 'nullable|max:50|min:7',
            'nationality' => 'nullable|max:50|min:3',
            'national_id_type' => 'nullable|max:8|min:3|in:'.implode(',', TypeNationalId::getTypes()),
            'gender' => 'nullable|digits_between:0,3|in:'.implode(',', Gender::getTypes()),
            'name' => 'nullable|max:150|min:1',
            'last_name' => 'nullable|max:150|min:1',
            'alias' => ['required', 'max:150', 'min:2',
                Rule::unique('castellers')
                    ->where('colla_id', $colla->getId())],
            'birthdate' => 'nullable|date_format:d/m/Y',
            'subscription_date' => 'nullable|date_format:d/m/Y',
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
            'height' => 'nullable|numeric|max:400|min:-400',
            'weight' => 'nullable|numeric|max:200|min:-200',
            'shoulder_height' => 'nullable|numeric|max:400|min:-400',
            'photo' => 'nullable|file|image|mimes:jpeg,png|max:10240',
            'tags' => 'nullable|array',
            'position' => 'nullable|numeric',
            'status' => 'required',
        ]);

        $bag = new ParameterBag($request->except('_token'));
        $casteller = $castellersManager->createCasteller($colla, $bag);

        Session::flash('status_ok', trans('casteller.casteller_added'));

        return redirect()->route('castellers.edit', $casteller->getId());
    }

    public function postAddCastellerExcel(CastellersManager $castellersManager, Request $request)
    {
        $user = $this->user();

        if (! $user->can('edit BBDD')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        Excel::import(new CastellersMultiSheetImport($castellersManager), $request->file('users'));

        Session::flash('status_ok', trans('casteller.casteller_added'));

        return redirect()->route('castellers.list');

    }

    /** get home card of casteller
     * @throws AuthorizationException
     */
    public function getCardCasteller(Casteller $casteller, Request $request): View
    {

        $user = $this->user();

        if (! $user->can('view BBDD') && ! $user->can('edit BBDD')) {
            abort(404);
        }

        $this->authorize('getCasteller', $casteller);
        $colla = Colla::getCurrent();

        $data_content['casteller'] = $casteller;
        $data_content['tags'] = $colla->getTags();
        $data_content['positions'] = $colla->getTags(TypeTags::POSITIONS);
        $data_content['tags_groups'] = Tag::groups();
        $data_content['families'] = $colla->getFamilies();
        $data_content['statuses'] = Casteller::getStatuses();
        $data_content['active_tab'] = ($request->get('active_tab')) ? $request->get('active_tab') : 'basic';

        return view('castellers.card', $data_content);
    }

    /** get edit card casteller via Ajax*/
    public function getCardEditCastellerAjax(Casteller $casteller, Request $request): View
    {

        $user = $this->user();

        if (! $user->can('edit BBDD')) {
            abort(404);
        }

        $this->authorize('getCasteller', $casteller);

        $data_content['casteller'] = $casteller;
        $data_content['tags'] = Tag::currentTags();
        $data_content['positions'] = Tag::currentTags(TypeTags::Positions()->value());
        $data_content['tags_groups'] = Tag::groups();
        $data_content['families'] = $casteller->getColla()->getFamilies();
        $data_content['statuses'] = Casteller::getStatuses();
        $data_content['active_tab'] = ($request->get('active_tab')) ? $request->get('active_tab') : 'basic';

        return view('castellers.ajax.card-edit', $data_content);
    }

    /** get attendance card casteller via AJAX*/
    public function getCardAttendanceAjax(Casteller $casteller): View
    {

        $user = $this->user();

        if (! $user->can('view BBDD') && ! $user->can('edit BBDD')) {
            abort(404);
        }

        $this->authorize('getCasteller', $casteller);

        $data_content['casteller'] = $casteller;
        $data_content['tags'] = Tag::currentTags('EVENTS');
        $colla = Colla::getCurrent();
        $data_content['periods'] = $colla->periods;
        $data_content['tags_event_type'] = Event::getTypes();

        return view('castellers.ajax.card-attendance', $data_content);
    }

    /** get $time = upcoming/past events via AJAX*/
    public function postCardAttendanceEventsAjax(Request $request, Casteller $casteller, string $time = 'upcoming'): JsonResponse
    {
        $user = $this->user();

        if (! $user->can('view BBDD') && ! $user->can('edit BBDD')) {
            abort(404);
        }
        $colla = Colla::getCurrent();

        $this->authorize('getCasteller', $casteller);

        //tags
        $tags = $request->input('tags', []);
        $tags_search_type = $request->input('filter_search_type'); //AND or OR
        $search_period = $request->input('search_period') ?? null;
        $tag_event_type = (int) $request->input('tags_event_type');

        $eventsFilter = Event::filter($colla);

        if ($tags_search_type == FilterSearchTypesEnum::AND) {
            $eventsFilter->withTags($tags, FilterSearchTypesEnum::AND);
        } elseif ($tags_search_type == FilterSearchTypesEnum::OR) {
            $eventsFilter->withTags($tags, FilterSearchTypesEnum::OR);
        }

        if ($time === 'upcoming') {
            $eventsFilter->upcoming();
        } elseif ($time === 'past') {
            $eventsFilter->past();
        }

        if ($search_period !== 0) {
            $period = Period::find($search_period);
        } else {
            $period = $colla->getCurrentPeriod();
        }

        $eventsFilter->withPeriod($period);

        if ($tag_event_type != 0) {
            $eventsFilter->withType($tag_event_type);
        }

        $data = $eventsFilter->datatablesFilter($request, ['events.name']);
        $attendances = Attendance::getAttendanceCasteller($casteller->getId());

        foreach ($eventsFilter->eloquentBuilder()->get() as $event) {
            $array_event = [];

            $attendance = $attendances->where('event_id', $event->getId())->first();

            $array_event['name'] = $event->name.' <span class="text-success" style="font-size: 11px;"><i class="fa-solid fa-check"></i>'.$event->countAttenders()['ok'].'</span>
                                                    <span class="text-danger" style="font-size: 11px;"><i class="fa-solid fa-close"></i>'.$event->countAttenders()['nok'].'</span>
                                                    <span class="text-warning" style="font-size: 11px;"><i class="fa-solid fa-question"></i>'.$event->countAttenders()['unknown'].'</span>';
            $array_event['type'] = $event->getTypeName();
            $array_event['tags'] = Humans::readEventColumn($event, 'tags', 'right');
            $array_event['start_date'] = Humans::parseDate($event->getStartDate(), true);
            $array_event['status'] = Humans::readAttendanceStatus($event, $attendance);
            $array_event['status_verified'] = Humans::readAttendanceStatusVerified($event, $attendance);

            $data->data[] = $array_event;
        }

        return new JsonResponse($data, Response::HTTP_OK);

    }

    /** Update casteller*/
    public function postUpdateCasteller(CastellersManager $castellersManager, Request $request, Casteller $casteller): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit BBDD')) {
            abort(404);
        }

        $this->authorize('getCasteller', $casteller);

        $data_content['active_tab'] = $request->input('active_tab');
        $data_content['casteller'] = $casteller;

        // Depending on the privileges we want to validate only some fields
        // Also we want to be sure only the desired fields are accepted on the form request

        $colla = Colla::getCurrent();

        if (! $user->can('edit casteller personals')) {
            $request->validate([
                'alias' => ['required', 'max:150', 'min:2',
                    Rule::unique('castellers')
                        ->where('colla_id', $colla->getId())
                        ->ignore($casteller->getId(), 'id_casteller')],
                'tags' => 'nullable|array',
                'position' => 'nullable|numeric',
            ]);

            $attributes = $request->only(['alias', 'position', 'tags']);
        } elseif (! $user->can('edit BBDD')) {

            // we add the required field as it is not set on the form
            if (! $request->has('alias')) {
                $request->merge(['alias' => $casteller->getAlias()]);
            }

            $request->validate([
                'num_soci' => 'nullable|max:20|min:1',
                'national_id_number' => 'nullable|max:50|min:1',
                'nationality' => 'nullable|max:50|min:3',
                'national_id_type' => 'nullable|max:8|min:3|in:'.implode(',', TypeNationalId::getTypes()),
                'gender' => 'nullable|digits_between:0,3|in:'.implode(',', Gender::getTypes()),
                'name' => 'nullable|max:150|min:1',
                'last_name' => 'nullable|max:150|min:1',
                'alias' => ['required', 'max:150', 'min:2',
                    Rule::unique('castellers')
                        ->where('colla_id', $colla->getId())
                        ->ignore($casteller->getId(), 'id_casteller')],
                'birthdate' => 'nullable|date_format:d/m/Y',
                'subscription_date' => 'nullable|date_format:d/m/Y',
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
                'height' => 'nullable|numeric|max:400|min:-400',
                'weight' => 'nullable|numeric|max:200|min:-200',
                'shoulder_height' => 'nullable|numeric|max:400|min:-400',
                'photo' => 'nullable|file|image|mimes:jpeg,png|max:10240',
                'status' => 'required',
            ]);

            $attributes = $request->except(['alias', 'position', 'tags', '_token']);
        } else {
            $request->validate([
                'num_soci' => 'nullable|max:20|min:1',
                'national_id_number' => 'nullable|max:50|min:1',
                'nationality' => 'nullable|max:50|min:3',
                'national_id_type' => 'nullable|max:8|min:3|in:'.implode(',', TypeNationalId::getTypes()),
                'gender' => 'nullable|digits_between:0,3|in:'.implode(',', Gender::getTypes()),
                'name' => 'nullable|max:150|min:1',
                'last_name' => 'nullable|max:150|min:1',
                'alias' => ['required', 'max:150', 'min:2',
                    Rule::unique('castellers')
                        ->where('colla_id', $colla->getId())
                        ->ignore($casteller->getId(), 'id_casteller')],
                'birthdate' => 'nullable|date_format:d/m/Y',
                'subscription_date' => 'nullable|date_format:d/m/Y',
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
                'height' => 'nullable|numeric|max:400|min:-400',
                'weight' => 'nullable|numeric|max:200|min:-200',
                'shoulder_height' => 'nullable|numeric|max:400|min:-400',
                'photo' => 'nullable|file|image|mimes:jpeg,png|max:10240',
                'tags' => 'nullable|array',
                'position' => 'nullable|numeric',
                'status' => 'required',
            ]);
            $attributes = $request->except(['_token', 'active_tab', 'nationality']);
        }

        $bag = new ParameterBag($attributes);
        $castellersManager->updateCasteller($casteller, $bag);

        Session::flash('status_ok', trans('casteller.casteller_updated'));

        return redirect()->route('castellers.edit', $data_content);
    }

    /** Remove casteller */
    public function postDestroyCasteller(Casteller $casteller): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit BBDD')) {
            abort(404);
        }

        $this->authorize('getCasteller', $casteller);

        $colla = Colla::getCurrent();

        //if photo, delete photos
        if ($casteller->getPhoto()) {
            unlink(public_path('media/colles/'.$colla->getShortName().'/castellers').'/'.$casteller->getPhoto().'-xs.png');
            unlink(public_path('media/colles/'.$colla->getShortName().'/castellers').'/'.$casteller->getPhoto().'-med.png');
            unlink(public_path('media/colles/'.$colla->getShortName().'/castellers').'/'.$casteller->getPhoto().'-xl.png');
        }

        $casteller->delete();

        Session::flash('status_ok', trans('casteller.casteller_destroyed'));

        return redirect()->route('castellers.list');
    }

    public function castellersExportExcel()
    {
        $colla_id = Colla::getCurrent()->getId();
        $date = now()->format('Y-m-d');

        return Excel::download(new CastellersMultiSheetExport($colla_id), "castellers_{$date}.xlsx", \Maatwebsite\Excel\Excel::XLSX);
    }

    public function castellersExportOds()
    {
        $colla_id = Colla::getCurrent()->getId();
        $date = now()->format('Y-m-d');

        return Excel::download(new CastellersMultiSheetExport($colla_id), "castellers_{$date}.ods", \Maatwebsite\Excel\Excel::ODS);
    }
}
