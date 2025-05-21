<?php

namespace App\Http\Controllers;

use App\Colla;
use App\Enums\TypeTags;
use App\Managers\TagsManager;
use App\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\ParameterBag;

final class TagsController extends Controller
{
    /**List Tags type=TypeTags::CASTELLERS.*/
    public function getListTags(): View
    {

        $user = $this->user();

        if (! $user->can('view BBDD') && ! $user->can('edit BBDD')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $data_content['colla'] = $colla;
        $data_content['tags'] = $colla->getTags();
        $data_content['tags_groups'] = Tag::groups();
        if ($user->getColla()->getConfig()->getBoardsEnabled()) {
            $data_content['positions'] = $colla->getTags(TypeTags::POSITIONS);
        }
        $data_content['type'] = TypeTags::CASTELLERS;

        return view('tags.list', $data_content);
    }

    /**List Tags type=TypeTags::EVENTS.*/
    public function getListEvents(): View
    {

        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $data_content['colla'] = $colla;
        $data_content['tags'] = $colla->getTags(TypeTags::EVENTS);
        $data_content['type'] = TypeTags::EVENTS;

        return view('tags.list', $data_content);
    }

    /** List Tags (bases) type=TypeTags::BOARDS */
    public function getListBoards(): View
    {

        $user = $this->user();

        if (! $user->can('view boards') && ! $user->can('edit boards')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $data_content['colla'] = $colla;
        $data_content['tags'] = $colla->getTags(TypeTags::BOARDS);
        $data_content['type'] = TypeTags::BOARDS;

        return view('tags.list', $data_content);
    }

    /** List Tags type=TypeTags::ATTENDANCE */
    public function getListAttendance(): View
    {

        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        $data_content['colla'] = $colla;
        $data_content['tags'] = $colla->getTags(TypeTags::ATTENDANCE);
        $data_content['type'] = TypeTags::ATTENDANCE;

        return view('tags.list', $data_content);
    }

    /**get Modal add Tag via AJAX*/
    public function getAddTagModalAjax($type = TypeTags::CASTELLERS): View
    {

        $user = $this->user();

        if (! $user->can('edit events') && ! $user->can('edit BBDD')) {
            abort(404);
        }

        if ($type === TypeTags::CASTELLERS) {
            $data_content['tags_groups'] = Tag::groups();
        }
        if ($type === TypeTags::POSITIONS) {
            $data_content['tags_groups'] = Tag::groups(TypeTags::POSITIONS);
        }

        $data_content['type'] = $type;

        return view('tags.modal-add', $data_content);
    }

    /** toggle Tag group via AJAX*/
    public function getToggleGroupAjax(TagsManager $tagsManager, Tag $tag, $group): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('edit BBDD')) {
            abort(404);
        }

        $this->authorize('getTag', $tag);

        $tagsManager->updateTag($tag, new ParameterBag(['group' => $group]));

        return new JsonResponse(true, Response::HTTP_OK);
    }

    /** Add Casteller Tag*/
    public function postAddCastellerTag(TagsManager $tagsManager, Request $request): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit BBDD')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:150|min:2',
        ]);

        $colla = Colla::getCurrent();

        //Check vàlid name
        if (! Tag::validName($request->input('name'))) {

            Session::flash('status_ko', trans('tag.invalid_name'));

            return redirect()->to(route('castellers.tags'));
        }

        //Check if exist
        $value = Str::slug($request->input('name'));
        $tags_same = Tag::query()
            ->where('colla_id', $colla->getId())
            ->where('type', TypeTags::CASTELLERS)
            ->where('value', $value)
            ->count();

        if ($tags_same >= 1) {

            Session::flash('status_ko', trans('tag.tag_exist'));

            return redirect()->to(route('castellers.tags'));
        }

        $bag = new ParameterBag($request->except('_token'));
        $bag->set('type', TypeTags::CASTELLERS);
        $bag->set('value', $value);
        $tagsManager->createTag($colla, $bag);

        Session::flash('status_ok', trans('tag.tag_added'));

        return redirect()->to(route('castellers.tags'));
    }

    public function postAddPositionTag(TagsManager $tagsManager, Request $request): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit BBDD')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:150|min:2',
        ]);

        $colla = Colla::getCurrent();

        //Check vàlid name
        if (! Tag::validName($request->input('name'))) {

            Session::flash('status_ko', trans('tag.invalid_name'));

            return redirect()->to(route('castellers.tags'));
        }

        //Check if exist
        $value = Str::slug($request->input('name'));
        $tags_same = Tag::query()
            ->where('colla_id', $colla->getId())
            ->where('type', TypeTags::POSITIONS)
            ->where('value', $value)
            ->count();

        if ($tags_same >= 1) {

            Session::flash('status_ko', trans('tag.tag_exist'));

            return redirect()->to(route('castellers.tags'));
        }

        $bag = new ParameterBag($request->except('_token'));
        $bag->set('type', TypeTags::POSITIONS);
        $bag->set('value', $value);
        $tagsManager->createTag($colla, $bag);

        Session::flash('status_ok', trans('tag.tag_added'));

        return redirect()->to(route('castellers.tags'));
    }

    public function postAddBoardTag(TagsManager $tagsManager, Request $request): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:150|min:2',
        ]);

        $colla = Colla::getCurrent();

        //Check vàlid name
        if (! Tag::validName($request->input('name'))) {

            Session::flash('status_ko', trans('tag.invalid_name'));

            return redirect(route('castellers.tags'));
        }

        //Check if exist
        $value = Str::slug($request->input('name'));
        $tags_same = Tag::query()
            ->where('colla_id', $colla->getId())
            ->where('type', TypeTags::BOARDS)
            ->where('value', $value)
            ->count();

        if ($tags_same >= 1) {

            Session::flash('status_ko', trans('tag.tag_exist'));

            return redirect()->route('castellers.tags');
        }

        $bag = new ParameterBag($request->except('_token'));
        $bag->set('type', TypeTags::BOARDS);
        $bag->set('value', $value);
        $tagsManager->createTag($colla, $bag);

        Session::flash('status_ok', trans('boards.base_added'));

        return redirect()->to(route('boards.tags'));
    }

    /** Add Event Tag*/
    public function postAddEventTag(TagsManager $tagsManager, Request $request): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:150|min:2',
        ]);

        $colla = Colla::getCurrent();

        //Check vàlid name
        if (! Tag::validName($request->input('name'))) {

            Session::flash('status_ko', trans('tag.invalid_name'));

            return redirect()->to(route('events.tags'));
        }

        //Check if exist
        $value = Str::slug($request->input('name'));
        $tags_same = Tag::query()
            ->where('colla_id', $colla->getId())
            ->where('type', TypeTags::EVENTS)
            ->where('value', $value)
            ->count();

        if ($tags_same >= 1) {

            Session::flash('status_ko', trans('tag.tag_exist'));

            return redirect()->to(route('events.tags'));
        }

        $bag = new ParameterBag($request->except('_token'));
        $bag->set('type', TypeTags::EVENTS);
        $bag->set('value', $value);
        $tagsManager->createTag($colla, $bag);

        Session::flash('status_ok', trans('tag.tag_added'));

        return redirect()->to(route('events.tags'));
    }

    /** ADD Atendance Tag*/
    public function postAddAttendanceTag(TagsManager $tagsManager, Request $request): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:150|min:2',
        ]);

        $colla = Colla::getCurrent();

        //Check vàlid name
        if (! Tag::validNameAttendance($request->input('name'))) {

            Session::flash('status_ko', trans('tag.invalid_name'));

            return redirect()->to(route('events.answers'));
        }

        //Check if exist
        $value = Str::slug($request->input('name'));
        $tags_same = Tag::query()
            ->where('colla_id', $colla->getId())
            ->where('type', TypeTags::ATTENDANCE)
            ->where('value', $value)
            ->count();

        if ($tags_same >= 1) {

            Session::flash('status_ko', trans('tag.tag_exist'));

            return redirect()->to(route('events.answers'));
        }

        $bag = new ParameterBag($request->except('_token'));
        $bag->set('type', TypeTags::ATTENDANCE);
        $bag->set('value', $value);
        $tagsManager->createTag($colla, $bag);

        Session::flash('status_ok', trans('tag.tag_added'));

        return redirect()->to(route('events.answers'));
    }

    public function getEditTagsModalAjax(Tag $tag): View
    {

        $user = $this->user();

        if (! $user->can('edit BBDD')) {
            abort(404);
        }

        if ($tag->getType() === TypeTags::CASTELLERS) {

            $data_content['tags_groups'] = Tag::groups();
        }

        $data_content['type'] = $tag->getType();
        $data_content['tag'] = $tag;

        return view('tags.modal-add', $data_content);
    }

    public function postUpdateTag(TagsManager $tagsManager, Request $request, Tag $tag): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit events') && ! $user->can('edit BBDD') && ! $user->can('edit boards')) {
            abort(404);
        }

        $this->authorize('getTag', $tag);

        $request->validate([
            'name' => 'required|max:150|min:2',
        ]);

        //Check valid name
        if ($tag->getType() === TypeTags::EVENTS || $tag->getType() === TypeTags::CASTELLERS || $tag->getType() === TypeTags::POSITIONS) {

            if (! Tag::validName($request->input('name'))) {

                Session::flash('status_ko', trans('tag.invalid_name'));

                if ($tag->getType() === TypeTags::CASTELLERS) {

                    return redirect()->to(route('castellers.tags'));
                } elseif ($tag->getType() === TypeTags::EVENTS) {

                    return redirect()->to(route('events.tags'));
                }
            }
        } elseif ($tag->getType() === TypeTags::ATTENDANCE) {
            if (! Tag::validNameAttendance($request->input('name'))) {

                return redirect()->to(route('events.answers'));
            }
        } elseif ($tag->getType() === TypeTags::BOARDS) {
            if (! Tag::validName($request->input('name'))) {

                return redirect()->to(route('boards.tags'));
            }
        }

        $value = Str::slug($request->input('name'));

        $bag = new ParameterBag($request->except('_token'));
        $bag->set('value', $value);
        $tagsManager->updateTag($tag, $bag);

        Session::flash('status_ok', trans('tag.tag_updated'));

        if ($tag->getType() === TypeTags::CASTELLERS) {

            return redirect(route('castellers.tags'));
        } elseif ($tag->getType() === TypeTags::EVENTS) {

            return redirect()->to(route('events.tags'));
        } elseif ($tag->getType() === TypeTags::ATTENDANCE) {

            return redirect()->to(route('events.answers'));
        }

        return redirect()->to(route('boards.tags'));
    }

    public function postDestroyTag(TagsManager $tagsManager, Tag $tag): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit events')) {
            abort(404);
        }

        $type = $tag->getType();

        $this->authorize('getTag', $tag);

        $tagsManager->deleteTag($tag);

        if ($type === TypeTags::POSITIONS) {
            Session::flash('status_ok', trans('tag.position_deleted'));
        } else {
            Session::flash('status_ok', trans('tag.tag_deleted'));
        }

        if ($type === TypeTags::CASTELLERS || $type === TypeTags::POSITIONS) {

            return redirect()->to(route('castellers.tags'));
        } elseif ($type === TypeTags::EVENTS) {

            return redirect()->to(route('events.tags'));
        } elseif ($type == TypeTags::ATTENDANCE) {

            return redirect()->to(route('events.answers'));
        }

        return redirect()->to(route('boards.tags'));
    }
}
