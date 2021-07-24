<?php

namespace uuf6429\StateEngine\Implementation;

use uuf6429\StateEngine\Interfaces\StateAwareInterface;
use uuf6429\StateEngine\Interfaces\StateInterface;

class StateEngine extends AbstractEngine
{
    /**
     * A shortcut to avoid getting a transition.
     *
     * @param StateAwareInterface $item
     * @param StateInterface $newState
     */
    public function changeState(StateAwareInterface $item, StateInterface $newState): void
    {
        $transition = new Entities\Transition($item->getState(), $newState);
        $this->execute($item, $transition);
    }
}
