<?php

namespace uuf6429\StateEngine\Implementation;

use Psr\EventDispatcher\EventDispatcherInterface;
use uuf6429\StateEngine\Exceptions\TransitionNotAllowedException;
use uuf6429\StateEngine\Interfaces\EngineInterface;
use uuf6429\StateEngine\Interfaces\StateInterface;
use uuf6429\StateEngine\Interfaces\StateAwareInterface;
use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

class Engine implements EngineInterface
{
    private TransitionRepositoryInterface $repository;

    private ?EventDispatcherInterface $dispatcher;

    public function __construct(TransitionRepositoryInterface $repository, ?EventDispatcherInterface $dispatcher)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    public function changeState(StateAwareInterface $item, StateInterface $newState): void
    {
        $transition = new Entities\Transition($item->getState(), $newState);
        $this->applyTransition($item, $transition);
    }

    public function applyTransition(StateAwareInterface $item, TransitionInterface $transition): void
    {
        if (!$this->repository->has($transition)) {
            throw new TransitionNotAllowedException($transition);
        }

        $this->dispatcher && $this->dispatcher->dispatch(new Events\StateChanging($item, $transition->getNewState()));

        $item->setState($transition->getNewState());

        $this->dispatcher && $this->dispatcher->dispatch(new Events\StateChanged($item, $transition->getOldState()));
    }
}
