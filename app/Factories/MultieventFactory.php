<?php

declare(strict_types=1);

namespace App\Factories;

use App\Enums\TypeTags;
use App\Multievent;
use App\Tag;
use Symfony\Component\HttpFoundation\ParameterBag;

class MultieventFactory
{
    public static function make(int $collaId, ParameterBag $bag): Multievent
    {
        $multievent = new Multievent();
        $multievent->setAttribute('colla_id', $collaId);

        return self::update($multievent, $bag);
    }

    public static function update(Multievent $multievent, ParameterBag $bag): Multievent
    {
        if ($bag->has('name')) {
            $multievent->setAttribute('name', $bag->get('name'));
        }

        if ($bag->has('address')) {
            $multievent->setAttribute('address', $bag->get('address'));
        }

        if ($bag->has('location_link')) {
            $multievent->setAttribute('location_link', $bag->get('location_link'));
        }

        if ($bag->has('comments')) {
            $multievent->setAttribute('comments', $bag->get('comments'));
        }

        if ($bag->has('duration')) {
            $multievent->setAttribute('duration', $bag->getInt('duration'));
        }

        if ($bag->has('time')) {
            $multievent->setAttribute('time', $bag->get('time'));
        }

        if ($bag->has('companions')) {
            $multievent->setAttribute('companions', $bag->get('companions'));
        }

        if ($bag->has('visibility')) {
            $multievent->setAttribute('visibility', $bag->getInt('visibility'));
        }

        if ($bag->has('type')) {
            $multievent->setAttribute('type', $bag->getInt('type'));
        }

        if ($bag->has('photo')) {
            $multievent->setAttribute('photo', $bag->get('photo'));
        }

        $multievent->save();

        if ($bag->has('tags')) {
            self::addOrUpdateTags($multievent, $bag->get('tags'));
        } else {
            self::addOrUpdateTags($multievent, []);
        }

        if ($bag->has('tags_casteller')) {
            self::addOrUpdateCastellerTags($multievent, $bag->get('tags_casteller'));
        } else {
            self::addOrUpdateCastellerTags($multievent, []);
        }

        return $multievent;
    }

    private static function addOrUpdateTags(Multievent $multievent, ?array $tags)
    {
        if ($multievent->hasTags()) {
            self::removeTagsFromMultievent($multievent);
        }

        if (! empty($tags)) {
            foreach ($tags as $tag) {
                self::addTagToMultievent($multievent, $tag);
            }
        }
    }

    private static function removeTagsFromMultievent(Multievent $multievent)
    {
        foreach ($multievent->getTags() as $tag) {
            $multievent->removeTag($tag);
        }
    }

    private static function addTagToMultievent(Multievent $multievent, string $tag): void
    {
        /** @var Tag $tag */
        $tag = Tag::currentTags(TypeTags::EVENTS, $multievent->getColla())->where('value', $tag)->first();

        if ($tag) {
            $existingTag = $multievent->getTags()->find($tag->getId());
            if (! $existingTag) {
                $multievent->tags()->save($tag);
            }
        }
    }

    private static function addOrUpdateCastellerTags(Multievent $multievent, ?array $tags)
    {
        if ($multievent->hasCastellerTags()) {
            self::removeCastellerTagsFromMultievent($multievent);
        }

        if (! empty($tags)) {
            foreach ($tags as $tag) {
                self::addCastellerTagToMultievent($multievent, $tag);
            }
        }
    }

    private static function removeCastellerTagsFromMultievent(Multievent $multievent)
    {
        foreach ($multievent->getCastellerTags() as $tag) {
            $multievent->removeCastellerTag($tag);
        }
    }

    private static function addCastellerTagToMultievent(Multievent $multievent, string $tag): void
    {
        /** @var Tag $tag */
        $tag = Tag::currentTags(TypeTags::CASTELLERS, $multievent->getColla())->where('value', $tag)->first();

        if ($tag) {
            $existingTag = $multievent->getCastellerTags()->find($tag->getId());
            if (! $existingTag) {
                $multievent->castellerTags()->save($tag);
            }
        }
    }
}
