<?php

namespace uuf6429\StateEngine\Interfaces;

interface StateInterface extends EquatableInterface, IdentifiableInterface
{
    /**
     * Returns a string that can uniquely identify the state. Eg: "new" or "active"
     *
     * @return string
     */
    public function getName(): string;

    public function __toString(): string;
}
