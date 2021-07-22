<?php

namespace uuf6429\StateEngine\Interfaces;

use Traversable;

interface TransitionRepositoryInterface
{
    /**
     * Returns true if the specified transition exists in the repository.
     *
     * @param TransitionInterface $transition
     * @return bool
     */
    public function has(TransitionInterface $transition): bool;

    /**
     * Returns an array or Traversable object of all transitions.
     *
     * @return Traversable|TransitionInterface[]
     */
    public function all(): Traversable;
}
