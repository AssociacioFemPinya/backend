<?php

namespace App;

use App\Helpers\Encryptors\EncryptorAes256;
use App\Helpers\Humans;
use App\Traits\FilterableTrait;
use App\Traits\TimeStampsGetterTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BoardEvent extends Model
{
    use FilterableTrait;
    use TimeStampsGetterTrait;

    protected $table = 'board_event';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected static $filterClass = \App\Services\Filters\EventBoardsFilter::class;

    //Relations
    public function event(): HasOne
    {
        return $this->hasOne(Event::class, 'id_event', 'event_id');
    }

    public function board(): HasOne
    {
        return $this->hasOne(Board::class, 'id_board', 'board_id');
    }

    public function boardPosition(): HasMany
    {
        return $this->hasMany(BoardPosition::class, 'board_event_id', 'id');
    }

    public function ronda(): ?HasOne
    {
        return $this->hasOne(Ronda::class, 'board_event_id', 'id');
    }

    //Properties
    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getEventId(): int
    {
        return $this->getAttribute('event_id');
    }

    public function getEvent(): Event
    {
        return $this->getAttribute('event');
    }

    public function getBoard(): Board
    {
        return $this->getAttribute('board');
    }

    public function getBoardId(): int
    {
        return $this->getAttribute('board_id');
    }

    public function getDisplay(): bool
    {
        return $this->getAttribute('display');
    }

    public function getFavourite(): bool
    {
        return $this->getAttribute('favourite');
    }

    public function getUpdatedAt(bool $shortDate = false): ?string
    {
        return Humans::parseDate($this->getAttribute('updated_at'), $shortDate);
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function getDisplayName(): ?string
    {
        return ($this->getAttribute('name')) ?: $this->getBoard()->getName();
    }

    public function getRonda(): ?Ronda
    {
        return $this->getAttribute('ronda');
    }

    public function getPublicUrl($castellerId = null): ?string
    {

        $colla = $this->getEvent()->getColla();
        $encryptor = new EncryptorAes256($colla->getConfig()->getAes256KeyPublic());
        $toEncrypt = [
            'collaId' => $colla->getId(),
            'boardEventId' => $this->getId(),
        ];
        if ($castellerId != null) {
            $toEncrypt['castellerId'] = $castellerId;
        }
        $toEncrypt = json_encode($toEncrypt);

        return route('public.display', ['shortName' => $colla->getShortName(), 'token' => $encryptor->encrypt($toEncrypt)]);
    }
}
