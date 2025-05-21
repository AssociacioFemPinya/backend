<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Token TOTP Language Lines - Català
    |--------------------------------------------------------------------------
    |
    | Les següents línies d'idioma s'utilitzen en la funcionalitat de
    | verificació d'assistència mitjançant tokens TOTP.
    |
    */

    // Vista de verificació (esdeveniments/administració)
    'verification_code' => 'Codi de verificació',
    'code_changes' => 'Aquest codi canvia cada :seconds segons',
    'code_static' => 'Aquest codi no caduca',
    'manual_verification' => 'Verificació manual d\'assistència',

    // Formulari de verificació (membres)
    'verify_attendance' => 'Verificació d\'assistència',
    'verify_attendance_to_event' => 'Verifica l\' assistència a l\'esdeveniment',
    'event' => 'Esdeveniment',
    'select_event' => 'Selecciona un esdeveniment',
    'enter_code' => 'Introdeix el codi de 6 dígits',
    'verify_button' => 'Verificar',

    // Missatges
    'success_verified' => 'La teva assistència ha estat verificada correctament per a l\'esdeveniment: :event',
    'invalid_code' => 'El codi de verificació no és vàlid o ha caducat. Si us plau, torna-ho a provar.',
];
