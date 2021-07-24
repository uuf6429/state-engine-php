<?php

namespace uuf6429\StateEngine\Interfaces;

use Traversable;

interface TransitionRepositoryInterface
{
    /**
     * Returns the stored transition matching the $search transition (or null if none found).
     *
     * @param TransitionInterface $search
     * @return null|TransitionInterface
     */
    public function find(TransitionInterface $search): ?TransitionInterface;

    /**
     * Returns an array or Traversable object of all transitions.
     *
     * @return Traversable|TransitionInterface[]
     */
    public function all(): Traversable;
}
