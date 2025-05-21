
@php
    $emojis = new App\Helpers\BotmanEmojisHelper();
@endphp

{{ $emojis->getEmoji('eventsRightArrow') }} {{trans('botman.reminders')}}
{{ trans('botman.reminders_message', ['name' => $casteller?->getDisplayName() ?? 'Usuari' ]) }}

{{ $emojis->getEmoji('eventsRightArrow') }}{{ $eventName }}
{{ $emojis->getEmoji('eventsDate') }}{{ $eventStartDate }}
{{ $emojis->getEmoji('eventsCompanions') }}{{ $casteller?->getName() ?? 'Usuari' }}

{{ $emojis->getEmoji('speaking') }} {{ trans('botman.written_by') }}{{ $notification->user?->getName() ?? 'Usuari' }}

{{ $customMessage }}
