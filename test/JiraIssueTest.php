<?php

namespace uuf6429\StateEngine;

use PHPUnit\Framework\TestCase;
use uuf6429\StateEngine\Implementation\Builder;
use uuf6429\StateEngine\Implementation\Engine;
use uuf6429\StateEngine\Implementation\Entities\State;
use uuf6429\StateEngine\Interfaces\EngineInterface;
use uuf6429\StateEngine\Interfaces\StateAwareInterface;
use uuf6429\StateEngine\Interfaces\TransitionInterface;
use uuf6429\StateEngine\Interfaces\TransitionRepositoryInterface;

class JiraIssueTest extends TestCase
{
    private TransitionRepositoryInterface $repository;
    private EngineInterface $engine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Builder::create()
            // add states
            ->addState('backlog', 'Backlog')
            ->addState('analysis', 'Analysis')
            ->addState('ready-for-dev', 'Ready for Dev')
            ->addState('in-dev', 'In Dev')
            ->addState('ready-for-qa', 'Ready for QA')
            ->addState('in-qa', 'In QA')
            ->addState('ready-for-release', 'Ready for Release')
            ->addState('resolved', 'Resolved')
            // add transitions
            ->addTransition('backlog', 'analysis', 'Begin analysis')
            ->addTransition('backlog', 'in-dev', 'Fast-track for development')
            ->addTransition('analysis', 'ready-for-dev', 'Analysis complete')
            ->addTransition('analysis', 'backlog', 'Return to backlog')
            ->addTransition('ready-for-dev', 'analysis', 'Need more details')
            ->addTransition('ready-for-dev', 'in-dev', 'Begin development')
            ->addTransition('in-dev', 'ready-for-qa', 'Send to QA')
            ->addTransition('in-dev', 'ready-for-release', 'Fast-track for release')
            ->addTransition('in-dev', 'ready-for-dev', 'Stop development')
            ->addTransition('ready-for-qa', 'in-qa', 'Begin testing')
            ->addTransition('in-qa', 'ready-for-dev', 'QA Failed')
            ->addTransition('in-qa', 'ready-for-release', 'QA Passed')
            ->addTransition('ready-for-release', 'resolved', 'Released')
            ->addTransition('resolved', 'backlog', 'Reopen')
            // get repository
            ->getRepository();

        $this->engine = new Engine($this->repository, null);
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
        $this->expectNotToPerformAssertions();

        $item = new TestStateAwareItem(new State('in-dev'));
        $this->engine->changeState($item, new State('ready-for-qa'));
    }

    public function test_that_transitioning_from_in_dev_to_in_qa_is_not_allowed(): void
    {
        $this->expectExceptionMessage('Cannot change state from in-dev to in-qa; no such transition was defined.');

        $item = new TestStateAwareItem(new State('in-dev'));
        $this->engine->changeState($item, new State('in-qa'));
    }

    public function test_that_plant_uml_generation_works(): void
    {
        $this->assertEquals(
            [
                '@startuml',
                '',
                '(Backlog) --> (Analysis) : Begin analysis',
                '(Backlog) --> (In Dev) : Fast-track for development',
                '(Analysis) --> (Ready for Dev) : Analysis complete',
                '(Analysis) --> (Backlog) : Return to backlog',
                '(Ready for Dev) --> (Analysis) : Need more details',
                '(Ready for Dev) --> (In Dev) : Begin development',
                '(In Dev) --> (Ready for QA) : Send to QA',
                '(In Dev) --> (Ready for Release) : Fast-track for release',
                '(In Dev) --> (Ready for Dev) : Stop development',
                '(Ready for QA) --> (In QA) : Begin testing',
                '(In QA) --> (Ready for Dev) : QA Failed',
                '(In QA) --> (Ready for Release) : QA Passed',
                '(Ready for Release) --> (Resolved) : Released',
                '(Resolved) --> (Backlog) : Reopen',
                '',
                '@enduml',
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
            ->method('setState')
            ->with($newState);

        $this->engine->changeState($mock, $newState);
    }
}
