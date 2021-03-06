<?php

namespace uuf6429\StateEngine\Exceptions;

use uuf6429\StateEngine\Interfaces\TransitionInterface;

class BuilderTransitionAlreadyDeclaredException extends InvalidArgumentException
{
    public function __construct(TransitionInterface $transition)
    {
        parent::__construct("Cannot add transition \"$transition\", it has already been declared.");
    }
}
