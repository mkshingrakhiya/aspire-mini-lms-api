<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Determines whether the test have called the given class.
     *
     * @param string $action
     */
    public function shouldHaveCalledAction(string $action)
    {
        $original = $this->app->make($action);

        $this->mock($action)
             ->shouldReceive('__invoke')
             ->atLeast()
             ->once()
             ->andReturnUsing(fn(...$args) => $original(...$args));
    }
}
