<?php

namespace uuf6429\StateEngine\Implementation\Repositories;

use Exception;
use IteratorAggregate;
use Traversable;
use uuf6429\StateEngine\Implementation\Traits\FindsTransition;
use uuf6429\StateEngine\Implementation\Traits\StateTraversion;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

abstract class AbstractTraversable implements TransitionRepositoryInterface, IteratorAggregate
{
    use FindsTransition, StateTraversion;

    /**
     * @throws Exception
     */
    public function all(): Traversable
    {
        return $this->getIterator();
    }
}
