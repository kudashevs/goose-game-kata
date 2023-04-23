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
    private const UNKNOWN_COMMAND_MESSAGE = 'unknown command';

    private const ALREADY_STARTED_MESSAGE = 'You cannot add %s. The game has already started.';
    private const MOVE_UNKNOWN_PLAYER_MESSAGE = 'You cannot move %s. The player does not exist.';

    private const SPACE_NAMES = [
        0 => 'Start',
    ];

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

        if (preg_match('/move (?P<player>.+)\s+(?P<dice1>[1-6]),\s+(?P<dice2>[1-6])$/iSu', $input, $matches) === 1) {
            return $this->processMovePlayer($matches['player'], (int)$matches['dice1'], (int)$matches['dice2']);
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
        if (array_key_exists($name, $this->players)) {
            throw new DomainException(
                sprintf(self::PLAYER_ALREADY_EXISTS_MESSAGE, $name)
            );
        }
    }

    private function checkGameHasStarted(string $name): void
    {
        if ($this->started === true) {
            throw new DomainException(
                sprintf(self::ALREADY_STARTED_MESSAGE, $name)
            );
        }
    }

    private function processMovePlayer(string $name, int $dice1, int $dice2): string
    {
        try {
            $this->checkPlayerDoesntExist($name);
        } catch (DomainException $e) {
            return $e->getMessage();
        }

        $player = $this->getPlayerByName($name);
        $player->move($dice1, $dice2);

        return sprintf(
            '%s rolls %s, %s. %s moves from %s to %s',
            $name,
            $dice1,
            $dice2,
            $name,
            $this->getSpaceTitle($player->getPreviousPosition()),
            $this->getSpaceTitle($player->getCurrentPosition()),
        );
    }

    private function checkPlayerDoesntExist(string $name)
    {
        if (! array_key_exists($name, $this->players)) {
            throw new DomainException(
                sprintf(self::MOVE_UNKNOWN_PLAYER_MESSAGE, $name)
            );
        }
    }

    private function getPlayerByName(string $name): Player
    {
        return $this->players[$name];
    }

    private function getSpaceTitle(int $position): string
    {
        if (array_key_exists($position, self::SPACE_NAMES)) {
            return self::SPACE_NAMES[$position];
        }

        return (string)$position;
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
        $this->players[$name] = new Player($name);
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
