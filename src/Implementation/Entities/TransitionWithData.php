<?php

namespace uuf6429\StateEngine\Implementation\Entities;

use uuf6429\StateEngine\Interfaces\StateInterface;

class TransitionWithData extends Transition
{
    private array $data;

    public function __construct(StateInterface $oldState, array $data, StateInterface $newState, ?string $description = null)
    {
        parent::__construct($oldState, $newState, $description);

        $this->data = $data;
        ksort($this->data);
    }

    public function getId(): string
    {
        return sprintf('(%s) %s', $this->getOldState()->getId(), sha1(serialize($this->data)));
    }

    public function __toString(): string
    {
        return sprintf(
            '%s (%s)',
            $this->getOldState()->getId(),
            serialize($this->data)
        );
    }
}
