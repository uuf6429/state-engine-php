<?php

namespace uuf6429\StateEngine\Implementation;

use uuf6429\StateEngine\Implementation\Entities\State;
use uuf6429\StateEngine\Interfaces\StateAwareInterface;

class StateMachine extends AbstractEngine
{
    /**
     * A shortcut to avoid getting a transition.
     *
     * @param StateAwareInterface $item
     * @param array $input
     */
    public function processInput(StateAwareInterface $item, array $input): void
    {
        $transition = new Entities\TransitionWithData($item->getState(), $input, new State(''));
        $this->execute($item, $transition);
    }
}
