<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CastellerConfig extends Model
{
    protected $table = 'casteller_config';

    protected $primaryKey = 'id_casteller_config';

    public $timestamps = true;

    protected $casts = [
        'last_access_at' => 'datetime',
        'last_credentials_sent_at' => 'datetime',
    ];

    protected $events = [
        'updated' => CastellerConfigListener::class,
    ];

    // Relations

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

    public function getApiToken(): ?string
    {
        return $this->getAttribute('api_token');
    }

    public function getApiTokenEnabled(): int
    {
        return $this->getAttribute('api_token_enabled');
    }

    public function getTelegramToken(): ?string
    {
        return $this->getAttribute('telegram_token');
    }

    public function getTelegramEnabled(): int
    {
        return $this->getAttribute('telegram_enabled');
    }

    public function getAuthTokenEnabled(): int
    {
        return $this->getAttribute('auth_token_enabled');
    }

    public function hasAnyAuthEnabled(): bool
    {
        return
            boolval($this->getTelegramEnabled()) ||
            boolval($this->getApiTokenEnabled()) ||
            boolval($this->getAuthTokenEnabled());
    }

    public function getTecnica(): int
    {
        return $this->getAttribute('tecnica');
    }

    public function getLastAccessAt(): ?Carbon
    {
        return $this->getAttribute('last_access_at');
    }

    public function getCredentialsSentAt(): ?Carbon
    {
        return $this->getAttribute('last_credentials_sent_at');
    }

    public function getWebUrl(): string
    {
        $casteller = $this->getCasteller();
        $url = env('DOMAIN').'/member/login?auth_token='.$casteller->authConfig->getAuthToken();

        return $url;
    }
}
