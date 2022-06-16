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
        $generateNodesAndEdges = static function (TransitionInterface $transition): string {
            $oldText = ($oldState = $transition->getOldState()) instanceof DescribableInterface
                ? $oldState->getDescription() : $oldState->getName();
            $newText = ($newState = $transition->getNewState()) instanceof DescribableInterface
                ? $newState->getDescription() : $newState->getName();

            $result = "($oldText) --> ($newText)";

            if ($transition instanceof DescribableInterface) {
                $result .= " : {$transition->getDescription()}";
            }

            return $result;
        };

        /** @var $this TransitionRepositoryInterface */
        return implode(PHP_EOL, array_merge(
            ['@startuml', ''],
            array_map($generateNodesAndEdges, iterator_to_array($this)),
            ['', '@enduml']
        ));
    }
}
