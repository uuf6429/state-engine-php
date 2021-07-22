<?php

namespace uuf6429\StateEngine\Implementation\Repositories;

use IteratorAggregate;
use Traversable;
use uuf6429\StateEngine\Implementation\Traits\HasTransition;
use uuf6429\StateEngine\Implementation\Traits\StateTraversion;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

abstract class AbstractTraversable implements TransitionRepositoryInterface, IteratorAggregate
{
    use HasTransition, StateTraversion;

    public function all(): Traversable
    {
        return $this->getIterator();
    }
}
