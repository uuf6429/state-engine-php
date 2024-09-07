<?php

namespace uuf6429\StateEngine\Implementation\Repositories;

use IteratorAggregate;
use uuf6429\StateEngine\Implementation\Traits\FindsTransition;
use uuf6429\StateEngine\Implementation\Traits\StateTraversal;
use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

/**
 * @implements IteratorAggregate<int, TransitionInterface>
 */
abstract class AbstractTraversable implements TransitionRepositoryInterface, IteratorAggregate
{
    use FindsTransition;
    use StateTraversal;

    public function all(): iterable
    {
        return $this->getIterator();
    }
}
