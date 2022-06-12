<?php

namespace uuf6429\StateEngine\Interfaces;

interface StateAwareInterface
{
    /**
     * Returns the old (or current) state.
     *
     * @return StateInterface
     */
    public function getState(): StateInterface;

    /**
     * Sets the new state.
     *
     * @param StateInterface $newState
     */
    public function setState(StateInterface $newState): void;
}
