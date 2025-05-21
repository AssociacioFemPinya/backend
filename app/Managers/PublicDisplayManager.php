<?php

declare(strict_types=1);

namespace App\Managers;

use App\BoardEvent;
use App\Helpers\Encryptors\EncryptorAes256;
use App\Repositories\BoardEventRepository;
use App\Repositories\BoardRepository;
use App\Repositories\CastellerRepository;
use App\Repositories\CollaRepository;

final class PublicDisplayManager
{
    private CollaRepository $collaRepository;

    private BoardEventRepository $boardEventRepository;

    private CastellerRepository $castellerRepository;

    private BoardRepository $boardRepository;

    public function __construct(CollaRepository $collaRepository, BoardEventRepository $boardEventRepository, BoardRepository $boardRepository, CastellerRepository $castellerRepository)
    {
        $this->collaRepository = $collaRepository;
        $this->boardEventRepository = $boardEventRepository;
        $this->boardRepository = $boardRepository;
        $this->castellerRepository = $castellerRepository;
    }

    public function getDisplayBoardInfoByPublicToken(string $shortName, string $token): array
    {
        if (! $colla = $this->collaRepository->fetchByShortName($shortName)) {
            return [];
        }
        $collaConfig = $colla->getConfig();
        if (! $key = $collaConfig->getAes256KeyPublic()) {
            return [];
        }

        $decryptedData = self::decryptToken($token, $key);
        if (is_null($decryptedData)) {
            abort(404);
        }

        $return = [];

        if (! property_exists($decryptedData, 'collaId') || $decryptedData?->collaId === null) {
            return $return;
        }

        /* We get info from PublicDisplay if request is for Public Display */
        if (property_exists($decryptedData, 'publicDisplay') && $decryptedData?->publicDisplay === true) {

            if (! $collaConfig->getPublicDisplayEnabled()) {
                return ['publicDisplayDisabled' => true];
            }

            $boardEvent = $this->boardEventRepository->fetchDisplayBoardEventByCollaId($decryptedData->collaId);

            if (is_null($boardEvent)) {
                return ['noPinyaOnDisplay' => true];
            }

            /* We get info from token for Pinyes URLs */
        } else {

            if (! property_exists($decryptedData, 'boardEventId') || $decryptedData?->boardEventId === null) {
                return $return;
            }

            $boardEvent = $this->boardEventRepository->fetchOneById($decryptedData->boardEventId);

            if (is_null($boardEvent->getRonda())) {
                return ['noRonda' => true];
            }

        }

        $return['boardEvent'] = $boardEvent;
        $return['board'] = $boardEvent->getBoard();

        if (property_exists($decryptedData, 'castellerId')) {
            $return['castellerId'] = $decryptedData?->castellerId;
            if ($return['castellerId'] && $return['castellerId'] > 0) {
                $casteller = $this->castellerRepository->fetchOneById($return['castellerId']);
                $boardPosition = $this->boardEventRepository->fetchBoardPositionFromCastellerInBoardEvent($boardEvent, $casteller);
                if (isset($boardPosition)) {
                    $return['castellerBase'] = $boardPosition->getBase();
                }
            }
        }

        return $return;
    }

    public function fetchDisplayBoardEventByPublicToken(string $shortName, string $token): ?BoardEvent
    {
        if (! $colla = $this->collaRepository->fetchByShortName($shortName)) {
            return null;
        }
        if (! $key = $colla->getConfig()->getAes256KeyPublic()) {
            return null;
        }

        $decryptedData = self::decryptToken($token, $key);

        if ($decryptedData?->collaId !== null) {

            return $this->boardEventRepository->fetchDisplayBoardEventByCollaId($decryptedData->collaId);
        }

        return null;
    }

    private function decryptToken(string $token, string $key): ?\stdClass
    {
        $encryptor = new EncryptorAes256($key);
        if (! $res = $encryptor->decrypt($token)) {
            return null;
        }

        if ($decryptedData = json_decode($res, false)) {

            return $decryptedData;
        }

        return null;
    }
}
