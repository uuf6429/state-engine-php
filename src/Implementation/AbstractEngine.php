<?php

namespace uuf6429\StateEngine\Implementation;

use Psr\EventDispatcher\EventDispatcherInterface;
use uuf6429\StateEngine\Exceptions\TransitionNotDeclaredException;
use uuf6429\StateEngine\Interfaces\EngineInterface;
use uuf6429\StateEngine\Interfaces\StateAwareInterface;
use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

abstract class AbstractEngine implements EngineInterface
{
    private TransitionRepositoryInterface $repository;

    private ?EventDispatcherInterface $dispatcher;

    public function __construct(TransitionRepositoryInterface $repository, ?EventDispatcherInterface $dispatcher)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(StateAwareInterface $item, TransitionInterface $transition): void
    {
        if (!($matched = $this->repository->find($transition))) {
            throw new TransitionNotDeclaredException($transition);
        }

        $this->dispatcher && $this->dispatcher->dispatch(new Events\StateChanging($item, $matched->getNewState()));

        $item->setState($matched->getNewState());

        $this->dispatcher && $this->dispatcher->dispatch(new Events\StateChanged($item, $matched->getOldState()));
    }
}
