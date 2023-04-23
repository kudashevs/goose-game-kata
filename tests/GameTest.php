<?php

namespace Kudashevs\GooseGameKata\Tests;

use Kudashevs\GooseGameKata\Game;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    private Game $game;

    protected function setUp(): void
    {
        $this->game = new Game();
    }

    /** @test */
    public function it_can_add_a_player()
    {
        $result = $this->game->process('add player Pippo');

        $this->assertSame('players: Pippo', $result);
    }

    /** @test */
    public function it_can_add_multiple_players()
    {
        $this->game->process('add player Pippo');
        $output = $this->game->process('add player Pluto');

        $this->assertSame('players: Pippo, Pluto', $output);
    }

    /** @test */
    public function it_cannot_add_an_existing_player()
    {
        $this->game->process('add player Pippo');
        $output = $this->game->process('add player Pippo');

        $this->assertSame('Pippo: already existing player', $output);
    }

    /** @test */
    public function it_cannot_add_a_player_when_game_has_started()
    {
        $this->game->process('add player Pippo');
        $this->game->process('add player Pluto');
        $this->game->process(' ');

        $output = $this->game->process('add player Pino');

        $this->assertSame('You cannot add Pino. The game has already started.', $output);
    }

    /** @test */
    public function it_can_notify_when_no_enough_participants()
    {
        $this->game->process('add player Pippo');
        $this->game->process('add player Pippo');
        $output = $this->game->process(' ');

        $this->assertSame('There is no enough participants', $output);
    }

    /** @test */
    public function it_cannot_move_an_unregistered_player()
    {
        $game = $this->initReadyGame('Pippo', 'Pluto');
        $output = $game->process('move Popo 2, 4');

        $this->assertSame('You cannot move Popo. The player does not exist.', $output);
    }

    /** @test */
    public function it_can_move_the_registered_player()
    {
        $game = $this->initReadyGame('Pippo', 'Pluto');
        $output = $game->process('move Pippo 4, 2');

        $this->assertSame('Pippo rolls 4, 2. Pippo moves from Start to 6', $output);
    }

    private function initReadyGame(string ...$players): Game
    {
        $game = new Game();
        foreach ($players as $player) {
            $game->process('add player ' . $player);
        }
        $game->process(' ');

        return $game;
    }
}
