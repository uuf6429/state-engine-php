<?php

namespace uuf6429\StateEngine;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use uuf6429\StateEngine\Implementation\Builder;
use uuf6429\StateEngine\Implementation\Entities\State;
use uuf6429\StateEngine\Interfaces\EngineInterface;

class EventTest extends TestCase
{
    private EventDispatcherInterface $dispatcher;
    private EngineInterface $engine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->onlyMethods(['dispatch'])
            ->getMock();

        $this->engine = Builder::create()
            ->defState('started')
            ->defState('finished')
            ->defTransition('started', 'finished')
            ->getEngine($this->dispatcher);
    }

    public function test_that_engine_triggers_events(): void
    {
        $dispatchedEvents = [];
        $this->dispatcher
            ->method('dispatch')
            ->willReturnCallback(static function ($event) use (&$dispatchedEvents) {
                $dispatchedEvents[] = $event;
            });
        $startedState = new State('started');
        $finishedState = new State('finished');

        $statefulItem = new StatefulItem($startedState);
        $this->engine->changeState($statefulItem, $finishedState);

        $this->assertEquals($finishedState, $statefulItem->getState());
        $this->assertEquals(
            [
                'uuf6429\StateEngine\Implementation\Events\StateChanging[StatefulItem, finished->finished]',
                'uuf6429\StateEngine\Implementation\Events\StateChanged[StatefulItem, started->finished]',
            ],
            array_map('strval', $dispatchedEvents)
        );
    }
}
