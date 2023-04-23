<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

class Game
{
    private Player $player;

    public function process(string $input): string
    {
        return $this->parseCommand($input);
    }

    private function parseCommand($input): string
    {
        if (preg_match('/add player (?P<player>.+)$/iSU', $input, $matches) === 1) {
            $this->addPlayer($matches['player']);

            return $this->getPlayers();
        }

        return 'unknown command';
    }

    private function addPlayer(string $name): void
    {
        $this->player = new Player($name);
    }

    private function getPlayers(): string
    {
        return 'players: ' . $this->player->getName();
    }
}
