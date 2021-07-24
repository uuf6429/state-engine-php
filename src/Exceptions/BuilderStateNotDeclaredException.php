<?php

namespace uuf6429\StateEngine\Exceptions;

use uuf6429\StateEngine\Interfaces\StateInterface;

class BuilderStateNotDeclaredException extends InvalidArgumentException
{
    public function __construct(StateInterface $state)
    {
        parent::__construct("Cannot use state \"$state\", since it has not been declared yet.");
    }
}
