<?php

declare(strict_types=1);

namespace App\Managers;

use App\Colla;
use App\Factories\TagFactory;
use App\Repositories\TagRepository;
use App\Tag;
use Symfony\Component\HttpFoundation\ParameterBag;

class TagsManager
{
    private TagRepository $repository;

    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createTag(Colla $colla, ParameterBag $bag): Tag
    {
        $tag = TagFactory::make($colla->getId(), $bag);
        $this->repository->save($tag);

        return $tag;
    }

    public function updateTag(Tag $tag, ParameterBag $bag): Tag
    {
        $tag = TagFactory::update($tag, $bag);
        $this->repository->save($tag);

        return $tag;
    }

    public function deleteTag(Tag $tag)
    {
        $this->repository->delete($tag);
    }
}
