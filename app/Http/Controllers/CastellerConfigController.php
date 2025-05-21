<?php

namespace App\Http\Controllers;

use App\Casteller;
use App\Colla;
use App\Enums\CastellersStatusEnum;
use App\Helpers\Humans;
use App\Helpers\RenderHelper;
use App\Mail\CastellerCredentials;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class CastellerConfigController extends Controller
{
    /** get list of CastellerConfig */
    public function getList(): View
    {

        $user = $this->user();

        if (! $user->can('view casteller config') && ! $user->can('edit casteller config')) {
            abort(404);
        }

        $data_content['tags'] = Tag::currentTags();
        $data_content['positions'] = Tag::currentTags('POSITIONS');
        $data_content['tags_groups'] = Tag::groups();
        $data_content['statuses'] = Casteller::getStatuses();

        return view('castellers.config.list', $data_content);
    }

    /** get Castellers filtered list via AJAX */
    public function postListAjax(Request $request): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('view casteller config') && ! $user->can('edit casteller config')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        $tags = $request->input('tags') ?? [Tag::TAG_ALL];
        $status = CastellersStatusEnum::getBy($request->input('status')) ?? [CastellersStatusEnum::ALL];
        $searchType = $request->input('filter_search_type'); //AND or OR
        $castellersFilter = Casteller::filter($colla)
            ->withStatus($status)
            ->withTags($tags, $searchType);

        $data = $castellersFilter->datatablesFilter($request, [
            'castellers.name', 'castellers.last_name', DB::raw('concat(castellers.name," ",castellers.last_name)'),
            'castellers.alias', 'castellers.email', 'castellers.email2',
        ]);

        foreach ($castellersFilter->eloquentBuilder()->with('tags')->with('colla')->get() as $casteller) {
            $array_casteller = [];

            $castellerConfig = $casteller->getCastellerConfig();

            if (! is_null($castellerConfig)) {
                $array_casteller['photo'] = '<img src="'.$casteller->getProfileImage('xs').'" class="img-avatar img-avatar32" alt="">';
                $array_casteller['name'] = $casteller->getDisplayName();
                $array_casteller['alias'] = $casteller->getAlias();
                $array_casteller['tags'] = Humans::readCastellerColumn($casteller, 'tags', 'right');
                $array_casteller['telegram_enabled'] = RenderHelper::fieldSwitcher($castellerConfig->getTelegramEnabled(), 'data-id_casteller', $casteller->getId(), 'telegram_enabled', 'telegram_enabled', 'telegram_enabled');
                $array_casteller['auth_token_enabled'] = RenderHelper::fieldSwitcher($castellerConfig->getAuthTokenEnabled(), 'data-id_casteller', $casteller->getId(), 'auth_token_enabled', 'auth_token_enabled', 'auth_token_enabled');
                $array_casteller['api_token_enabled'] = RenderHelper::fieldSwitcher($castellerConfig->getApiTokenEnabled(), 'data-id_casteller', $casteller->getId(), 'api_token_enabled', 'api_token_enabled', 'api_token_enabled');
                $array_casteller['tecnica'] = RenderHelper::fieldSwitcher($castellerConfig->getTecnica(), 'data-id_casteller', $casteller->getId(), 'tecnica', 'tecnica', 'tecnica');
                $array_casteller['last_access_at'] = Humans::howLong($castellerConfig->getLastAccessAt());
                $array_casteller['last_credentials_sent_at'] = Humans::howLong($castellerConfig->getCredentialsSentAt(), true);
                $array_casteller['id_casteller'] = $casteller->getId();
                $array_casteller['buttons'] = '<button class="btn btn-primary btn-mail" data-casteller_id="'.$casteller->getId().'"><i class="fa fa-mail-forward"></i></a>';

                array_push($data->data, $array_casteller);
            }
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /** set CastellerConfig status via AJAX
     * @return array|mixed
     *
     * @throws AuthorizationException
     */
    public function postSetStatusAjax(Request $request)
    {

        $user = $this->user();

        if (! $user->can('edit casteller config')) {
            abort(404);
        }

        $casteller = Casteller::find($request->id_casteller);
        $castellerConfig = $casteller->getCastellerConfig();

        $status = $request->status;
        $field = $request->fieldname;
        $castellerConfig->$field = $status;
        $castellerConfig->save();

        return $status;
    }

    /** get send credentials modal via AJAX
     * @return array|mixed
     *
     * @throws AuthorizationException
     */
    public function getCredentialsMailModalAjax(casteller $casteller, Request $request)
    {
        if (! Auth::user()->can('edit casteller config')) {
            abort(404);
        }

        if (! $casteller->castellerConfig->hasAnyAuthEnabled()) {
            return response()->json(['status' => 'ko', 'message' => trans('casteller.any_auth_enabled')], 400);
        }

        $data_content['email_view'] = (new CastellerCredentials($casteller))->render();
        $data_content['casteller_id'] = $casteller->getId();

        return view('castellers.modals.send-credentials-mail', $data_content);
    }

    public function sendCredentialsMail(casteller $casteller, Request $request)
    {
        if (! Auth::user()->can('edit casteller config')) {
            abort(404);
        }

        Mail::to($casteller->getEmail())->send(new CastellerCredentials($casteller));
        $castellerConfig = $casteller->getCastellerConfig();
        $castellerConfig->last_credentials_sent_at = Carbon::now()->toDateTime();
        $castellerConfig->save();

        return Response::HTTP_OK;
    }
}
