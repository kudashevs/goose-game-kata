<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

use DomainException;

class Game
{
    private array $players = [];

    public function process(string $input): string
    {
        return $this->parseCommand($input);
    }

    private function parseCommand($input): string
    {
        if (preg_match('/add player (?P<player>.+)$/iSU', $input, $matches) === 1) {
            try {
                $this->checkPlayerExists($matches['player']);
            } catch (DomainException $e) {
                return $e->getMessage();
            }

            $this->addPlayer($matches['player']);

            return $this->getPlayers();
        }

        return 'unknown command';
    }

    private function checkPlayerExists(string $name)
    {
        foreach ($this->players as $player) {
            if ($player->getName() === $name) {
                throw new DomainException($name . ': already existing player');
            }
        }
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
