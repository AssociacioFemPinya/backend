<?php

namespace App\Http\Controllers;

use App\Conversations\FempinyaConversation;
use App\Conversations\UnlinkedConversation;
use App\Http\Middleware\RxBotman;
use App\Services\BotmanService;
use Carbon\Carbon;

class BotManController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        $botman->hears(
            '{message}',
            function ($bot) {

                $botmanDriver = $bot->getDriver();

                $botmanService = new BotmanService($botmanDriver);

                $botmanUser = $botmanService->getValidUser($botmanDriver, $bot->getMessage());

                if (is_null($botmanUser)) {
                    return;
                }

                // We check for the active casteller of the main casteller (castellerTelegram) linked to this Telegram Account
                $activeCasteller = $botmanService->getActiveCasteller($botmanUser);
                if (is_null($activeCasteller)) {
                    $bot->startConversation(new UnlinkedConversation($botmanDriver));
                } else {
                    $castellerConfig = $activeCasteller->getCastellerConfig();
                    //check if active casteller has telegram enabled
                    if ($castellerConfig->getTelegramEnabled() == 0) {
                        $bot->reply(__('botman.conversation_inactive_casteller', ['nameCasteller' => $activeCasteller->getDisplayName()]));
                    } else {
                        $castellerConfig->last_access_at = Carbon::now()->toDateTimeString();
                        $castellerConfig->save();
                        $bot->startConversation(new FempinyaConversation($botmanDriver, $activeCasteller));
                    }
                }

            }
        );

        $middleware = new RxBotman();
        $botman->middleware->received($middleware);

        $botman->listen();
    }

    /**
     * Used for testing on a web environment
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('botman.tinker-nostudio');
    }
}
