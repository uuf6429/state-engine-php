<?php

namespace uuf6429\StateEngine\Exceptions;

use uuf6429\StateEngine\Interfaces\TransitionInterface;

class TransitionNotDeclaredException extends RuntimeException
{
    public function __construct(TransitionInterface $transition)
    {
        parent::__construct("Cannot apply transition \"$transition\"; no such transition was defined.");
    }
}
