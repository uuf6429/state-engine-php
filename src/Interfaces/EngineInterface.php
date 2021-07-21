<?php

namespace uuf6429\StateEngine\Interfaces;

interface EngineInterface
{
    /**
     * Transition an item to a new state (given the new state).
     *
     * @param StateAwareInterface $item
     * @param StateInterface $newState
     */
    public function changeState(StateAwareInterface $item, StateInterface $newState): void;

    /**
     * Transition an item to a new state (given a transition).
     *
     * @param StateAwareInterface $item
     * @param TransitionInterface $transition
     */
    public function applyTransition(StateAwareInterface $item, TransitionInterface $transition): void;
}
