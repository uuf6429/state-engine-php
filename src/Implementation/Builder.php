<?php

namespace uuf6429\StateEngine\Implementation;

use Psr\EventDispatcher\EventDispatcherInterface;
use uuf6429\StateEngine\Exceptions\BuilderStateAlreadyDeclaredException;
use uuf6429\StateEngine\Exceptions\BuilderStateNotDeclaredException;
use uuf6429\StateEngine\Exceptions\BuilderTransitionAlreadyDeclaredException;
use uuf6429\StateEngine\Implementation\Entities\State;
use uuf6429\StateEngine\Implementation\Entities\Transition;
use uuf6429\StateEngine\Implementation\Repositories\ArrayRepository;
use uuf6429\StateEngine\Interfaces\EngineInterface;
use uuf6429\StateEngine\Interfaces\StateAwareInterface;
use uuf6429\StateEngine\Interfaces\StateInterface;
use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

class Builder
{
    /**
     * @var array<string, StateInterface>
     */
    private array $states = [];

    /**
     * @var array<string, TransitionInterface>
     */
    private array $transitions = [];

    private function __construct()
    {

    }

    public static function create(): self
    {
        return new static();
    }

    public static function stateMutator(callable $getter, callable $setter): StateAwareInterface
    {
        return new class ($getter, $setter) implements StateAwareInterface {
            private $getter;
            private $setter;

            public function __construct(callable $getter, callable $setter)
            {
                $this->getter = $getter;
                $this->setter = $setter;
            }

            public function getState(): StateInterface
            {
                return ($this->getter)();
            }

            public function setState(StateInterface $newState): void
            {
                ($this->setter)($newState);
            }
        };
    }

    public function addState(string $name, ?string $description = null): self
    {
        if (isset($this->states[$name])) {
            throw new BuilderStateAlreadyDeclaredException($name);
        }

        $this->states[$name] = new State($name, $description);

        return $this;
    }

    public function addTransition(string $oldStateName, string $newStateName, ?string $description = null): self
    {
        if (!isset($this->states[$oldStateName])) {
            throw new BuilderStateNotDeclaredException($oldStateName);
        }

        if (!isset($this->states[$newStateName])) {
            throw new BuilderStateNotDeclaredException($newStateName);
        }

        $transitionName = "($oldStateName) -> ($newStateName)";
        if (isset($this->transitions[$transitionName])) {
            throw new BuilderTransitionAlreadyDeclaredException($oldStateName, $newStateName);
        }

        $this->transitions[$transitionName] = new Transition(
            $this->states[$oldStateName],
            $this->states[$newStateName],
            $description
        );

        return $this;
    }

    /**
     * @return ArrayRepository
     */
    public function getRepository(): TransitionRepositoryInterface
    {
        return new ArrayRepository(array_values($this->transitions));
    }

    /**
     * @param EventDispatcherInterface|null $eventDispatcher
     * @return Engine
     */
    public function getEngine(?EventDispatcherInterface $eventDispatcher = null): EngineInterface
    {
        return new Engine($this->getRepository(), $eventDispatcher);
    }
}
