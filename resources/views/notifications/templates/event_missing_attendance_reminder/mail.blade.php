@php
    $emojis = new App\Helpers\BotmanEmojisHelper();
@endphp

<tr><td>
{{ trans('botman.reminders_message', ['name' => $casteller?->getDisplayName() ?? '<Nom Casteller>' ]) }}
<br><br></td></tr>
<tr>
    <td class="one-column baby-blue-block" style="border-radius: 9px; background-color: #e8f2ff; display: flex; padding: 15px; flex-wrap: wrap; justify-content: center; margin-bottom: 16px; align-items: center;">
        <table width="100%" style="border-spacing: 0;">
        <tr>
            <td class="inner contents" style="padding: 0 0px; width: 100%; text-align: left; margin-bottom: 10px;" width="100%" align="left">
            <p class="h2" style="margin: 0; font-family: sans-serif; font-size: 18px; font-weight: bold; line-height: 1.2; margin-bottom: 10px; margin-top: 12px;"></p>
            <div class="body-access-web" style="display: flex; align-items: flex-start; justify-content: space-between;">
            <p style="margin: 0; font-family: sans-serif; margin-bottom: 10px; width: 100%; font-size: 15px; color: #666666; display: flex; flex-direction: column;">
                {{ $emojis->getEmoji('eventsRightArrow') }}  {{ $eventName }}
<br>
{{ $emojis->getEmoji('eventsDate') }}  {{ $eventStartDate }}
</p><p>
            </p>

            </div>
            <p style="margin: 0; font-family: sans-serif; margin-bottom: 10px;"></p>

            </td>
        </tr>
        </table>
    </td>
</tr>
<tr><td>
<br>
{{ $customMessage }}
<br><br><br><br>
</td></tr>
<tr><td>
{{ $emojis->getEmoji('speaking') }} {{ trans('botman.written_by') }}{{ $notification->user?->getName() ?? '<Usuari admin>' }}
<br><br>
</td></tr>
