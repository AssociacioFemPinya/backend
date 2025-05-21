<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Token TOTP Language Lines - Français
    |--------------------------------------------------------------------------
    |
    | Les lignes de langue suivantes sont utilisées dans la fonctionnalité
    | de vérification de présence à l'aide de jetons TOTP.
    |
    */

    // Vue de vérification (événements/administration)
    'verification_code' => 'Code de vérification',
    'code_changes' => 'Ce code change toutes les :seconds secondes',
    'code_static' => 'Ce code n\'expire jamais',
    'manual_verification' => 'Vérification manuelle de présence',

    // Formulaire de vérification (membres)
    'verify_attendance' => 'Vérifier la présence',
    'verify_attendance_to_event' => 'Vérifier la présence à l\'événement',
    'event' => 'Événement',
    'select_event' => 'Sélectionnez un événement',
    'enter_code' => 'Entrez le code à 6 chiffres',
    'verify_button' => 'Vérifier',

    // Messages
    'success_verified' => 'Votre présence a été vérifiée avec succès pour l\'événement: :event',
    'invalid_code' => 'Le code de vérification n\'est pas valide ou a expiré. Veuillez réessayer.',
];
