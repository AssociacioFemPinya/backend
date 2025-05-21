<?php

namespace App;

use App\Enums\EventTypeNameEnum;
use App\Helpers\Encryptors\EncryptorAes256;
use App\Traits\TimeStampsGetterTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CollaConfig extends Model
{
    use TimeStampsGetterTrait;

    protected $table = 'colla_config';

    protected $primaryKey = 'id_colla_config';

    public $timestamps = true;

    protected $casts = [
        'boards_enabled' => 'boolean',
    ];

    public function getPublicDisplayEnabled(): bool
    {
        return $this->getAttribute('public_display_enabled');
    }

    public function getPublicDisplayUrl($castellerId = null): ?string
    {
        if (! $this->getPublicDisplayEnabled()) {
            return '';
        }

        $encryptor = new EncryptorAes256($this->getAes256KeyPublic());
        $toEncrypt = [
            'collaId' => $this->getCollaId(),
            'publicDisplay' => true,
        ];

        if ($castellerId != null) {
            $toEncrypt['castellerId'] = $castellerId;
        }
        $toEncrypt = json_encode($toEncrypt);

        return route('public.display', ['shortName' => $this->getColla()->getShortName(), 'token' => $encryptor->encrypt($toEncrypt)]);
    }

    // Relations
    public function colla(): BelongsTo
    {
        return $this->belongsTo(Colla::class, 'colla_id', 'id_colla');
    }

    //getters relations
    public function getColla(): Colla
    {
        return $this->getAttribute('colla');
    }

    //getters
    public function getId(): int
    {
        return $this->getAttribute('id_colla_config');
    }

    public function getCollaId(): int
    {
        return $this->getAttribute('colla_id');
    }

    public function getTranslationActivitat(): ?string
    {
        return $this->getAttribute('translation_activitat');
    }

    public function getTranslationActuacio(): ?string
    {
        return $this->getAttribute('translation_actuacio');
    }

    public function getTranslationAssaig(): ?string
    {
        return $this->getAttribute('translation_assaig');
    }

    public function getBoardsEnabled(): bool
    {
        return $this->getAttribute('boards_enabled');
    }

    public function getMemberSessionExpire(): bool
    {
        return $this->getAttribute('member_session_expire');
    }

    public function getAes256KeyPublic(): ?string
    {
        return $this->getAttribute('aes256_key_public');
    }

    public function getHeightBaseline(): int
    {
        return $this->getAttribute('height_baseline');
    }

    public function getShoulderHeightBaseline(): int
    {
        return $this->getAttribute('shoulder_height_baseline');
    }

    public function getMaxActivitats(): ?int
    {
        return $this->getAttribute('max_activitats');
    }

    public function getMaxActuacions(): ?int
    {
        return $this->getAttribute('max_actuacions');
    }

    public function getMaxAssaigs(): ?int
    {
        return $this->getAttribute('max_assaigs');
    }

    public function getLanguage(): ?string
    {
        return $this->getAttribute('language');
    }

    public function getMaxEvents($typeEvent): ?int
    {
        switch ($typeEvent) {
            case EventTypeNameEnum::Actuacio()->value():
                return $this->getMaxActuacions();
            case EventTypeNameEnum::Assaig()->value():
                return $this->getMaxAssaigs();
            case EventTypeNameEnum::Activitat()->value():
                return $this->getMaxActivitats();
            default:
                return $this->getMaxActuacions();
        }
    }

    public function getMemberEditPersonalData(): bool
    {
        return $this->getAttribute('member_edit_personal');
    }

    public function getGoogleCalendarEnabledActivitats(): bool
    {
        return $this->getAttribute('google_calendar_activitats');
    }

    public function getGoogleCalendarEnabledActuacions(): bool
    {
        return $this->getAttribute('google_calendar_actuacions');
    }

    public function getGoogleCalendarEnabledAssaigs(): bool
    {
        return $this->getAttribute('google_calendar_assaigs');
    }

    public function getTOTPTokenExpiration(): int
    {
        return $this->getAttribute('totp_token_expiration');
    }
}
