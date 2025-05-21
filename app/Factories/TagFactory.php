<?php

declare(strict_types=1);

namespace App\Factories;

use App\Tag;
use Symfony\Component\HttpFoundation\ParameterBag;

class TagFactory
{
    public static function make(int $collaId, ParameterBag $bag): Tag
    {
        $tag = new Tag();

        $tag->setAttribute('colla_id', $collaId);

        return self::update($tag, $bag);
    }

    public static function update(Tag $tag, ParameterBag $bag): Tag
    {

        if ($bag->has('id_tag_external')) {
            $tag->setAttribute('id_tag_external', $bag->getInt('id_tag_external'));
        }

        if ($bag->has('name')) {
            $tag->setAttribute('name', $bag->get('name'));
        }

        if ($bag->has('value')) {
            $tag->setAttribute('value', $bag->get('value'));
        }

        if ($bag->has('group')) {
            $tag->setAttribute('group', $bag->get('group'));
        }

        if ($bag->has('type')) {
            $tag->setAttribute('type', $bag->get('type'));
        }

        return $tag;
    }
}
