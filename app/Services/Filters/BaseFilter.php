<?php

namespace App\Services\Filters;

use App\Colla;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseFilter implements \IteratorAggregate
{
    private Colla $colla;

    private Builder $eloquentBuilder;

    public function __construct(Builder $eloquentBuilder)
    {
        $this->eloquentBuilder = $eloquentBuilder;
    }

    public function eloquentBuilder(): Builder
    {
        return $this->eloquentBuilder;
    }

    public function getIterator(): \ArrayIterator
    {
        return $this->eloquentBuilder->get()->getIterator();
    }

    public function get(): Collection
    {
        return $this->eloquentBuilder->get();
    }

    public function getArray(): array
    {
        return $this->eloquentBuilder->get()->toArray();
    }

    public function with(string $relation): self
    {
        $this->eloquentBuilder->with($relation);

        return $this;
    }

    public function orderBy(string $field, string $order = 'asc'): self
    {
        $this->eloquentBuilder
            ->orderBy($field, $order);

        return $this;
    }
}
