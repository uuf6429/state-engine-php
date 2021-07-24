<?php

namespace uuf6429\StateEngine\Exceptions;

use uuf6429\StateEngine\Interfaces\StateInterface;

class BuilderStateAlreadyDeclaredException extends InvalidArgumentException
{
    public function __construct(StateInterface $state)
    {
        parent::__construct("Cannot add state \"$state\", it has already been declared.");
    }
}
