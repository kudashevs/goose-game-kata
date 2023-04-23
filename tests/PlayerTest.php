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

    /** @test */
    public function it_can_return_previous_position()
    {
        $pippo = new Player('Pippo');
        $pippo->move(1, 1);

        $this->assertSame(0, $pippo->getPreviousPosition());

        $pippo->move(1, 2);

        $this->assertSame(2, $pippo->getPreviousPosition());
    }

    /** @test */
    public function it_can_change_position()
    {
        $pippo = new Player('Pippo');
        $pippo->move(2, 3);

        $this->assertSame(5, $pippo->getCurrentPosition());
    }
}
