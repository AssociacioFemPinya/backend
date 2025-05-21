<?php

declare(strict_types=1);

namespace App\Managers;

use App\BoardEvent;
use App\BoardPosition;
use App\Casteller;
use App\Colla;
use App\Enums\BasesEnum;
use App\Enums\CastellersStatusEnum;
use App\Enums\FilterSearchTypesEnum;
use App\Events\PublicDisplayUpdated;
use App\Factories\BoardEventFactory;
use App\Repositories\BoardEventRepository;
use App\Repositories\BoardRepository;
use App\Repositories\CastellerRepository;
use App\Repositories\EventRepository;
use App\Row;
use App\Tag;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\ParameterBag;

final class EventBoardManager
{
    private BoardRepository $boardRepository;

    private EventRepository $eventRepository;

    private BoardEventRepository $boardEventRepository;

    private CastellerRepository $castellersRepository;

    public function __construct(BoardRepository $boardRepository, EventRepository $eventRepository, BoardEventRepository $boardEventRepository, CastellerRepository $castellerRepository)
    {
        $this->boardRepository = $boardRepository;
        $this->eventRepository = $eventRepository;
        $this->boardEventRepository = $boardEventRepository;
        $this->castellersRepository = $castellerRepository;
    }

    public function listCastellersPositionFiltered(
        Colla $colla,
        BoardEvent $boardEvent,
        ?int $positionId = null,
        array $attendanceStatus = [],
        array $attendanceStatusVerified = [],
        ?string $filterText = null,
        string $status = CastellersStatusEnum::ALL,
    ): Collection {
        /** @var Tag $position */
        if (! is_null($positionId)) {
            $position = $colla->tags()->find($positionId);
            $castellersIncluded = [$position->getId()];
        } else {
            $castellersIncluded = [];
        }

        $castellersIncludedSearch = FilterSearchTypesEnum::OR;

        $castellers = Casteller::filter($colla)
            ->withStatus(CastellersStatusEnum::getBy($status))
            ->withTags($castellersIncluded, $castellersIncludedSearch)
            ->withAlias($filterText);

        return $this->castellersRepository
            ->fetchFromAttendance($castellers, $boardEvent, $attendanceStatus, $attendanceStatusVerified);
    }

    public function putCastellerOnBoard(BoardEvent $boardEvent, Casteller $casteller, Row $row): ?BoardPosition
    {
        $isCastellerInBoard = $this->boardEventRepository->fetchBoardPositionFromCastellerInBoardEvent($boardEvent, $casteller);

        if ($isCastellerInBoard) {
            return null;
        }

        $isRowFull = $this->boardEventRepository->fetchBoardPositionFromRowInBoardEvent($boardEvent, $row);

        if ($isRowFull) {
            return null;
        }

        $boardPosition = $this->boardEventRepository->createBoardPosition($boardEvent, $casteller, $row);
        $this->boardEventRepository->saveBoardPosition($boardPosition);

        return $boardPosition;
    }

    public function swapCastellersOnBoard(BoardEvent $boardEvent, Row $row, Row $rowSwap): ?array
    {
        $boardPosition = $this->boardEventRepository->fetchBoardPositionFromRowInBoardEvent($boardEvent, $row);
        $boardPositionSwap = $this->boardEventRepository->fetchBoardPositionFromRowInBoardEvent($boardEvent, $rowSwap);

        $isCastellerInBoard = $this->boardEventRepository
            ->fetchBoardPositionFromCastellerInBoardEvent($boardPosition->getBoardEvent(), $boardPosition->getCasteller());

        //Change casteller row
        if (! $boardPositionSwap && $isCastellerInBoard) {

            $updatedBoardPosition = $this
                ->boardEventRepository->updateBoardPosition($boardPosition, $boardPosition->getCasteller(), $rowSwap);

            $this->boardEventRepository->start();

            try {
                $updatedBoardPosition->save();

                $this->boardEventRepository->success();

                return [$updatedBoardPosition, null];
            } catch (\Exception $e) {

                $this->boardEventRepository->fail();
                //TODO: send exception to slack $e->getMessage()

                return null;
            }
        }

        //Swap castellers rows
        $isCastellerSwapInBoard = $this->boardEventRepository
            ->fetchBoardPositionFromCastellerInBoardEvent($boardPositionSwap->getBoardEvent(), $boardPositionSwap->getCasteller());

        if ($isCastellerInBoard && $isCastellerSwapInBoard) {

            $newBoardPosition = $this->boardEventRepository
                ->createBoardPosition($boardEvent, $boardPosition->getCasteller(), $rowSwap);
            $boardPosition->delete();

            $newBoardPositionSwap = $this->boardEventRepository
                ->createBoardPosition($boardEvent, $boardPositionSwap->getCasteller(), $row);
            $boardPositionSwap->delete();

            $this->boardEventRepository->start();

            try {
                $newBoardPosition->save();
                $newBoardPositionSwap->save();

                $this->boardEventRepository->success();

                return [$newBoardPosition, $newBoardPositionSwap];
            } catch (\Exception $e) {

                $this->boardEventRepository->fail();
                //TODO: send exception to slack $e->getMessage()

                return null;
            }
        }

        return null;
    }

    public function emptyRowFromEventBoard(BoardEvent $boardEvent, int $divId, string $base): bool
    {
        $board = $boardEvent->getBoard();

        /** @var Row $row */
        $row = $board->rows()
            ->where('div_id', $divId)
            ->where('base', $base)
            ->first();

        if (! $row) {

            return false;
        }

        return (bool)
        BoardPosition::query()
            ->where('row_id', $row->getId())
            ->where('base', $base)
            ->where('board_event_id', $boardEvent->getId())
            ->delete();
    }

    public function eventBoardToggleToDisplay(int $boardEventId, int $collaId): bool
    {
        $boardEvent = $this->boardEventRepository->fetchOneById($boardEventId);
        if ($boardEvent->getBoard()->getCollaId() !== $collaId) {

            return false;
        }
        $display = $boardEvent->getDisplay();

        $this->boardEventRepository->updateAllBoardEventByCollaId($collaId, ['display' => false]);

        $bag = new ParameterBag(['display' => ! $display]);
        $boardEvent = BoardEventFactory::update($boardEvent, $bag);
        $boardEvent->save();

        PublicDisplayUpdated::dispatch(Colla::where('id_colla', $collaId)->first());

        return ! $display;
    }

    public function eventBoardToggleAddFavourite(int $boardEventId, int $collaId): bool
    {
        $boardEvent = $this->boardEventRepository->fetchOneById($boardEventId);
        if ($boardEvent->getBoard()->getCollaId() !== $collaId) {

            return false;
        }
        $favourite = $boardEvent->getFavourite();

        $bag = new ParameterBag(['favourite' => ! $favourite]);
        $boardEvent = BoardEventFactory::update($boardEvent, $bag);
        $boardEvent->save();

        return ! $favourite;
    }

    /* A Refactor is needed. BoardEvent is a Model so should have its own Manager */
    public function eventBoardSetName(BoardEvent $boardEvent, string $name)
    {

        $bag = new ParameterBag(['name' => $name]);
        $boardEvent = BoardEventFactory::update($boardEvent, $bag);
        $boardEvent->save();

    }

    public function loadMapByBoardEventId(Colla $colla, BoardEvent $boardEvent, string $base = BasesEnum::PINYA): Collection
    {

        return BoardPosition::filter($colla)
            ->inBoardEvent($boardEvent)
            ->inBase($base)
            ->getRowInfo();
    }

    public function deletePositionsByBoardEventId(int $id, ?array $castellers = null)
    {
        if ($castellers) {
            BoardPosition::query()
                ->where('board_event_id', $id)
                ->whereIn('casteller_id', $castellers)
                ->delete();
        } else {
            BoardPosition::query()
                ->where('board_event_id', $id)
                ->delete();
        }

    }

    public function importPositionsByBoardEventId(int $sourceBoardEventId, BoardEvent $targetBoardEvent)
    {
        $positions = BoardPosition::query()
            ->where('board_event_id', $sourceBoardEventId)
            ->get();

        foreach ($positions as $position) {
            $boardPosition = $this->boardEventRepository->createBoardPosition($targetBoardEvent, $position->getCasteller(), $position->getRow());
            $this->boardEventRepository->saveBoardPosition($boardPosition);
        }

    }
}
