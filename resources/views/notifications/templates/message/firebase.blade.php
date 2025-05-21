{
    "message": {
        "notification": {
            "title": "{{trans('notifications.firebase_new_message_received')}}",
            "body": ""
        },
        "data": {
            "action_url": "notification",
            "resource_id": "{{ $notification->getId() }}"
        }
    }
}
