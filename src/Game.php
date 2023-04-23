<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

use DomainException;

class Game
{
    private const LIST_PLAYERS_MESSAGE = 'players: ';
    private const PLAYER_ALREADY_EXISTS_MESSAGE = ': already existing player';
    private const UNKNOWN_COMMAND_MESSAGE = 'unknown command';

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

        return self::UNKNOWN_COMMAND_MESSAGE;
    }

    /**
     * @throws DomainException
     */
    private function checkPlayerExists(string $name): void
    {
        foreach ($this->players as $player) {
            if ($player->getName() === $name) {
                throw new DomainException($name . self::PLAYER_ALREADY_EXISTS_MESSAGE);
            }
        }
    }

    private function addPlayer(string $name): void
    {
        $this->players[] = new Player($name);
    }

    private function getPlayers(): string
    {
        return self::LIST_PLAYERS_MESSAGE . implode(', ', array_map(function ($player) {
                return $player->getName();
            }, $this->players));
    }
}
