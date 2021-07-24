<?php

namespace uuf6429\StateEngine\Implementation\Traits;

use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

/**
 * @mixin TransitionRepositoryInterface
 */
trait FindsTransition
{
    public function find(TransitionInterface $search): ?TransitionInterface
    {
        foreach ($this->all() as $match) {
            if ($search->equals($match)) {
                return $match;
            }
        }

        return null;
    }
}
