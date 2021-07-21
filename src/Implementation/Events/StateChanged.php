<?php


namespace uuf6429\StateEngine\Implementation\Events;


use uuf6429\StateEngine\Interfaces\StateInterface;
use uuf6429\StateEngine\Interfaces\StateAwareInterface;

class StateChanged
{
    private StateAwareInterface $item;
    private StateInterface $oldState;

    /**
     * Event triggered after a state is changed.
     *
     * @param StateAwareInterface $item
     * @param StateInterface $oldState
     */
    public function __construct(StateAwareInterface $item, StateInterface $oldState)
    {
        $this->item = $item;
        $this->oldState = $oldState;
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
    public function getOldState(): StateInterface
    {
        return $this->oldState;
    }
}
