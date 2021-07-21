<?php

namespace uuf6429\StateEngine\Exceptions;

class BuilderStateAlreadyDeclaredException extends InvalidArgumentException
{
    public function __construct(string $name)
    {
        parent::__construct("Cannot add state \"$name\", it has already been declared.");
    }
}
