<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

use DomainException;

class Game
{
    private const START_COMMAND = ' ';

    private const LIST_PLAYERS_MESSAGE = 'players: %s';
    private const PLAYER_ALREADY_EXISTS_MESSAGE = '%s: already existing player';
    private const NOT_ENOUGH_PLAYERS_MESSAGE = 'There is no enough participants';
    private const ALREADY_STARTED_MESSAGE = 'You cannot add %s. The game has already started.';
    private const UNKNOWN_COMMAND_MESSAGE = 'unknown command';

    private bool $started = false;

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
            $this->checkGameHasStarted($player);
        } catch (DomainException $e) {
            return $e->getMessage();
        }

        $this->addPlayer($player);

        return $this->getPlayers();
    }

    /**
     * @throws DomainException
     */
    private function checkPlayerExists(string $name): void
    {
        foreach ($this->players as $player) {
            if ($player->getName() === $name) {
                throw new DomainException(
                    sprintf(self::PLAYER_ALREADY_EXISTS_MESSAGE, $name)
                );
            }
        }
    }

    private function checkGameHasStarted(string $name)
    {
        if ($this->started === true) {
            throw new DomainException(
                sprintf(self::ALREADY_STARTED_MESSAGE, $name)
            );
        }
    }

    private function processStart(): string
    {
        if (count($this->players) <= 1) {
            return self::NOT_ENOUGH_PLAYERS_MESSAGE;
        }

        $this->started = true;

        return '';
    }

    private function addPlayer(string $name): void
    {
        $this->players[] = new Player($name);
    }

    private function getPlayers(): string
    {
        return sprintf(
            self::LIST_PLAYERS_MESSAGE,
            implode(', ', array_map(function ($player) {
                return $player->getName();
            }, $this->players))
        );
    }
}
