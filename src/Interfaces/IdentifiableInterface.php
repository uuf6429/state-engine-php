<?php

namespace uuf6429\StateEngine\Interfaces;

interface IdentifiableInterface
{
    /**
     * Returns a string that uniquely identifies the content of this object in relation to objects of the same type.
     *
     * @return string
     */
    public function getId(): string;
}
