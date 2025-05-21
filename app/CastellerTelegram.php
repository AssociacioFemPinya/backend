<?php

namespace App;

use BotMan\BotMan\Interfaces\UserInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CastellerTelegram extends Model
{
    protected $table = 'casteller_telegram';

    protected $primaryKey = 'id_casteller_telegram';

    public $timestamps = true;

    public static function getCastellerTelegramByTelegramId($telegram_id): ?CastellerTelegram
    {
        return CastellerTelegram::where('telegram_id', $telegram_id)->first();
    }

    public static function getCastellerTelegramByIdCasteller($casteller_id): ?CastellerTelegram
    {
        return CastellerTelegram::where('casteller_id', $casteller_id)->first();
    }

    public static function newCastellerTelegram(Casteller $casteller, UserInterface $botmanUser): ?CastellerTelegram
    {

        $castellerTelegram = new static();

        $castellerTelegram->casteller_id = $casteller->getId();
        $castellerTelegram->colla_id = $casteller->getCollaId();
        $castellerTelegram->telegram_id = $botmanUser->getId();
        $castellerTelegram->casteller_active_id = $casteller->getId();

        $castellerTelegram->save();

        return $castellerTelegram;

    }

    // relations

    public function casteller(): BelongsTo
    {
        return $this->belongsTo(Casteller::class, 'casteller_id', 'id_casteller');
    }

    //getters relations

    public function getCasteller(): Casteller
    {
        return $this->getAttribute('casteller');
    }

    //getters
    public function getId(): int
    {
        return $this->getAttribute('id_casteller_telegram');
    }

    public function getCastellerId(): int
    {
        return $this->getAttribute('casteller_id');
    }

    public function getCollaId(): int
    {
        return $this->getAttribute('colla_id');
    }

    public function getTelegramId(): int
    {
        return $this->getAttribute('telegram_id');
    }

    public function getCastellerActiveId(): int
    {
        return $this->getAttribute('casteller_active_id');
    }

    public function setCastellerActiveId(int $castellerActiveID)
    {
        return $this->setAttribute('casteller_active_id', $castellerActiveID);
    }
}
