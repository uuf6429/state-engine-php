<?php

namespace uuf6429\StateEngine;

use PHPUnit\Framework\TestCase;
use uuf6429\StateEngine\Implementation\Builder;
use uuf6429\StateEngine\Implementation\Entities\State;
use uuf6429\StateEngine\Implementation\Repositories\ArrayRepository;
use uuf6429\StateEngine\Implementation\StateMachine;

class TurnstileGateTest extends TestCase
{
    private ArrayRepository $repository;
    private StateMachine $machine;

    protected function setUp(): void
    {
        parent::setUp();

        $builder = Builder::create()
            // make states
            ->defState('locked', 'Impassable')
            ->defState('open', 'Passable')
            // make transitions
            ->defDataTransition('locked', ['insert_coin'], 'open', 'Coin placed')
            ->defDataTransition('open', ['walk_through'], 'locked', 'Person walks through');

        $this->repository = $builder->getRepository();
        $this->machine = $builder->getMachine();
    }

    public function test_that_user_cannot_walk_when_locked(): void
    {
        $this->expectExceptionMessage('Cannot apply transition "locked (a:1:{i:0;s:12:"walk_through";})"; no such transition was defined.');

        $item = new StatefulItem(new State('locked'));
        $this->machine->processInput($item, ['walk_through']);
    }

    public function test_turnstile_opens_after_paying(): void
    {
        $item = new StatefulItem(new State('locked'));
        $this->machine->processInput($item, ['insert_coin']);

        $this->assertSame('open', $item->getState()->getName());
    }

    public function test_that_plant_uml_generation_works(): void
    {
        $this->assertEquals(
            [
                0 => '@startuml',
                1 => '',
                2 => '(Impassable) --> (Passable) : Coin placed',
                3 => '(Passable) --> (Impassable) : Person walks through',
                4 => '',
                5 => '@enduml',
            ],
            explode(PHP_EOL, $this->repository->toPlantUML())
        );
    }
}
