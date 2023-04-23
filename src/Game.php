<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

use DomainException;

class Game
{
    private const START_COMMAND = ' ';

    private const LIST_PLAYERS_MESSAGE = 'players: ';
    private const PLAYER_ALREADY_EXISTS_MESSAGE = ': already existing player';
    private const NOT_ENOUGH_PLAYERS_MESSAGE = 'There is no enough participants';
    private const UNKNOWN_COMMAND_MESSAGE = 'unknown command';

    private array $players = [];

    public function process(string $input): string
    {
        return $this->parseCommand($input);
    }

    private function parseCommand($input): string
    {
        if (preg_match('/add player (?P<player>.+)$/iSU', $input, $matches) === 1) {
            return $this->processAddPlayer($matches['player']);
        }

        if ($input === self::START_COMMAND) {
            return $this->processStart();
        }

        return self::UNKNOWN_COMMAND_MESSAGE;
    }

    private function processAddPlayer($player): string
    {
        try {
            $this->checkPlayerExists($player);
        } catch (DomainException $e) {
            return $e->getMessage();
        }

        $this->addPlayer($player);

        return $this->getPlayers();
    }

    private function processStart(): string
    {
        if (count($this->players) <= 1) {
            return self::NOT_ENOUGH_PLAYERS_MESSAGE;
        }

        return '';
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
