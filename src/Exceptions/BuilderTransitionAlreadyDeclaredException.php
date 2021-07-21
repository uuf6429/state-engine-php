<?php

namespace uuf6429\StateEngine\Exceptions;

class BuilderTransitionAlreadyDeclaredException extends InvalidArgumentException
{
    public function __construct(string $oldState, string $newState)
    {
        parent::__construct("Cannot add transition from \"$oldState\" to \"$newState\", it has already been declared.");
    }
}
