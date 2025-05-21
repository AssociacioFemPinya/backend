<?php

declare(strict_types=1);

namespace App\Helpers\Encryptors;

abstract class Aes256
{
    protected const ALG = 'AES-256-CBC';

    protected const OPTIONS = 0;

    private string $key;

    private string $iv;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->iv = '7439430567630598';
    }

    protected function key(): string
    {
        return $this->key;
    }

    protected function iv(): string
    {
        return $this->iv;
    }

    protected function alg(): string
    {
        return Aes256::ALG;
    }

    protected function options(): int
    {
        return Aes256::OPTIONS;
    }
}
