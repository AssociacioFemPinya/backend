<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Token TOTP Language Lines - English
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in the attendance verification
    | functionality using TOTP tokens.
    |
    */

    // Verification view (events/administration)
    'verification_code' => 'Verification code',
    'code_changes' => 'This code changes every :seconds seconds',
    'code_static' => 'This code never expires',
    'manual_verification' => 'Manual attendance verification',

    // Verification form (members)
    'verify_attendance' => 'Verify attendance',
    'verify_attendance_to_event' => 'Verify attendance to event',
    'event' => 'Event',
    'select_event' => 'Select an event',
    'enter_code' => 'Enter the 6-digit code',
    'verify_button' => 'Verify',

    // Messages
    'success_verified' => 'Your attendance has been successfully verified for the event: :event',
    'invalid_code' => 'The verification code is not valid or has expired. Please try again.',
];
