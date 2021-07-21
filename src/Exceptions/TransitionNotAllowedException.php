<?php

namespace uuf6429\StateEngine\Exceptions;

use uuf6429\StateEngine\Interfaces\TransitionInterface;

class TransitionNotAllowedException extends RuntimeException
{
    public function __construct(TransitionInterface $transition)
    {
        parent::__construct(sprintf(
            'Cannot change state from %s to %s; no such transition was defined.',
            $transition->getOldState()->getName(),
            $transition->getNewState()->getName()
        ));
    }
}
