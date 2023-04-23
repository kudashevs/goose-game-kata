<?php

namespace Kudashevs\GooseGameKata\Tests;

use Kudashevs\GooseGameKata\Game;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    /** @test */
    public function it_can_add_a_player()
    {
        $game = new Game();
        $result = $game->process('add player Pippo');

        $this->assertSame('players: Pippo', $result);
    }
}
