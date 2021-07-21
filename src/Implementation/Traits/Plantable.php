<?php

namespace uuf6429\StateEngine\Implementation\Traits;

use uuf6429\StateEngine\Interfaces\DescribableInterface;
use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

/**
 * This trait provides a method for generating a Plant UML diagram of the various states and transitions, assuming the
 * current class is a {@see TransitionRepositoryInterface}.
 */
trait Plantable
{
    public function toPlantUML(): string
    {
        /** @var $this TransitionRepositoryInterface */
        return implode(PHP_EOL, array_merge(
            ['@startuml', ''],
            array_map(
                static function (TransitionInterface $transition): string {
                    $oldState = $transition->getOldState();
                    $newState = $transition->getNewState();
                    $oldText = $oldState instanceof DescribableInterface ? $oldState->getDescription() : $oldState->getName();
                    $newText = $newState instanceof DescribableInterface ? $newState->getDescription() : $newState->getName();

                    $result = "($oldText) --> ($newText)";

                    if ($transition instanceof DescribableInterface) {
                        $result .= " : {$transition->getDescription()}";
                    }

                    return $result;
                },
                iterator_to_array($this)
            ),
            ['', '@enduml']
        ));
    }
}
