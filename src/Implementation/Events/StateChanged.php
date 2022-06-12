<?php

namespace uuf6429\StateEngine\Implementation\Events;

use uuf6429\StateEngine\Interfaces\StateAwareInterface;
use uuf6429\StateEngine\Interfaces\StateInterface;

/**
 * Represents the event triggered after a state is changed.
 */
class StateChanged
{
    private StateAwareInterface $item;
    private StateInterface $oldState;

    public function __construct(StateAwareInterface $item, StateInterface $oldState)
    {
        $this->item = $item;
        $this->oldState = $oldState;
    }

    public function getItem(): StateAwareInterface
    {
        return $this->item;
    }

    public function getOldState(): StateInterface
    {
        return $this->oldState;
    }
}
