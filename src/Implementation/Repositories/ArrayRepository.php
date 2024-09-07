<?php

namespace uuf6429\StateEngine\Implementation\Repositories;

use ArrayIterator;
use uuf6429\StateEngine\Implementation\Traits;
use uuf6429\StateEngine\Interfaces\TransitionInterface;

class ArrayRepository extends AbstractTraversable
{
    use Traits\StateTraversal;
    use Traits\Plantable;
    use Traits\Mermaidable;

    /**
     * @var list<TransitionInterface>
     */
    private array $transitions;

    /**
     * @param list<TransitionInterface> $transitions
     */
    public function __construct(array$transitions)
    {
        $this->transitions = $transitions;
    }

    /**
     * @return ArrayIterator<int, TransitionInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->transitions);
    }
}
