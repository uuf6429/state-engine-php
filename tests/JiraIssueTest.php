<?php

namespace uuf6429\StateEngine;

use PHPUnit\Framework\TestCase;
use uuf6429\StateEngine\Implementation\Builder;
use uuf6429\StateEngine\Implementation\Entities\State;
use uuf6429\StateEngine\Implementation\Repositories\ArrayRepository;
use uuf6429\StateEngine\Implementation\StateEngine;
use uuf6429\StateEngine\Interfaces\StateAwareInterface;
use uuf6429\StateEngine\Interfaces\TransitionInterface;

class JiraIssueTest extends TestCase
{
    private ArrayRepository $repository;
    private StateEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();

        $builder = Builder::create()
            // make states
            ->defState('backlog', 'Backlog')
            ->defState('analysis', 'Analysis')
            ->defState('ready-for-dev', 'Ready for Dev')
            ->defState('in-dev', 'In Dev')
            ->defState('ready-for-qa', 'Ready for QA')
            ->defState('in-qa', 'In QA')
            ->defState('ready-for-release', 'Ready for Release')
            ->defState('resolved', 'Resolved')
            // make transitions
            ->defTransition('backlog', 'analysis', 'Begin analysis')
            ->defTransition('backlog', 'in-dev', 'Fast-track for development')
            ->defTransition('analysis', 'ready-for-dev', 'Analysis complete')
            ->defTransition('analysis', 'backlog', 'Return to backlog')
            ->defTransition('ready-for-dev', 'analysis', 'Need more details')
            ->defTransition('ready-for-dev', 'in-dev', 'Begin development')
            ->defTransition('in-dev', 'ready-for-qa', 'Send to QA')
            ->defTransition('in-dev', 'ready-for-release', 'Fast-track for release')
            ->defTransition('in-dev', 'ready-for-dev', 'Stop development')
            ->defTransition('ready-for-qa', 'in-qa', 'Begin testing')
            ->defTransition('in-qa', 'ready-for-dev', 'QA Failed')
            ->defTransition('in-qa', 'ready-for-release', 'QA Passed')
            ->defTransition('ready-for-release', 'resolved', 'Released')
            ->defTransition('resolved', 'backlog', 'Reopen');

        $this->repository = $builder->getRepository();
        $this->engine = $builder->getEngine();
    }

    public function test_that_backlog_can_transition_to_analysis_or_in_dev(): void
    {
        $transitions = $this->repository->getForwardTransitions(new State('backlog'));

        $this->assertEquals(
            ['analysis', 'in-dev'],
            array_map(
                static function (TransitionInterface $transition) {
                    return $transition->getNewState()->getName();
                },
                $transitions
            )
        );
    }

    public function test_that_ready_for_release_happens_after_fast_track_or_passing_qa(): void
    {
        $transitions = $this->repository->getBackwardTransitions(new State('ready-for-release'));

        $this->assertEquals(
            ['in-dev', 'in-qa'],
            array_map(
                static function (TransitionInterface $transition) {
                    return $transition->getOldState()->getName();
                },
                $transitions
            )
        );
    }

    public function test_that_transitioning_from_in_dev_to_ready_for_qa_is_allowed(): void
    {
        $item = new StatefulItem(new State('in-dev'));

        $this->engine->changeState($item, new State('ready-for-qa'));

        $this->assertSame('ready-for-qa', $item->getState()->getName());
    }

    public function test_that_transitioning_from_in_dev_to_in_qa_is_not_allowed(): void
    {
        $this->expectExceptionMessage('Cannot apply transition "in-dev -> in-qa"; no such transition was defined.');

        $item = new StatefulItem(new State('in-dev'));

        $this->engine->changeState($item, new State('in-qa'));
    }

    public function test_that_plant_uml_generation_works(): void
    {
        $this->assertEquals(
            [
                0 => '@startuml',
                1 => '',
                2 => '(Backlog) --> (Analysis) : Begin analysis',
                3 => '(Backlog) --> (In Dev) : Fast-track for development',
                4 => '(Analysis) --> (Ready for Dev) : Analysis complete',
                5 => '(Analysis) --> (Backlog) : Return to backlog',
                6 => '(Ready for Dev) --> (Analysis) : Need more details',
                7 => '(Ready for Dev) --> (In Dev) : Begin development',
                8 => '(In Dev) --> (Ready for QA) : Send to QA',
                9 => '(In Dev) --> (Ready for Release) : Fast-track for release',
                10 => '(In Dev) --> (Ready for Dev) : Stop development',
                11 => '(Ready for QA) --> (In QA) : Begin testing',
                12 => '(In QA) --> (Ready for Dev) : QA Failed',
                13 => '(In QA) --> (Ready for Release) : QA Passed',
                14 => '(Ready for Release) --> (Resolved) : Released',
                15 => '(Resolved) --> (Backlog) : Reopen',
                16 => '',
                17 => '@enduml',
            ],
            explode(PHP_EOL, $this->repository->toPlantUML())
        );
    }

    public function test_that_the_engine_reads_and_writes_state(): void
    {
        $newState = new State('analysis');

        $mock = $this->getMockBuilder(StateAwareInterface::class)
            ->onlyMethods(['getState', 'setState'])
            ->getMock();

        $mock->expects($this->once())
            ->method('getState')
            ->willReturn(new State('backlog'));

        $mock->expects($this->once())
            ->method('setState');

        $this->engine->changeState($mock, $newState);
    }
}
