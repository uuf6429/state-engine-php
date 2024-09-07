<?php

namespace uuf6429\StateEngine\Implementation\Traits;

use Exception;
use uuf6429\StateEngine\Interfaces\StateInterface;
use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

/**
 * This trait provide methods for navigating between states via transitions, either forward (old state to new state)
 * or backward (new state to old state). You'd typically `use` this in a class implementing {@see TransitionRepositoryInterface}.
 */
trait StateTraversal
{
    /**
     * @return iterable<TransitionInterface>
     */
    abstract public function all(): iterable;

    /**
     * @return list<TransitionInterface>
     * @throws Exception
     */
    public function getForwardTransitions(StateInterface $state): array
    {
        return array_values(array_filter(
            [...$this->all()],
            static function (TransitionInterface $transition) use ($state): bool {
                return $transition->getOldState()->equals($state);
            },
        ));
    }

    /**
     * @return list<TransitionInterface>
     * @throws Exception
     */
    public function getBackwardTransitions(StateInterface $state): array
    {
        return array_values(array_filter(
            [...$this->all()],
            static function (TransitionInterface $transition) use ($state): bool {
                return $transition->getNewState()->equals($state);
            },
        ));
    }
}
