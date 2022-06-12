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
     * @param array $inputData
     */
    public function processInput(StateAwareInterface $item, array $inputData): void
    {
        $transition = new Entities\TransitionWithData($item->getState(), $inputData, new State(''));
        $this->execute($item, $transition);
    }
}
