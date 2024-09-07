<?php

namespace uuf6429\StateEngine\Implementation\Traits;

use RuntimeException;
use uuf6429\StateEngine\Interfaces\DescribableInterface;
use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

/**
 * This trait provides a method for generating a Plant UML diagram of the various states and transitions.
 * It may only be added to a class implementing {@see TransitionRepositoryInterface}.
 */
trait Plantable
{
    public function toPlantUML(): string
    {
        if (!$this instanceof TransitionRepositoryInterface) {
            throw new RuntimeException(
                sprintf('The `%s` trait may only be added to a class implementing `%s`', Plantable::class, TransitionInterface::class),
            );
        }

        /**
         * @return iterable<string>
         */
        $generateNodesAndEdges = static function (TransitionInterface ...$transitions): iterable {
            foreach ($transitions as $transition) {
                $oldStateText = ($oldState = $transition->getOldState()) instanceof DescribableInterface
                    ? $oldState->getDescription() : $oldState->getName();
                $newStateText = ($newState = $transition->getNewState()) instanceof DescribableInterface
                    ? $newState->getDescription() : $newState->getName();

                yield $transition instanceof DescribableInterface
                    ? sprintf('(%s) --> (%s) : %s', $oldStateText, $newStateText, $transition->getDescription())
                    : sprintf('(%s) --> (%s)', $oldStateText, $newStateText);
            }
        };

        return implode(
            PHP_EOL,
            [
                '@startuml',
                '',
                ...$generateNodesAndEdges(...$this->all()),
                '',
                '@enduml',
            ],
        );
    }
}
