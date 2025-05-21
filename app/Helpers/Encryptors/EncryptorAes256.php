<?php

declare(strict_types=1);

namespace App\Helpers\Encryptors;

final class EncryptorAes256 extends Aes256 implements IEncryptor
{
    public function decrypt(string $data): ?string
    {
        $decoded = base64_decode($data);

        if ($res = openssl_decrypt($decoded, $this->alg(), $this->key(), $this->options(), $this->iv())) {

            return $res;
        }

        return null;
    }

    public function encrypt(string $data): string
    {
        $encrypted = openssl_encrypt(
            $data,
            $this->alg(),
            $this->key(),
            $this->options(),
            $this->iv(),
        );

        return base64_encode($encrypted);
    }
}
