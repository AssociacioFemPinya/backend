<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AuthConfig extends Authenticatable
{
    protected $primaryKey = 'id_auth_config';

    protected $fillable = [
        'casteller_id',
    ];

    /** get authConfig id */
    public function getId(): int
    {
        return $this->getAttribute('id_auth_config');
    }

    /** get authConfig related casteller */
    public function casteller()
    {
        return $this->belongsTo(Casteller::class, 'casteller_id', 'id_casteller');
    }

    /** get authConfig token */
    public function getAuthToken(): ?string
    {
        return $this->getAttribute('auth_token');
    }

    public function getColla(): Colla
    {
        return $this->casteller->getColla('colla');
    }

    // TODO: Add language configuration in castellers, and call casteller->getLanguage()
    public function getLanguage(): string
    {
        return 'ca';
    }
}
