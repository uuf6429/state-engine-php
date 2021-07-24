<?php

namespace uuf6429\StateEngine\Interfaces;

interface TransitionInterface extends EquatableInterface, IdentifiableInterface
{
    /**
     * Return the old state.
     *
     * @return StateInterface
     */
    public function getOldState(): StateInterface;

    /**
     * Return the new state.
     *
     * @return StateInterface
     */
    public function getNewState(): StateInterface;

    public function __toString(): string;
}
