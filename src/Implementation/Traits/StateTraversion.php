<?php

namespace uuf6429\StateEngine\Implementation\Traits;

use uuf6429\StateEngine\Interfaces\StateInterface;
use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

/**
 * Assuming the actual class is a {@see TransitionRepositoryInterface}, this trait provide methods for navigating
 * between states via transitions, either forward (old state to new state) or backward (new state to old state).
 */
trait StateTraversion
{
    public function getForwardTransitions(StateInterface $state): array
    {
        /** @var $this TransitionRepositoryInterface */
        return array_values(array_filter(
            iterator_to_array($this->all()),
            static function (TransitionInterface $transition) use ($state): bool {
                return $transition->getOldState()->equals($state);
            }
        ));
    }

    public function getBackwardTransitions(StateInterface $state): array
    {
        /** @var $this TransitionRepositoryInterface */
        return array_values(array_filter(
            iterator_to_array($this->all()),
            static function (TransitionInterface $transition) use ($state): bool {
                return $transition->getNewState()->equals($state);
            }
        ));
    }
}
