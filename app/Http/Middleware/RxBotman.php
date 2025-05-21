<?php

namespace App\Http\Middleware;

use App\Services\BotmanService;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class RxBotman implements Received
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {

        $botmanDriver = $bot->getDriver();
        $botmanService = new BotmanService($botmanDriver);
        $botmanUser = $botmanService->getValidUser($botmanDriver, $message);

        if (is_null($botmanUser)) {
            return $next($message);
        }

        $botmanUserId = $botmanUser->getId();

        $userInformation = $bot->userStorage()->find($botmanUserId);

        if ($language = $userInformation->get('lang')) {
            $locale = $language;
        } else {

            $activeCasteller = $botmanService->getActiveCasteller($botmanUser);
            $locale = env('USER_LANG', \App\Enums\Lang::getLangKey(\App\Enums\Lang::CA));

            if (! is_null($activeCasteller) && $activeCasteller->getLanguage()) {
                $locale = $activeCasteller->getLanguage();
            }
            if (! is_null($activeCasteller) && ! $activeCasteller->getLanguage() && $activeCasteller->getColla()->getConfig()->getLanguage()) {
                $locale = $activeCasteller->getColla()->getConfig()->getLanguage();
            }

            $bot->userStorage()->save([
                'lang' => $locale,
            ], $botmanUserId);
        }

        app()->setLocale($locale);

        return $next($message);
    }
}
