<?php

namespace uuf6429\StateEngine;

use uuf6429\StateEngine\Interfaces\StateAwareInterface;
use uuf6429\StateEngine\Interfaces\StateInterface;

class StatefulItem implements StateAwareInterface
{
    private StateInterface $state;

    public function __construct(StateInterface $initialState)
    {
        $this->state = $initialState;
    }

    public function getState(): StateInterface
    {
        return $this->state;
    }

    public function setState(StateInterface $newState): void
    {
        $this->state = $newState;
    }
}
