<?php

namespace uuf6429\StateEngine\Exceptions;

class BuilderStateNotDeclaredException extends InvalidArgumentException
{
    public function __construct(string $name)
    {
        parent::__construct("Cannot use state \"$name\", since it has not been declared yet.");
    }
}
