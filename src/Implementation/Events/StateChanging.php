<?php


namespace uuf6429\StateEngine\Implementation\Events;


use uuf6429\StateEngine\Interfaces\StateInterface;
use uuf6429\StateEngine\Interfaces\StateAwareInterface;

class StateChanging
{
    private StateAwareInterface $item;
    private StateInterface $newState;

    /**
     * Event triggered before a state is changed.
     *
     * @param StateAwareInterface $item
     * @param StateInterface $newState
     */
    public function __construct(StateAwareInterface $item, StateInterface $newState)
    {
        $this->item = $item;
        $this->newState = $newState;
    }

    /**
     * @return StateAwareInterface
     */
    public function getItem(): StateAwareInterface
    {
        return $this->item;
    }

    /**
     * @return StateInterface
     */
    public function getNewState(): StateInterface
    {
        return $this->newState;
    }
}
