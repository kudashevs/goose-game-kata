<?php

namespace Kudashevs\GooseGameKata\Tests\Unit\Domain;

use Kudashevs\GooseGameKata\Domain\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    private Player $pippo;

    protected function setUp(): void
    {
        $this->pippo = new Player('Pippo');
    }

    /** @test */
    public function it_can_retain_the_name()
    {
        $this->assertSame('Pippo', $this->pippo->getName());
    }

    /** @test */
    public function it_can_return_a_previous_position()
    {
        $this->pippo->move(1, 1);

        $this->assertSame(0, $this->pippo->getPreviousPosition());

        $this->pippo->move(1, 2);

        $this->assertSame(2, $this->pippo->getPreviousPosition());
    }

    /** @test */
    public function it_can_return_a_new_position()
    {
        $this->pippo->move(2, 3);

        $this->assertSame(5, $this->pippo->getCurrentPosition());
    }

    /** @test */
    public function it_can_update_a_current_position()
    {
        $this->pippo->move(2, 3);
        $this->pippo->updatePosition(50);

        $this->assertSame(50, $this->pippo->getCurrentPosition());
        $this->assertSame(5, $this->pippo->getPreviousPosition());
    }
}
