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
                $transitions,
            ),
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
                $transitions,
            ),
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
            explode(PHP_EOL, $this->repository->toPlantUML()),
        );
    }

    public function test_that_mermaid_generation_works(): void
    {
        $this->assertEquals(
            [
                'stateDiagram',
                '    s1_backlog: Backlog',
                '    s2_analysis: Analysis',
                '    s3_in_dev: In Dev',
                '    s4_ready_for_dev: Ready for Dev',
                '    s5_ready_for_qa: Ready for QA',
                '    s6_ready_for_release: Ready for Release',
                '    s7_in_qa: In QA',
                '    s8_resolved: Resolved',
                '    s1_backlog --> s2_analysis : Begin analysis',
                '    s1_backlog --> s3_in_dev : Fast-track for development',
                '    s2_analysis --> s4_ready_for_dev : Analysis complete',
                '    s2_analysis --> s1_backlog : Return to backlog',
                '    s4_ready_for_dev --> s2_analysis : Need more details',
                '    s4_ready_for_dev --> s3_in_dev : Begin development',
                '    s3_in_dev --> s5_ready_for_qa : Send to QA',
                '    s3_in_dev --> s6_ready_for_release : Fast-track for release',
                '    s3_in_dev --> s4_ready_for_dev : Stop development',
                '    s5_ready_for_qa --> s7_in_qa : Begin testing',
                '    s7_in_qa --> s4_ready_for_dev : QA Failed',
                '    s7_in_qa --> s6_ready_for_release : QA Passed',
                '    s6_ready_for_release --> s8_resolved : Released',
                '    s8_resolved --> s1_backlog : Reopen',
            ],
            explode(PHP_EOL, $this->repository->toMermaid()),
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
