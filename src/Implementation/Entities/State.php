<?php

namespace uuf6429\StateEngine\Implementation\Entities;

use uuf6429\StateEngine\Interfaces\DescribableInterface;
use uuf6429\StateEngine\Interfaces\StateInterface;

class State implements StateInterface, DescribableInterface
{
    private string $name;
    private ?string $description;

    public function __construct(string $name, ?string $description = null)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function equals($other): bool
    {
        return $other instanceof StateInterface
            && $this->getName() === $other->getName();
    }
}
