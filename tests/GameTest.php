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

    /** @test */
    public function it_can_add_multiple_players()
    {
        $game = new Game();
        $game->process('add player Pippo');
        $result = $game->process('add player Pluto');

        $this->assertSame('players: Pippo, Pluto', $result);
    }

    /** @test */
    public function it_cannot_add_an_existing_player()
    {
        $game = new Game();
        $game->process('add player Pippo');
        $result = $game->process('add player Pippo');

        $this->assertSame('Pippo: already existing player', $result);
    }
}
