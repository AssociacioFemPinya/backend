<?php

declare(strict_types=1);

namespace App\Managers;

use App\Board;
use App\Factories\BoardFactory;
use App\Factories\RowFactory;
use App\Repositories\BoardEventRepository;
use App\Repositories\BoardRepository;
use App\Row;
use Symfony\Component\HttpFoundation\ParameterBag;

final class BoardManager
{
    private BoardEventRepository $boardEventRepository;

    private BoardRepository $boardRepository;

    public function __construct(BoardEventRepository $boardEventRepository, BoardRepository $boardRepository)
    {
        $this->boardEventRepository = $boardEventRepository;
        $this->boardRepository = $boardRepository;
    }

    public function addBaixBoardData(Board $board, string $base, string $rowName, int $rowId): Board
    {
        $data = $board->getData();

        $data[strtolower($base)]['structure'][$rowName][Board::baixName] = $rowId;

        $bag = new ParameterBag(['data' => $data]);
        $board = BoardFactory::update($board, $bag);
        $this->boardRepository->saveBoard($board);

        return $board;
    }

    public function addPositionBoardData(Board $board, string $base, int $rowId, string $rengle, string $position, bool $core, ?int $cord = 0, ?string $side = '', ?int $id_position = 0): Board
    {
        $data = $board->getData();

        if ($core) {

            if (is_null($side)) {
                $data[$base]['structure'][$rengle][$position] = $rowId;
            } else {
                $data[$base]['structure'][$rengle][$position][$side] = $rowId;
            }
        } else {
            if (is_null($side)) {

                $data[$base]['structure'][$rengle][$position][(int) $cord] = $rowId;
            } else {

                if (isset($data[$base]['structure'][$rengle][$position][$side][(int) $cord])) {

                    if (! in_array($rowId, $data[$base]['structure'][$rengle][$position][$side][(int) $cord])) {

                        $data[$base]['structure'][$rengle][$position][$side][$cord][] = $rowId;
                    }
                } else {

                    $data[$base]['structure'][$rengle][$position][$side][(int) $cord] = [$rowId];
                }
            }
        }

        $bag = new ParameterBag(['data' => $data]);
        $board = BoardFactory::update($board, $bag);
        $this->boardRepository->saveBoard($board);

        return $board;
    }

    public function tagPosition(Board $board, int $divId, string $rengle, string $position, string $base, ?int $cord = 0, ?string $side = '', ?int $id_position = 0): Row
    {
        $row = $this->boardRepository->fetchRowInBoardByBaseDivId($board->getId(), $divId, $base);
        if ($row !== null) {
            self::updateRow($row, $divId, $rengle, $position, $base, $cord, $side, $id_position);
        } else {
            $row = self::createRow($board, $divId, $rengle, $position, $base, $cord, $side, $id_position);
        }

        return $row;
    }

    public function updateRow(Row $row, int $divId, string $rengle, string $position, string $base, ?int $cord = 0, ?string $side = '', ?int $id_position = 0): Row
    {
        $bag = new ParameterBag([
            'div_id' => $divId,
            'row' => $rengle,
            'cord' => $cord,
            'side' => $side ?? '',
            'id_position' => $id_position,
            'position' => $position,
            'base' => $base,
        ]);
        $row = RowFactory::update($row, $bag);

        $this->boardEventRepository->saveRow($row);

        return $row;
    }

    public function createRow(Board $board, int $rowId, string $rengle, string $position, string $base, ?int $cord = 0, ?string $side = '', ?int $id_position = 0): Row
    {
        $bag = new ParameterBag([
            'div_id' => $rowId,
            'row' => $rengle,
            'cord' => $cord,
            'side' => $side ?? '',
            'id_position' => $id_position,
            'position' => $position,
            'base' => $base,
        ]);

        $row = RowFactory::make($board, $bag);

        $this->boardEventRepository->saveRow($row);

        return $row;
    }

    /** delete row from board*/
    public function deleteRow(int $boardId, int $rowId, string $base, string $rengle, int $cord, ?string $side, string $name): bool
    {
        $board = $this->boardRepository->fetchOneById($boardId);

        if (! $board) {

            return false;
        }

        $row = $this->boardRepository->fetchRowInBoardByBaseDivId($boardId, $rowId, $base);

        if (! $row) {

            return false;
        }
        $row->delete();

        $data = $board->getData();

        if ($side) {
            $side = strtolower($side);
        }

        if ($cord === 0) {
            if (! $side) {
                unset($data[$base]['structure'][$rengle][$name]);
            } else {
                unset($data[$base]['structure'][$rengle][$name][$side]);
            }
        } else {
            if (! $side) {
                unset($data[$base]['structure'][$rengle][$name][$cord]);
            } else {
                if (($key = array_search($rowId, $data[$base]['structure'][$rengle][$name][$side][$cord])) !== false) {
                    unset($data[$base]['structure'][$rengle][$name][$side][$cord][$key]);
                }
            }
        }

        $bag = new ParameterBag(['data' => $data]);
        $board = BoardFactory::update($board, $bag);

        return $this->boardRepository->saveBoard($board);
    }
}
