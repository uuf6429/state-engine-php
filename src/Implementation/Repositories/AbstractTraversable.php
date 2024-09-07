<?php

namespace uuf6429\StateEngine\Implementation\Repositories;

use IteratorAggregate;
use uuf6429\StateEngine\Implementation\Traits\FindsTransition;
use uuf6429\StateEngine\Implementation\Traits\StateTraversion;
use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

/**
 * @implements IteratorAggregate<int, TransitionInterface>
 */
abstract class AbstractTraversable implements TransitionRepositoryInterface, IteratorAggregate
{
    use FindsTransition;
    use StateTraversion;

    public function all(): iterable
    {
        return $this->getIterator();
    }
}
