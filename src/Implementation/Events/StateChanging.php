<?php

namespace uuf6429\StateEngine\Implementation\Events;

use uuf6429\StateEngine\Interfaces\StateAwareInterface;
use uuf6429\StateEngine\Interfaces\StateInterface;

/**
 * Represents the event triggered before a state is changed.
 */
class StateChanging
{
    private StateAwareInterface $item;
    private StateInterface $newState;

    public function __construct(StateAwareInterface $item, StateInterface $newState)
    {
        $this->item = $item;
        $this->newState = $newState;
    }

    public function getItem(): StateAwareInterface
    {
        return $this->item;
    }

    public function getNewState(): StateInterface
    {
        return $this->newState;
    }

    public function __toString(): string
    {
        $item = $this->getItem();

        return sprintf(
            '%s[%s, %s->%s]',
            __CLASS__,
            method_exists($item, '__toString') ? $item : get_class($item),
            $item->getState(),
            $this->getNewState()
        );
    }
}
