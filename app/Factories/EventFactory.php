<?php

declare(strict_types=1);

namespace App\Factories;

use App\Enums\TypeTags;
use App\Event;
use App\Tag;
use Symfony\Component\HttpFoundation\ParameterBag;

class EventFactory
{
    public static function make(int $collaId, ParameterBag $bag): Event
    {
        $event = new Event();

        $event->setAttribute('colla_id', $collaId);

        return self::update($event, $bag);
    }

    public static function update(Event $event, ParameterBag $bag): Event
    {

        if ($bag->has('id_event_external')) {
            $event->setAttribute('id_event_external', $bag->getInt('id_event_external'));
        }

        if ($bag->has('name')) {
            $event->setAttribute('name', $bag->get('name'));
        }

        if ($bag->has('address')) {
            $event->setAttribute('address', $bag->get('address'));
        }

        if ($bag->has('location_link')) {
            $event->setAttribute('location_link', $bag->get('location_link'));
        }

        if ($bag->has('comments')) {
            $event->setAttribute('comments', $bag->get('comments'));
        }

        if ($bag->has('duration')) {
            $event->setAttribute('duration', $bag->getInt('duration'));
        }

        if ($bag->has('start_date')) {
            $event->setAttribute('start_date', $bag->get('start_date'));
        }

        if ($bag->has('open_date')) {
            $event->setAttribute('open_date', $bag->get('open_date'));
        }

        if ($bag->has('close_date')) {
            $event->setAttribute('close_date', $bag->get('close_date'));
        }

        if ($bag->has('companions')) {
            $event->setAttribute('companions', $bag->get('companions'));
        }

        if ($bag->has('visibility')) {
            $event->setAttribute('visibility', $bag->getInt('visibility'));
        }

        if ($bag->has('type')) {
            $event->setAttribute('type', $bag->getInt('type'));
        }

        // we need to save the empty event at the beginning to be able to set a tag on it
        $event->save();

        if ($bag->has('tags')) {
            self::addOrUpdateTags($event, $bag->get('tags'));
        } else {
            self::addOrUpdateTags($event, []);
        }

        if ($bag->has('tags_casteller')) {
            self::addOrUpdateCastellerTags($event, $bag->get('tags_casteller'));
        } else {
            self::addOrUpdateCastellerTags($event, []);
        }

        if ($bag->has('answers')) {
            self::addOrUpdateAttendanceAnswers($event, $bag->get('answers'));
        } else {
            self::addOrUpdateAttendanceAnswers($event, []);
        }

        return $event;
    }

    private static function addOrUpdateTags(Event $event, ?array $tags)
    {

        if ($event->hasTags()) {
            self::removeTagsFromEvent($event);
        }

        if (! empty($tags)) {
            foreach ($tags as $tag) {
                self::addTagToEvent($event, $tag);
            }
        }
    }

    private static function addOrUpdateCastellerTags(Event $event, ?array $tags)
    {

        if ($event->hasCastellerTags()) {
            self::removeCastellerTagsFromEvent($event);
        }

        if (! empty($tags)) {
            foreach ($tags as $tag) {
                self::addCastellerTagToEvent($event, $tag);
            }
        }
    }

    private static function addOrUpdateAttendanceAnswers(Event $event, ?array $tags)
    {

        if ($event->hasAttendanceAnswers()) {
            self::removeAttendanceAnswerFromEvent($event);
        }

        if (! empty($tags)) {
            foreach ($tags as $tag) {
                self::addAttendanceAnswerToEvent($event, $tag);
            }
        }
    }

    private static function removeTagsFromEvent(Event $event)
    {
        foreach ($event->getTags() as $tag) {
            $event->removeTag($tag);
        }
    }

    private static function removeCastellerTagsFromEvent(Event $event)
    {
        foreach ($event->getCastellerTags() as $tag) {
            $event->removeCastellerTag($tag);
        }
    }

    private static function removeAttendanceAnswerFromEvent(Event $event)
    {
        foreach ($event->getAttendanceAnswers() as $tag) {
            $event->removeAttendanceAnswer($tag);
        }
    }

    private static function addTagToEvent(Event $event, string $tag): void
    {
        /** @var Tag $tag */
        $tag = Tag::currentTags(TypeTags::EVENTS, $event->getColla())->where('value', $tag)->first();

        $existingTag = $event->getTags()->find($tag->getId());
        if (! $existingTag) {
            $event->tags()->save($tag);

        }
    }

    private static function addCastellerTagToEvent(Event $event, string $tag): void
    {
        /** @var Tag $tag */
        $tag = Tag::currentTags(TypeTags::CASTELLERS, $event->getColla())->where('value', $tag)->first();

        $existingTag = $event->getCastellerTags()->find($tag->getId());
        if (! $existingTag) {
            $event->castellerTags()->save($tag);

        }
    }

    private static function addAttendanceAnswerToEvent(Event $event, string $tag): void
    {
        /** @var Tag $tag */
        $tag = Tag::currentTags(TypeTags::ATTENDANCE, $event->getColla())->where('value', $tag)->first();

        $existingTag = $event->getAttendanceAnswers()->find($tag->getId());
        if (! $existingTag) {
            $event->attendanceAnswers()->save($tag);

        }
    }
}
