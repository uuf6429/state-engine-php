<?php

namespace uuf6429\StateEngine\Interfaces;

interface EquatableInterface
{
    /**
     * Returns true if this object equals the $other object.
     *
     * @param $other
     * @return bool
     */
    public function equals($other): bool;
}
