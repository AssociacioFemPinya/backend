{
    "message": {
        "notification": {
            "title": "{{trans('notifications.firebase_new_reminder_received')}} {{ $eventName }}",
            "body": "{{ $customMessage }}"
        },
        "data": {
            "action_url": "event",
            "resource_id": "{{ $eventId }}"
        }
    }
}
