<?php

namespace uuf6429\StateEngine;

use PHPUnit\Framework\TestCase;
use uuf6429\StateEngine\Implementation\Builder;

class BuilderTest extends TestCase
{
    private Builder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = Builder::create()
            ->defState('started')
            ->defState('finished')
            ->defTransition('started', 'finished');
    }

    public function test_that_states_cannot_be_redeclared(): void
    {
        $this->expectExceptionMessage('Cannot add state "started", it has already been declared.');

        $this->builder->defState('started');
    }

    public function test_that_transitions_cannot_be_redeclared(): void
    {
        $this->expectExceptionMessage('Cannot add transition "started -> finished", it has already been declared.');

        $this->builder->defTransition('started', 'finished');
    }

    public function test_that_states_must_be_declared_before_transitions(): void
    {
        $this->expectExceptionMessage('Cannot use state "progressing", since it has not been declared yet.');

        $this->builder->defTransition('started', 'progressing');
    }
}
