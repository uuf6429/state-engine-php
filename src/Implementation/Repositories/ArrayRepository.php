<?php

namespace uuf6429\StateEngine\Implementation\Repositories;

use ArrayIterator;
use uuf6429\StateEngine\Implementation\Traits;

class ArrayRepository extends AbstractTraversable
{
    use Traits\StateTraversion;
    use Traits\Plantable;

    private array $transitions;

    public function __construct(array $transitions)
    {
        $this->transitions = $transitions;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->transitions);
    }
}
