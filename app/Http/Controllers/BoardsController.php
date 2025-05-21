<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Board;
use App\Colla;
use App\Enums\BasesEnum;
use App\Enums\TypeTags;
use App\Managers\BoardManager;
use App\Row;
use App\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

final class BoardsController extends Controller
{
    /** get Boards list */
    public function getList(): View
    {

        $user = $this->user();

        if (! $user->can('view boards')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        $data_content['bases'] = $colla->getTags(TypeTags::BOARDS);
        $data_content['posicions'] = $colla->getTags(TypeTags::POSITIONS);
        $data_content['boards'] = $colla->getBoards();
        $data_content['public_boards'] = Board::where('is_public', 1)->get();

        return view('boards.list', $data_content);
    }

    private function getPositions(array $structure): array
    {
        $structure_bases = [];
        $structure_positions = [];
        foreach ($structure as $template_rengla_index => $template_rengla_value) {
            foreach ($template_rengla_value as $template_position_index => $template_position_value) {
                array_push($structure_positions, $template_position_index);
            }

            array_push($structure_bases, $template_rengla_index);
        }

        return ['bases' => array_unique($structure_bases), 'positions' => array_unique($structure_positions)];
    }

    /** get Boards Biblio */
    public function getBiblio(): View
    {

        $user = $this->user();

        if (! $user->can('view boards')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        $data_content['bases'] = $colla->getTags(TypeTags::BOARDS);
        $data_content['colla_tags'] = $colla->getTags(TypeTags::POSITIONS);
        $data_content['public_boards'] = Board::where('is_public', 1)->get();
        $templates = Board::where('is_public', 1)->orderBy('type', 'asc')->get();
        $data_content['boards'] = $templates;
        $data_content['posicions'] = [];

        foreach ($templates as $template) {

            $structure_bases = [];
            $structure_positions = [];

            $id_board = $template->id_board;

            if (isset($template->data['pinya']) && isset($template->data['pinya']['structure'])) {
                $pinya_structure = $this->getPositions($template->data['pinya']['structure']);
                $structure_positions = array_merge($structure_positions, $pinya_structure['positions']);
                $structure_bases = array_merge($structure_bases, $pinya_structure['bases']);

            }
            if (isset($template->data['folre']) && isset($template->data['folre']['structure'])) {

                $folre_structure = $this->getPositions($template->data['folre']['structure']);
                $structure_positions = array_merge($structure_positions, $folre_structure['positions']);
                $structure_bases = array_merge($structure_bases, $folre_structure['bases']);

            }
            if (isset($template->data['manilles']) && isset($template->data['manilles']['structure'])) {

                $manilles_structure = $this->getPositions($template->data['manilles']['structure']);
                $structure_positions = array_merge($structure_positions, $manilles_structure['positions']);
                $structure_bases = array_merge($structure_bases, $manilles_structure['bases']);

            }
            if (isset($template->data['puntals']) && isset($template->data['puntals']['structure'])) {

                $puntals_structure = $this->getPositions($template->data['puntals']['structure']);
                $structure_positions = array_merge($structure_positions, $puntals_structure['positions']);
                $structure_bases = array_merge($structure_bases, $puntals_structure['bases']);

            }

            asort($structure_positions);
            asort($structure_bases);

            $data_content['posicions'][$id_board]['posicions'] = array_unique($structure_positions);
            $data_content['posicions'][$id_board]['rengla'] = array_unique($structure_bases);
        }

        return view('boards.biblio', $data_content);
    }

    /** get Modal Add Board */
    public function getAddBoardModalAjax(): View
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }
        $colla = Colla::getCurrent();

        $data_content['bases'] = $colla->getTags(TypeTags::BOARDS);

        return view('boards.modal-add', $data_content);
    }

    /**Import existing board */
    public function postImportBoard(Request $request): View//RedirectResponse
    {
        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }
        $request->validate([
            'pinya_id' => 'required|numeric',
            'base_id' => 'required|numeric',
        ]);

        $example_pinya_id = (int) $request->input('pinya_id');
        $example_board = Board::find($example_pinya_id);
        if (! $example_board->is_public) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        $new_board = $example_board->replicate();
        $new_board->colla_id = $colla->getId();
        $new_board->save();

        $example_rows = Row::where('board_id', $example_pinya_id)->get();
        foreach ($example_rows as $row) {
            $new_row = $row->replicate();
            $new_row->board_id = $new_board->getId();
            $new_row->save();
        }

        //this should be done when a base is provided
        DB::table('board_tags')->insert(['tag_id' => (int) $request->input('base_id'), 'board_id' => $new_board->getId()]);

        File::copy(public_path('media/colles/'.$example_board->colla->shortname.'/svg/'.$example_pinya_id.'_'.$example_board->type.'.svg'),
            public_path('media/colles/'.$colla->shortname.'/svg/'.$new_board->id_board.'_'.$new_board->type.'.svg'));

        $data_content['new_board'] = $new_board;
        $data_content['board_positions'] = Row::where('board_id', $example_pinya_id)->get();
        $data_content['colla_tags'] = Tag::where('colla_id', $colla->getId())->where('type', 'POSITIONS')->get();

        return view('boards.import-from-other.import-details', $data_content);
    }

    /**Translate the existing positions of the board */
    public function postImportTranslateBoard(Request $request): RedirectResponse
    {
        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }
        $request->validate([
            'boardId' => 'required',
        ]);

        $board = Board::find((int) $request->input('boardId'));
        $board_data = json_encode($board->data);
        for ($i = 1; $request->input('traductor_'.$i) != null; $i++) {
            $strings = explode(';', $request->input('traductor_'.$i));
            if ($strings[0] == $strings[1]) {
                continue;
            }

            Row::where('board_id', $board->getId())->where('position', $strings[0])->update(['position' => $strings[1]]);

            //to avoid substring-coincident positions to cause problems
            $strings[0] = '"'.$strings[0].'"';
            $strings[1] = '"'.$strings[1].'"';

            $board_data = str_replace($strings[0], $strings[1], $board_data);

            $board->html_pinya = str_replace($strings[0], $strings[1], $board->html_pinya);
            if ($board->html_folre != null) {
                $board->html_folre = str_replace($strings[0], $strings[1], $board->html_folre);
                if ($board->html_manilles != null) {
                    $board->html_manilles = str_replace($strings[0], $strings[1], $board->html_manilles);
                    if ($board->html_puntals != null) {
                        $board->html_puntals = str_replace($strings[0], $strings[1], $board->html_puntals);
                    }
                }
            }

        }
        $board->data = json_decode($board_data);
        $board->name = $request->input('newName');
        $board->save();

        return redirect()->route('boards.list');
    }

    public function postSetPublicBoard(Request $request): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }

        $request->validate([
            'id_board' => 'required|numeric',
            'is_public' => 'required',
        ]);

        $board = Board::find($request->input('id_board'));
        $board->is_public = (int) $request->input('is_public');
        $board->save();

        return new JsonResponse($board->is_public, Response::HTTP_OK);
    }

    /**Add board*/
    public function postAddBoard(Request $request): View
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }

        $request->validate([
            'type' => 'required',
            'base' => 'required|numeric',
            'name' => 'required|min:3|max:100',
            'type_map' => 'required',
        ]);
        $colla = Colla::getCurrent();

        switch ($request->input('type')) {
            case BasesEnum::PINYA:
                $data_json = ['pinya' => null];
                break;
            case BasesEnum::FOLRE:
                $data_json = ['pinya' => null, 'folre' => null];
                break;
            case BasesEnum::MANILLES:
                $data_json = ['pinya' => null, 'folre' => null, 'manilles' => null];
                break;
            case BasesEnum::PUNTALS:
                $data_json = ['pinya' => null, 'folre' => null, 'manilles' => null, 'puntals' => null];
                break;
        }

        //TODO make on manager
        $board = new Board();

        $board->colla_id = $colla->getId();
        $board->name = $request->input('name');
        $board->type = $request->input('type');
        $board->data = $data_json;

        $board->save();

        DB::table('board_tags')->insert(['tag_id' => (int) $request->input('base'), 'board_id' => $board->getId()]);

        $data_content['bases'] = $colla->getTags(TypeTags::BOARDS);
        $data_content['board'] = $board;
        $data_content['type_map'] = $request->input('type_map');

        return view('boards.import.1-upload-svg', $data_content);
    }

    /** Upload SVG && make data content (PINYA/FOLRE/MANILLES/PUNTALS) on Board via AJAX*/
    public function postUploadSvg(Board $board, Request $request): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {

            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $this->authorize('getBoard', $board);

        $colla = Colla::getCurrent();
        $data = $board->getData();

        $file = $request->file('svg');
        $type_map = $request->input('type_map');
        $html = $request->input('html');

        $file_name = $board->getId().'_'.$type_map.'.svg';

        switch ($type_map) {
            case BasesEnum::PINYA:
                $board->setAttribute('html_pinya', $html);
                $data['pinya']['svg'] = $file_name;
                $data['pinya']['structure'] = null;
                $type_map = BasesEnum::Folre()->value();
                break;
            case BasesEnum::FOLRE:
                $board->setAttribute('html_folre', $html);
                $data['folre']['svg'] = $file_name;
                $data['folre']['structure'] = null;
                $type_map = BasesEnum::Manilles()->value();
                break;
            case BasesEnum::MANILLES:
                $board->setAttribute('html_manilles', $html);
                $data['manilles']['svg'] = $file_name;
                $data['manilles']['structure'] = null;
                $type_map = BasesEnum::Puntals()->value();
                break;
            case BasesEnum::PUNTALS:
                $board->setAttribute('html_puntals', $html);
                $data['puntals']['svg'] = $file_name;
                $data['puntals']['structure'] = null;
                $type_map = null;
                break;
        }

        $board->setAttribute('data', $data);
        $board->setAttribute('data_code', $data);
        $board->save();

        $file->move(public_path('media/colles/'.$colla->getShortName().'/svg'), $file_name);

        $data_content['board'] = $board;
        $data_content['type_map'] = $type_map;

        return new JsonResponse(true, Response::HTTP_OK);
    }

    /**Show the form for editing the specified resource.*/
    public function getTagRowMap(Board $board, string $map): View
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }
        $this->authorize('getBoard', $board);

        $data_content['board'] = $board;
        $data_content['type_map'] = $map;

        return view('boards.import.2-tag-row-map', $data_content);
    }

    public function postTagBaixPosition(BoardManager $manager, Board $board, Request $request): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('view boards')) {

            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $this->authorize('getBoard', $board);

        $validator = Validator::make($request->all(), [
            'rowId' => 'required|numeric',
            'name' => 'required|alpha_dash',
            'base' => 'required|alpha',
        ]);

        if ($validator->fails()) {

            return new JsonResponse($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        }

        $rowId = (int) $request->input('rowId');
        $rowName = preg_replace("/\s+/", '', $request->input('name'));

        if (! Tag::validName($rowName)) {

            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $base = strtoupper($request->input('base'));

        $row = $manager->tagPosition($board, $rowId, $rowName, Board::baixName, $base);
        $manager->addBaixBoardData($board, strtolower($base), $rowName, $rowId);

        return new JsonResponse($row, Response::HTTP_OK);
    }

    /** Tag position from Board and map via AJAX*/
    public function postTagPosition(BoardManager $manager, Board $board, string $map, Request $request): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('view boards')) {

            return new JsonResponse(false, Response::HTTP_FORBIDDEN);
        }

        $this->authorize('getBoard', $board);

        $validator = Validator::make($request->all(), [
            'rowId' => 'required|numeric',
            'position' => 'required',
            'id_position' => 'required|numeric',
            'cord' => 'nullable|numeric',
            'row' => 'required',
            'side' => 'nullable|alpha',
        ]);

        if ($validator->fails()) {

            return new JsonResponse($request->all(), Response::HTTP_BAD_REQUEST);
        }

        $rowId = (int) $request->input('rowId');
        $position = $request->input('position');
        $id_position = (int) $request->input('id_position');
        $cord = (int) $request->input('cord');
        $core = $request->input('core') === 'true';
        $row = $request->input('row');
        $side = $request->input('side');
        $base = strtolower($map);

        $manager->tagPosition($board, $rowId, $row, $position, $base, $cord, $side, $id_position);
        $manager->addPositionBoardData($board, $base, $rowId, $row, $position, $core, $cord, $side, $id_position);

        return new JsonResponse(true, Response::HTTP_OK);
    }

    /** post delete position via AJAX*/
    public function postDeletePosition(BoardManager $manager, Board $board, string $map, Request $request): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }
        $this->authorize('getBoard', $board);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'id_row' => 'required|numeric',
            'row' => 'required|string',
            'side' => 'nullable|string',
            'cord' => 'required|numeric',
        ]);

        if ($validator->fails()) {

            return new JsonResponse($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        }

        if (! $manager
            ->deleteRow(
                $board->getId(),
                (int) $request->get('id_row'),
                strtolower($map),
                $request->get('row'),
                (int) $request->get('cord'),
                $request->get('side'),
                $request->get('name')
            )) {

            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(true, Response::HTTP_OK);
    }

    /** Tag ALL positions into map
     */
    public function getTagAllMap(Board $board, string $map): View
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }
        $this->authorize('getBoard', $board);

        $colla = Colla::getCurrent();
        $colors = Board::getColors();

        $row_color = [];

        $map_rows = $board->getArrayBasesRows()[strtolower($map)];

        foreach ($map_rows as $k => $row) {
            $row_color[$row] = $colors[$k];
        }

        $data_content['map_rows'] = $map_rows;
        $data_content['row_color'] = $row_color;
        $data_content['board'] = $board;
        $data_content['type_map'] = $map;
        $data_content['positions'] = $colla->getTags(TypeTags::POSITIONS);
        $data_content['boardRows'] = $board->getRows()
            ->where('base', $map)
            ->map->only(['div_id', 'row', 'cord', 'side', 'position', 'base'])
            ->toArray();

        return view('boards.import.3-tag-all-map', $data_content);
    }

    /** styles rows map*/
    public function getStyleMap(Board $board, string $map): View
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }
        $this->authorize('getBoard', $board);

        $colla = Colla::getCurrent();

        $data_content['board'] = $board;
        $data_content['type_map'] = $map;
        $data_content['positions'] = $colla->getTags(TypeTags::POSITIONS);
        $data_content['boardRows'] = $board->getRows()
            ->where('base', $map)
            ->map->only(['div_id', 'row', 'cord', 'side', 'position', 'base', 'id_position'])
            ->toArray();

        return view('boards.import.4-style-map', $data_content);
    }

    public function getModalFinishImport(int $boardId, string $base): View
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        if (! $board = $colla->boards()->find($boardId)) {

            abort(404);
        }

        $data_content['board'] = $board;
        $data_content['base'] = $base;

        return view('boards.import.modals.finish-import', $data_content);
    }

    /** change style map bia AJAX
     * TODO refactor
     */
    public function postStyleMapAjax(Board $board, string $map, Request $request): JsonResponse
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }
        $this->authorize('getBoard', $board);

        $html = 'html_'.strtolower($map);

        $board->{$html} = $request->input('html');

        $board->save();

        return new JsonResponse(true, Response::HTTP_OK);
    }

    /** add map on board*/
    public function getAddMap(Board $board, string $map): View
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }
        $this->authorize('getBoard', $board);

        $colla = Colla::getCurrent();

        $data_content['bases'] = $colla->getTags(TypeTags::BOARDS);
        $data_content['board'] = $board;
        $data_content['type_map'] = $map;

        return view('boards.import.1-upload-svg', $data_content);
    }

    /** get modal preview Board*/
    public function getModalPreviewBoard(Board $board): View
    {

        $user = $this->user();

        if (! $user->can('view boards')) {
            abort(404);
        }
        $this->authorize('getBoard', $board);

        $data_content['colla'] = Colla::getCurrent();
        $data_content['board'] = $board;

        return view('boards.modals.modal-view', $data_content);
    }

    /**Remove the specified resource from storage.*/
    public function postDestroy(Board $board): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }
        $this->authorize('getBoard', $board);

        $colla = Colla::getCurrent();

        foreach ($board->getData() as $base) {
            if ($base && file_exists(public_path('media/colles/'.$colla->getShortName()).'/svg/'.$base['svg'])) {
                unlink(public_path('media/colles/'.$colla->getShortName()).'/svg/'.$base['svg']);
            }
        }

        $board->delete();

        Session::flash('status_ok', trans('boards.board_destroyed'));

        return redirect()->route('boards.list');
    }

    /**Update Board Name*/
    public function postUpdateBoardName(Board $board, Request $request): RedirectResponse
    {
        $user = $this->user();

        if (! $user->can('edit boards')) {
            abort(404);
        }
        $this->authorize('getBoard', $board);

        $request->validate([
            'name' => 'required|min:3|max:100',
        ]);

        $board->name = $request->input('name');
        $board->save();

        Session::flash('status_ok', trans('boards.board_updated'));

        return redirect()->route('boards.list');
    }
}
