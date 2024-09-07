<?php

namespace uuf6429\StateEngine\Implementation\Traits;

use uuf6429\StateEngine\Interfaces\DescribableInterface;
use uuf6429\StateEngine\Interfaces\StateInterface;
use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

/**
 * This trait provides a method for generating a Mermaid State diagram of the various states and transitions. You'd
 * typically `use` this in a class implementing {@see TransitionRepositoryInterface}.
 */
trait Mermaidable
{
    /**
     * @return iterable<TransitionInterface>
     */
    abstract public function all(): iterable;

    public function toMermaid(): string
    {
        /**
         * @return array<string, array{id: string, text: string}>
         */
        $extractStateInfo = static function (TransitionInterface ...$transitions): array {
            $result = [];

            foreach ($transitions as $transition) {
                /** @var StateInterface $state */
                foreach ([$transition->getOldState(), $transition->getNewState()] as $state) {
                    $stateName = $state->getName();
                    $result[$stateName] ??= [
                        'id' => sprintf('s%d_%s', count($result) + 1, preg_replace('/\W/', '_', $stateName)),
                        'text' => ($state instanceof DescribableInterface) ? $state->getDescription() : $stateName,
                    ];
                }
            }

            return $result;
        };

        /**
         * @param array<string, array{id: string, text: string}> $stateInfo
         * @return iterable<string>
         */
        $generateNodesIds = static function (array $stateInfo): iterable {
            foreach ($stateInfo as ['id' => $stateId, 'text' => $stateText]) {
                yield "    $stateId: $stateText";
            }
        };

        /**
         * @param array<string, array{id: string, text: string}> $stateInfo
         * @return iterable<string>
         */
        $generateNodesAndEdges = static function (array $stateInfo, TransitionInterface ...$transitions): iterable {
            foreach ($transitions as $transition) {
                $oldStateId = $stateInfo[$transition->getOldState()->getName()]['id'];
                $newStateId = $stateInfo[$transition->getNewState()->getName()]['id'];

                yield $transition instanceof DescribableInterface
                    ? sprintf('    %s --> %s : %s', $oldStateId, $newStateId, $transition->getDescription())
                    : sprintf('    %s --> %s', $oldStateId, $newStateId);
            }
        };

        $transitions = iterator_to_array($this->all());
        $stateInfo = $extractStateInfo(...$transitions);

        return implode(
            PHP_EOL,
            [
                'stateDiagram',
                ...$generateNodesIds($stateInfo),
                ...$generateNodesAndEdges($stateInfo, ...$transitions),
            ],
        );
    }
}
