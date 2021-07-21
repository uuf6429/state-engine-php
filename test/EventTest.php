<?php

namespace uuf6429\StateEngine;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use uuf6429\StateEngine\Implementation\Builder;
use uuf6429\StateEngine\Implementation\Engine;
use uuf6429\StateEngine\Implementation\Entities\State;
use uuf6429\StateEngine\Implementation\Events\StateChanged;
use uuf6429\StateEngine\Implementation\Events\StateChanging;
use uuf6429\StateEngine\Interfaces\StateAwareInterface;
use uuf6429\StateEngine\Interfaces\StateInterface;

class EventTest extends TestCase
{
    private EventDispatcherInterface $dispatcher;
    private Engine $engine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->onlyMethods(['dispatch'])
            ->getMock();

        $this->engine = Builder::create()
            ->addState('started')
            ->addState('finished')
            ->addTransition('started', 'finished')
            ->getEngine($this->dispatcher);
    }

    public function test_that_engine_fires_exception(): void
    {
        $dispatchedEvents = [];
        $this->dispatcher
            ->method('dispatch')
            ->willReturnCallback(static function ($event) use (&$dispatchedEvents) {
                $dispatchedEvents[] = $event;
            });
        $startedState = new State('started');
        $finishedState = new State('finished');

        $statefulItem = new class implements StateAwareInterface {
            public StateInterface $state;

            public function getState(): StateInterface
            {
                return $this->state;
            }

            public function setState(StateInterface $newState): void
            {
                $this->state = $newState;
            }
        };

        $statefulItem->state = $startedState;
        $this->engine->changeState($statefulItem, $finishedState);

        $this->assertSame($finishedState, $statefulItem->state);
        $this->assertEquals(
            [
                new StateChanging($statefulItem, $finishedState),
                new StateChanged($statefulItem, $startedState),
            ],
            $dispatchedEvents
        );
    }
}
