<?php

namespace uuf6429\StateEngine\Interfaces;

interface EngineInterface
{
    /**
     * Transition an item to a new state given a transition.
     *
     * @param StateAwareInterface $item
     * @param TransitionInterface $transition
     */
    public function execute(StateAwareInterface $item, TransitionInterface $transition): void;
}
