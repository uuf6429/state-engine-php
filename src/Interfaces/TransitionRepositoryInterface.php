<?php

namespace uuf6429\StateEngine\Interfaces;

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
     * @return iterable<TransitionInterface>
     */
    public function all(): iterable;
}
