<?php

namespace uuf6429\StateEngine\Implementation\Entities;

use uuf6429\StateEngine\Interfaces\DescribableInterface;
use uuf6429\StateEngine\Interfaces\StateInterface;
use uuf6429\StateEngine\Interfaces\TransitionInterface;

class Transition implements TransitionInterface, DescribableInterface
{
    private StateInterface $oldState;
    private StateInterface $newState;
    private ?string $description;

    public function __construct(StateInterface $oldState, StateInterface $newState, ?string $description = null)
    {
        $this->oldState = $oldState;
        $this->newState = $newState;
        $this->description = $description;
    }

    public function getOldState(): StateInterface
    {
        return $this->oldState;
    }

    public function getNewState(): StateInterface
    {
        return $this->newState;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function equals($other): bool
    {
        return $other instanceof TransitionInterface
            && $this->getId() === $other->getId();
    }

    public function getId(): string
    {
        return "({$this->oldState->getId()}) -> ({$this->newState->getId()})";
    }

    public function __toString(): string
    {
        return "{$this->oldState->getId()} -> {$this->newState->getId()}";
    }
}
