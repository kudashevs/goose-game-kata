<?php

namespace Kudashevs\GooseGameKata\Tests\Acceptance;

use Kudashevs\GooseGameKata\GameApp;
use Kudashevs\GooseGameKata\Input\CliInput;
use Kudashevs\GooseGameKata\Output\CliOutput;
use PHPUnit\Framework\TestCase;

class GameAppTest extends TestCase
{
    /** @test */
    public function it_can_play_a_game()
    {
        $inputMock = $this->createMock(CliInput::class);
        $inputMock->expects($this->atLeastOnce())
            ->method('readLine')
            ->willReturnOnConsecutiveCalls(
                'add player Pippo',
                'add player Pluto',
                ' ',
                'move Pippo 2, 4', // bridge to 12
                'move Pluto 2, 3', // goose to 10
                'move Pippo 6, 6',
                'move Pluto 1, 1',
                'move Pippo 6, 6',
                'move Pluto 1, 1', // goose to 16
                'move Pippo 6, 6',
                'move Pluto 1, 1',
                'move Pippo 6, 6',
                'move Pluto 1, 2',
                'move Pippo 2, 1', // Pippo wins
                'move Pluto 1, 1', // game already ended
                'Stop!',
            );

        $outputMock = $this->createMock(CliOutput::class);
        $outputMock->expects($this->atLeastOnce())
            ->method('writeLine')
            ->withConsecutive(
                [$this->stringContains('Goose Game')],
                [$this->stringContains('players')],
                [$this->stringContains('players')],
                [$this->stringContains('Start')],
                [$this->stringContains('Bridge')],
                [$this->stringContains('Goose')],
                [$this->stringContains('24')],
                [$this->anything()],
                [$this->anything()],
                [$this->stringContains('16')],
                [$this->anything()],
                [$this->anything()],
                [$this->anything()],
                [$this->anything()],
                [$this->stringContains('Wins')],
                [$this->stringContains('over')],
            );
        $outputMock->expects($this->once())
            ->method('terminate')
            ->with(0);

        $app = new GameApp($inputMock, $outputMock);
        $app->run();
    }
}
