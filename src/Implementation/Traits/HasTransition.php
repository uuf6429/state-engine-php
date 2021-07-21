<?php

namespace uuf6429\StateEngine\Implementation\Traits;

use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

/**
 * @mixin TransitionRepositoryInterface
 */
trait HasTransition
{
    public function has(TransitionInterface $transition): bool
    {
        foreach ($this->all() as $testTransition) {
            if ($transition->equals($testTransition)) {
                return true;
            }
        }

        return false;
    }
}
