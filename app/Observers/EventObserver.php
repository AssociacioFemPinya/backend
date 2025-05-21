<?php

namespace App\Observers;

use App\Event;
use App\Multievent;

class EventObserver
{
    /**
     * Handle the Event "saving" event.
     *
     * @return void
     */
    public function saving(Event $event)
    {
        if ($event->id_multievent) {
            $multievent = Multievent::find($event->getMultieventId());

            if ($multievent) {
                if (
                    $event->isDirty('name') && $event->getName() != $multievent->getName() ||
                    $event->isDirty('address') && $event->getAddress() != $multievent->getAddress() ||
                    $event->isDirty('location_link') && $event->getLocationLink() != $multievent->getLocationLink() ||
                    $event->isDirty('comments') && $event->getComments() != $multievent->getComments() ||
                    $event->isDirty('duration') && $event->getDuration() != $multievent->getDuration() ||
                    $event->isDirty('companions') && $event->getCompanions() != $multievent->getCompanions() ||
                    $event->isDirty('visibility') && $event->getVisibility() != $multievent->getVisibility() ||
                    $event->isDirty('type') && $event->getType() != $multievent->getType() ||
                    $event->isDirty('photo') && $event->getPhoto() != $multievent->getPhoto() ||
                    $event->isDirty('start_date') && $event->getStartDate()->format('H:i:s') != $multievent->time
                ) {
                    $event->id_multievent = null;
                }
            }
        }
    }
}
