<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

class Game
{
    private array $players;

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
        $this->players[] = new Player($name);
    }

    private function getPlayers(): string
    {
        return 'players: ' . implode(', ', array_map(function ($player) {
                return $player->getName();
            }, $this->players));
    }
}
