<?php

namespace Kudashevs\GooseGameKata\Tests;

use Kudashevs\GooseGameKata\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /** @test */
    public function it_can_retain_the_name()
    {
        $pippo = new Player('Pippo');

        $this->assertSame('Pippo', $pippo->getName());
    }
}
