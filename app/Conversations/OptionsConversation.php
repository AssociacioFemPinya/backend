<?php

namespace App\Conversations;

use App\Casteller;
use App\CastellerTelegram;
use App\Enums\Lang;
use App\Enums\TelegramInteractionType;
use App\Helpers\BotmanEmojisHelper;
use App\Helpers\BotmanHelper;
use App\Services\BotmanService;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\TelegramDriver;

class OptionsConversation extends Conversation
{
    protected Casteller $casteller;

    protected string $lb;

    protected DriverInterface $botmanDriver;

    protected string $option;

    protected BotmanEmojisHelper $botmanEmojisHelper;

    public function __construct(DriverInterface $botmanDriver, Casteller $casteller, string $option = '')
    {

        $this->lb = BotmanHelper::getLbTag($botmanDriver);
        $this->botmanDriver = $botmanDriver;
        $this->casteller = $casteller;
        $this->option = $option;
        $this->botmanEmojisHelper = new BotmanEmojisHelper();
    }

    public function setCasteller(Casteller $casteller)
    {
        $this->casteller = $casteller;
    }

    private function reloadCasteller()
    {
        $casteller = BotmanService::reloadCasteller($this->casteller);
        $this->casteller = $casteller;
    }

    /* TODO: add the missing options and functions on the OPTIONS menu */
    public function run()
    {
        switch ($this->option) {
            case $this->botmanEmojisHelper->getEmoji('optionsLinkingCastellers'):
            case __('botman.optionsLinkingCastellers'):
                $this->askLinkedCastellers();
                break;
            case $this->botmanEmojisHelper->getEmoji('optionsURLMember'):
            case __('botman.optionsURLMember'):
                $this->getURLMember();
                break;
            case $this->botmanEmojisHelper->getEmoji('optionsLanguage'):
            case __('botman.optionsLanguage'):
                $this->askLanguages();
                break;
            case $this->botmanEmojisHelper->getEmoji('optionsSwitchTextEmojis'):
            case __('botman.optionsSwitchTextEmojis'):
                $this->askTextEmoji();
                break;
            case $this->botmanEmojisHelper->getEmoji('optionsLogOut'):
            case __('botman.optionsLogOut'):
                $this->askLogOut();
                break;
            default:
                $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuHome'));
        }
    }

    private function askLinkedCastellers()
    {

        $botmanService = new BotmanService($this->botmanDriver);

        // we get the main casteller (castellerTelegram) linked to this Telegram Account
        $castellerLinkedToTelegram = $botmanService->getLinkedCasteller($this->getBot()->getUser());

        // we get linked casteller to that main casteller
        $linkedCastellersExcludeActive = $castellerLinkedToTelegram->getLinkedCastellersExcludeActive();

        $message = trans_choice('botman.conversation_options_ask_linked_castellers_number_of_casteller', $linkedCastellersExcludeActive['all']).$this->lb.$this->lb;

        if ($linkedCastellersExcludeActive['all']->isEmpty()) {
            $rows[] = $botmanService->getLinkingCastellersMenu($this->casteller);
        } else {
            $message .= BotmanHelper::getLinkedCastellersFormatted($linkedCastellersExcludeActive['all'], $this->botmanDriver)['message'];
            $rows[] = $botmanService->getLinkingCastellersMenu($this->casteller, true);
        }

        $this->getBot()->reply($message, ['parse_mode' => 'Markdown']).$this->lb;

        $optionsMenu = $botmanService->getOptionsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $optionsMenu;
        $rows[] = $mainMenu;

        $questionText = trans_choice('botman.conversation_options_ask_linked_castellers_question', $linkedCastellersExcludeActive['all']);

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService, $mainMenu, $optionsMenu) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $optionsMenu)) {
                    return $this->manageCurrentMenu($answer);
                } else {
                    switch ($answer) {
                        case $this->botmanEmojisHelper->getEmoji('optionsLinkCasteller'):
                        case __('botman.optionsLinkCasteller'):
                            return $this->askLinkNewCasteller();

                        case $this->botmanEmojisHelper->getEmoji('optionsSwitchCasteller'):
                        case __('botman.optionsSwitchCasteller'):
                            return $this->askOfferLinkedCastellers();

                        default:
                            return $this->repeat(__('botman.general_use_menu'));
                    }
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );

    }

    public function askLinkNewCasteller()
    {

        // OPTIONS OF THE QUESTION
        $rows[] = [__('general.yes'), __('general.no')];
        $questionText = __('botman.conversation_options_ask_link_new_casteller_question');

        // SERVICE
        $botmanService = new BotmanService($this->botmanDriver);

        // MAIN MENU
        $optionsMenu = $botmanService->getOptionsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $optionsMenu;
        $rows[] = $mainMenu;

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }
        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService, $mainMenu, $optionsMenu) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $optionsMenu)) {
                    return $this->manageCurrentMenu($answer);
                } else {
                    switch ($answer) {
                        case __('general.no'):
                            $this->getBot()->reply(__('botman.conversation_unlinked_ask_link_code_answer_no'));

                            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuOptions'));
                        case __('general.yes'):
                            return $this->askInsertLinkCode();
                        default:
                            return $this->repeat(__('botman.general_use_menu'));
                    }
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askInsertLinkCode()
    {
        $this->ask(
            __('botman.conversation_unlinked_ask_insert_link_code_question'),
            function (Answer $answer) {
                $linkCode = $answer->getText();
                $casteller = Casteller::getCastellerByTelegramToken($linkCode);
                if (is_null($casteller)) {
                    $this->getBot()->reply(__('botman.conversation_unlinked_ask_insert_link_code_answer_unknown_code'));

                    return $this->askWhatelse();
                } elseif ($casteller->getId() === $this->casteller->getId()) {
                    $this->getBot()->reply(__('botman.conversation_options_ask_linsert_link_code_answer_same_casteller'));

                    return $this->askWhatelse();
                } else {
                    // Check if the code is about itself or an already linked casteller
                    if ($this->casteller->linkedCastellers()->where('casteller_id', $casteller->getId())->exists()) {
                        $this->getBot()->reply(__('botman.conversation_options_ask_linsert_link_code_answer_already_linked_casteller'));

                        return $this->askWhatelse();
                    } else {
                        $this->getBot()->reply(__('botman.conversation_unlinked_ask_insert_link_code_answer_correct_code', ['nameCasteller' => $casteller->getDisplayName(), 'nameColla' => $casteller->getColla()->getName()]));

                        return $this->askLinking($casteller);
                    }
                }
            }
        );

    }

    private function askWhatelse()
    {

        $rows[] = [__('general.yes'), __('general.no')];
        $questionText = __('botman.conversation_unlinked_ask_whatelse_question');

        $botmanService = new BotmanService($this->botmanDriver);

        $optionsMenu = $botmanService->getOptionsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $optionsMenu;
        $rows[] = $mainMenu;

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService, $mainMenu, $optionsMenu) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $optionsMenu)) {
                    return $this->manageCurrentMenu($answer);
                } else {
                    switch ($answer) {
                        case __('general.no'):
                            $this->getBot()->reply(__('botman.conversation_options_ask_whatelse_answer_no'));

                            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuOptions'));
                        case __('general.yes'):
                            return $this->askInsertLinkCode();
                        default:
                            return $this->repeat(__('botman.general_use_menu'));
                    }

                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askLinking(Casteller $casteller)
    {

        $rows[] = [__('general.yes'), __('general.no')];
        $questionText = __('botman.conversation_unlinked_ask_linking_question');

        $botmanService = new BotmanService($this->botmanDriver);

        $optionsMenu = $botmanService->getOptionsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $optionsMenu;
        $rows[] = $mainMenu;

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService, $casteller, $mainMenu, $optionsMenu) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $optionsMenu)) {
                    return $this->manageCurrentMenu($answer);
                } else {
                    switch ($answer) {
                        case __('general.no'):
                            $this->getBot()->reply(__('botman.conversation_unlinked_ask_linking_answer_no'));

                            return $this->askWhatelse();
                        case __('general.yes'):
                            $this->getBot()->reply(__('botman.conversation_unlinked_ask_linking_answer_yes'));

                            // we get the main casteller (castellerTelegram) linked to this account
                            $linkedCasteller = $botmanService->getLinkedCasteller($this->bot->getUser());
                            // we attach the new casteller
                            $linkedCasteller->linkedCastellers()->attach($casteller);
                            $linkedCasteller->save();
                            $this->getBot()->reply(__('botman.conversation_options_ask_linking_answer_linked'));

                            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuOptions'));
                        default:
                            return $this->repeat(__('botman.general_use_menu'));
                    }
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );

    }

    private function askOfferLinkedCastellers()
    {
        $castellerTelegram = CastellerTelegram::getCastellerTelegramByTelegramId($this->getBot()->getUser()->getId());
        $linkedCastellersExcludeActive = $castellerTelegram->getCasteller()->getLinkedCastellersExcludeActive();

        $message = trans_choice('botman.conversation_options_ask_linked_castellers_number_of_casteller', $linkedCastellersExcludeActive['all']).$this->lb.$this->lb;

        // if a casteller has Telegram disabled, don't set it as an Option
        $linkedCastellersNamesEnabled = BotmanHelper::getLinkedCastellersFormatted($linkedCastellersExcludeActive['enabled'], $this->botmanDriver);
        $linkedCastellersNamesDisabled = BotmanHelper::getLinkedCastellersFormatted($linkedCastellersExcludeActive['disabled'], $this->botmanDriver);

        $rows[] = $linkedCastellersNamesEnabled['options'];

        $this->getBot()->reply($message.$linkedCastellersNamesEnabled['message'].$this->lb.$linkedCastellersNamesDisabled['message'], ['parse_mode' => 'Markdown']);

        $botmanService = new BotmanService($this->botmanDriver);

        $optionsMenu = $botmanService->getOptionsMenu($this->casteller);
        $mainMenu = $botmanService->getMainMenu($this->casteller);

        $rows[] = $optionsMenu;
        $rows[] = $mainMenu;

        $questionText = __('botman.conversation_options_ask_offer_linked_castellers_offer_question');

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService, $mainMenu, $optionsMenu, $linkedCastellersNamesEnabled, $linkedCastellersExcludeActive) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, $mainMenu)) {
                    return $this->manageMainMenu($answer);
                } elseif (in_array($answer, $optionsMenu)) {
                    return $this->manageCurrentMenu($answer);
                } elseif (in_array($answer, $linkedCastellersNamesEnabled['options'])) {
                    $index = array_search($answer, $linkedCastellersNamesEnabled['options']);
                    $newCastellerActive = $linkedCastellersExcludeActive['enabled'][$index];
                    $castellerTelegram = CastellerTelegram::getCastellerTelegramByTelegramId($this->getBot()->getUser()->getId());
                    $castellerTelegram->setCastellerActiveId($newCastellerActive->getId());
                    $castellerTelegram->save();
                    $this->setCasteller($newCastellerActive);
                    $this->getBot()->reply('DONE!');

                    return $this->manageMainMenu();
                } else {
                    return $this->repeat(__('botman.general_use_menu'));
                }

            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );

    }

    /* TODO: Managing language switcher */
    private function askLanguages()
    {
        $rows[] = Lang::getTypes();
        $questionText = __('botman.general_menu_languages');

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $botmanService = new BotmanService($this->botmanDriver);

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService) {
                $answer = $botmanService->getAnswer($answer);
                if (in_array($answer, Lang::getTypes())) {
                    $lang = Lang::getLangKey($answer);
                    $this->bot->userStorage()->save([
                        'lang' => $lang,
                    ], $this->bot->getUser()->getId());
                    app()->setLocale($lang);
                    $this->casteller->setLanguage($lang);
                    $this->casteller->save();
                }

                return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuHome'));
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );

    }

    private function askTextEmoji()
    {
        $rows[] = [__('botman.conversation_unlinked_emoji'), __('botman.conversation_unlinked_text')];
        $questionText = __('botman.conversation_unlinked_ask_emoji_text');

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $botmanService = new BotmanService($this->botmanDriver);

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService) {
                $answer = $botmanService->getAnswer($answer);
                if ($answer === __('botman.conversation_unlinked_emoji')) {
                    $this->casteller->setInteractionType(TelegramInteractionType::ICON);
                    $this->casteller->save();
                } elseif ($answer === __('botman.conversation_unlinked_text')) {
                    $this->casteller->setInteractionType(TelegramInteractionType::TEXT);
                    $this->casteller->save();
                }

                return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuHome'));
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function askLogOut()
    {
        $rows[] = [__('general.yes'), __('general.no')];
        $questionText = $this->botmanEmojisHelper->getEmoji('warning').__('botman.conversation_options_ask_log_out');

        if ($this->botmanDriver instanceof TelegramDriver) {
            $buttonsQuestion = [];
            $buttonsKeyboard = $rows;
        } else {
            $buttonsQuestion = $rows;
            $buttonsKeyboard = [];
        }

        $botmanService = new BotmanService($this->botmanDriver);

        $this->ask(
            $botmanService->getQuestion($questionText, $buttonsQuestion),
            function (Answer $answer) use ($botmanService) {
                $answer = $botmanService->getAnswer($answer);
                switch ($answer) {
                    case __('general.yes'):
                        $this->casteller->linkedCastellers()->detach();
                        $castellerTelegram = CastellerTelegram::getCastellerTelegramByTelegramId($this->getBot()->getUser()->getId());
                        $castellerTelegram->delete();

                        return $this->getBot()->startConversation(new UnlinkedConversation($this->botmanDriver));
                    case __('general.no'):
                        return $this->manageMainMenu();
                    default:
                        return $this->repeat(__('botman.general_use_menu'));
                }
            }, $botmanService->getKeyBoard($buttonsKeyboard)
        );
    }

    private function getURLMember()
    {
        $this->reloadCasteller();

        $casteller_config = $this->casteller->getCastellerConfig();

        if ($casteller_config->getAuthTokenEnabled()) {

            $url = $casteller_config->getWebUrl();
            $message = __('botman.optionsURLMemberClick').$this->lb;
            $message .= BotmanHelper::setLink($this->botmanDriver, __('botman.optionsURLMember'), $url);
            $this->getBot()->reply($message, ['parse_mode' => 'Markdown']);

            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuOptions'));

        } else {

            $message = $this->lb.__('botman.optionsForbiddenURLMemberClick').' ';
            $this->getBot()->reply($message);

            return $this->manageMainMenu($this->botmanEmojisHelper->getEmoji('mainMenuOptions'));
        }
    }

    private function manageMainMenu(string $answer = '')
    {
        return $this->getBot()->startConversation(new FempinyaConversation($this->botmanDriver, $this->casteller, $answer));
    }

    private function manageCurrentMenu(string $answer = '')
    {
        return $this->getBot()->startConversation(new OptionsConversation($this->botmanDriver, $this->casteller, $answer));
    }
}
