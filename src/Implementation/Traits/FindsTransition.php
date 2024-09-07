<?php

namespace uuf6429\StateEngine\Implementation\Traits;

use Exception;
use uuf6429\StateEngine\Interfaces\TransitionInterface;

/**
 * This trait searches for a transition object and returns the match, or null if no match is found. You'd typically
 * `use` this in a class implementing {@see TransitionRepositoryInterface}.
 */
trait FindsTransition
{
    abstract public function all(): iterable;

    /**
     * @throws Exception
     */
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
