<?php

declare(strict_types=1);

namespace App\Factories;

use App\CollaConfig;
use Symfony\Component\HttpFoundation\ParameterBag;

final class CollaConfigFactory
{
    public static function make(int $collaId, ParameterBag $bag): CollaConfig
    {
        $config = new CollaConfig();
        $config->setAttribute('colla_id', $collaId);

        return self::update($config, $bag);
    }

    public static function update(CollaConfig $config, ParameterBag $bag): CollaConfig
    {
        if ($bag->has('translation_activitat')) {
            if ($translation_activitat = $bag->get('translation_activitat')) {
                $config->setAttribute('translation_activitat', $translation_activitat);
            } else {
                $config->setAttribute('translation_activitat', '');
            }
        }

        if ($bag->has('translation_activitat')) {
            if ($translation_actuacio = $bag->get('translation_actuacio')) {
                $config->setAttribute('translation_actuacio', $translation_actuacio);
            } else {
                $config->setAttribute('translation_actuacio', '');
            }

        }

        if ($bag->has('translation_assaig')) {
            if ($translation_assaig = $bag->get('translation_assaig')) {
                $config->setAttribute('translation_assaig', $translation_assaig);
            } else {
                $config->setAttribute('translation_assaig', '');
            }
        }

        if ($bag->has('max_activitats')) {
            if ($max_activitats = $bag->get('max_activitats')) {
                $config->setAttribute('max_activitats', $max_activitats);
            }
        }

        if ($bag->has('max_actuacions')) {
            if ($max_actuacions = $bag->get('max_actuacions')) {
                $config->setAttribute('max_actuacions', $max_actuacions);
            }
        }

        if ($bag->has('max_assaigs')) {
            if ($max_assaigs = $bag->get('max_assaigs')) {
                $config->setAttribute('max_assaigs', $max_assaigs);
            }
        }

        if ($bag->has('boards_enabled')) {
            if ($boards_enabled = $bag->getBoolean('boards_enabled')) {
                $config->setAttribute('boards_enabled', $boards_enabled);
            } else {
                $config->setAttribute('boards_enabled', false);
            }
        }

        if ($bag->has('public_display_enabled')) {
            if ($public_display_enabled = $bag->getBoolean('public_display_enabled')) {
                $config->setAttribute('public_display_enabled', $public_display_enabled);
            } else {
                $config->setAttribute('public_display_enabled', false);
            }
        }

        if ($bag->has('member_session_expire')) {
            if ($member_session_expire = $bag->getBoolean('member_session_expire')) {
                $config->setAttribute('member_session_expire', $member_session_expire);
            } else {
                $config->setAttribute('member_session_expire', false);
            }
        }

        if ($bag->has('member_edit_personal')) {
            if ($member_edit_personal = $bag->getBoolean('member_edit_personal')) {
                $config->setAttribute('member_edit_personal', $member_edit_personal);
            } else {
                $config->setAttribute('member_edit_personal', false);
            }
        }

        if ($bag->has('aes256_key_public')) {
            if ($aes256_key_public = $bag->get('aes256_key_public')) {
                $config->setAttribute('aes256_key_public', $aes256_key_public);
            } else {
                $config->setAttribute('aes256_key_public', null);
            }
        }

        if ($bag->has('height_baseline')) {
            if ($height_baseline = $bag->get('height_baseline')) {
                $config->setAttribute('height_baseline', $height_baseline);
            } else {
                $config->setAttribute('height_baseline', 0);
            }
        }

        if ($bag->has('shoulder_height_baseline')) {
            if ($shoulder_height_baseline = $bag->get('shoulder_height_baseline')) {
                $config->setAttribute('shoulder_height_baseline', $shoulder_height_baseline);
            } else {
                $config->setAttribute('shoulder_height_baseline', 0);
            }
        }

        if ($bag->has('language')) {
            if ($language = $bag->get('language')) {
                $config->setAttribute('language', $language);
            }
        }

        if ($bag->has('google_calendar_activitats')) {
            if ($google_calendar_activitats = $bag->getBoolean('google_calendar_activitats')) {
                $config->setAttribute('google_calendar_activitats', $google_calendar_activitats);
            } else {
                $config->setAttribute('google_calendar_activitats', false);
            }
        }

        if ($bag->has('google_calendar_actuacions')) {
            if ($google_calendar_actuacions = $bag->getBoolean('google_calendar_actuacions')) {
                $config->setAttribute('google_calendar_actuacions', $google_calendar_actuacions);
            } else {
                $config->setAttribute('google_calendar_actuacions', false);
            }
        }

        if ($bag->has('google_calendar_assaigs')) {
            if ($google_calendar_assaigs = $bag->getBoolean('google_calendar_assaigs')) {
                $config->setAttribute('google_calendar_assaigs', $google_calendar_assaigs);
            } else {
                $config->setAttribute('google_calendar_assaigs', false);
            }
        }

        if ($bag->has('totp_token_expiration')) {
            if ($totp_token_expiration = $bag->get('totp_token_expiration')) {
                $config->setAttribute('totp_token_expiration', $totp_token_expiration);
            } else {
                $config->setAttribute('totp_token_expiration', 0);
            }
        }

        return $config;
    }
}
