<?php

declare(strict_types=1);

namespace App\Services;

use App\Casteller;

class TelegramNotificator
{
    public function __construct() {}

    /**
     * Sends a message to a Casteller.
     *
     * @return void
     */
    public function send(Casteller $casteller, string $message): bool
    {
        $telegram_bot_token = config('botman.telegram')['telegram']['token'];
        $telegram_chat_id = $casteller->getCastellerTelegram()->getTelegramId();

        $url = "https://api.telegram.org/bot$telegram_bot_token/sendMessage";
        $data = [
            'chat_id' => $telegram_chat_id,
            'text' => $message,
            'parse_mode' => 'markdown',
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data),
                'ignore_errors' => true,
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result);

        // TODO: Better error handling
        if (! $response->ok) {
            return true;
        }

        return false;
    }
}
