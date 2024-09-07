<?php

namespace uuf6429\StateEngine\Interfaces;

interface EquatableInterface
{
    /**
     * Returns true if this object equals the $other object.
     *
     * @param mixed $other
     */
    public function equals($other): bool;
}
