<?php

declare(strict_types=1);

namespace App\Helpers\Encryptors;

interface IEncryptor
{
    public function encrypt(string $data): string;

    public function decrypt(string $data): ?string;
}
