<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Token TOTP Language Lines - Español
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de idioma se utilizan en la funcionalidad de
    | verificación de asistencia mediante tokens TOTP.
    |
    */

    // Vista de verificación (eventos/administración)
    'verification_code' => 'Código de verificación',
    'code_changes' => 'Este código cambia cada :seconds segundos',
    'code_static' => 'Este código no caduca',
    'manual_verification' => 'Verificación manual de asistencia',

    // Formulario de verificación (miembros)
    'verify_attendance' => 'Verificar asistencia',
    'verify_attendance_to_event' => 'Verifica la asistencia al evento',
    'event' => 'Evento',
    'select_event' => 'Selecciona un evento',
    'enter_code' => 'Ingresa el código de 6 dígitos',
    'verify_button' => 'Verificar',

    // Mensajes
    'success_verified' => 'Tu asistencia ha sido verificada correctamente para el evento: :event',
    'invalid_code' => 'El código de verificación no es válido o ha expirado. Por favor, intenta nuevamente.',
];
