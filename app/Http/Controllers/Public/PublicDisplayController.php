<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Enums\BasesEnum;
use App\Http\Controllers\Controller;
use App\Managers\CollesManager;
use App\Managers\EventBoardManager;
use App\Managers\PublicDisplayManager;
use App\Services\TOTPService;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class PublicDisplayController extends Controller
{
    public function getBoardDisplay(PublicDisplayManager $manager, EventBoardManager $eventBoardManager, CollesManager $collesManager, string $shortName, string $token): View
    {

        if (! $colla = $collesManager->fetchByShortName($shortName)) {
            abort(404);
        }
        $dataContent['collaName'] = $colla->getName();
        $dataContent['shortName'] = $shortName;

        $boardInfo = $manager->getDisplayBoardInfoByPublicToken($colla->getShortName(), $token);

        if (isset($boardInfo['publicDisplayDisabled'])) {
            $dataContent['boardEventName'] = '';
            $dataContent['castellerId'] = (isset($boardInfo['casteller'])) ?: null;
            $dataContent['message'] = 'boards.public_display_not_enabled';

            return view('public.display.disabled', $dataContent);
        } elseif (isset($boardInfo['noRonda'])) {
            $dataContent['boardEventName'] = '';
            $dataContent['castellerId'] = (isset($boardInfo['casteller'])) ?: null;
            $dataContent['message'] = 'boards.ronda_not_found';

            return view('public.display.disabled', $dataContent);
        } elseif (isset($boardInfo['noPinyaOnDisplay'])) {
            $dataContent['boardEventName'] = '';
            $dataContent['castellerId'] = (isset($boardInfo['casteller'])) ?: null;
            $logo = ($colla->getLogo()) ? asset('media/colles/'.$colla->getShortName().'/'.$colla->getLogo()) : asset('media/img/logo.svg');
            $dataContent['logo'] = $logo;

            return view('public.display.logo', $dataContent);
        } else {
            if (! isset($boardInfo['board']) || ! $board = $boardInfo['board']) {
                abort(404);
            }
            if (! isset($boardInfo['boardEvent']) || ! $boardEvent = $boardInfo['boardEvent']) {
                abort(404);
            }

            $dataContent['token'] = $token;
            $dataContent['board'] = $board;
            $dataContent['castellerBase'] = $boardInfo['castellerBase'] ?? BasesEnum::PINYA;
            $dataContent['base'] = $board?->getHtmlBase($dataContent['castellerBase']);
            $dataContent['boardEvent'] = $boardEvent;
            $dataContent['castellerId'] = $boardInfo['castellerId'] ?? 0;
            $dataContent['positions'] = $eventBoardManager->loadMapByBoardEventId($colla, $boardEvent, $dataContent['castellerBase']);
            $dataContent['boardEventName'] = $boardEvent->getDisplayName();

            // TOTP data if event is associated
            $event = $boardEvent->getEvent();
            if ($event) {
                $dataContent['event'] = $event;
                $dataContent['totpCode'] = TOTPService::getCurrentCode($event);
                $dataContent['remainingSeconds'] = TOTPService::getRemainingSeconds($event);
                $dataContent['totalSeconds'] = TOTPService::getPeriod($event);
            } else {
                $dataContent['event'] = null;
                $dataContent['totpCode'] = null;
                $dataContent['remainingSeconds'] = null;
                $dataContent['totalSeconds'] = 0;
            }
        }

        return view('public.display.home', $dataContent);
    }

    public function getLoadMap(PublicDisplayManager $manager, EventBoardManager $eventBoardManager, CollesManager $collesManager, string $shortName, string $token, string $base): JsonResponse
    {
        if (! $colla = $collesManager->fetchByShortName($shortName)) {
            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        if (! $boardEvent = $manager->fetchDisplayBoardEventByPublicToken($shortName, $token)) {
            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($eventBoardManager->loadMapByBoardEventId($colla, $boardEvent, $base), Response::HTTP_OK);
    }

    public function getTotpCode(PublicDisplayManager $manager, CollesManager $collesManager, string $shortName, string $token): JsonResponse
    {
        if (! $colla = $collesManager->fetchByShortName($shortName)) {
            return new JsonResponse(['error' => 'Colla not found'], Response::HTTP_NOT_FOUND);
        }

        $boardInfo = $manager->getDisplayBoardInfoByPublicToken($colla->getShortName(), $token);

        if (! isset($boardInfo['boardEvent']) || ! $boardEvent = $boardInfo['boardEvent']) {
            return new JsonResponse(['error' => 'Board event not found'], Response::HTTP_NOT_FOUND);
        }

        $event = $boardEvent->getEvent();
        if (! $event) {
            return new JsonResponse(['error' => 'No event associated'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'totpCode' => TOTPService::getCurrentCode($event),
            'remainingSeconds' => TOTPService::getRemainingSeconds($event),
            'totalSeconds' => TOTPService::getPeriod($event),
        ], Response::HTTP_OK);
    }
}
