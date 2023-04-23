<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

use DomainException;

class Game
{
    private const START_COMMAND = ' ';
    private const WIN_SPACE = 63;
    private const SPACE_NAMES = [
        0 => 'Start',
    ];
    private const DICE_MIN = 1;
    private const DICE_MAX = 6;

    private const ADD_PLAYER_REGEX = '/add player (?P<player>.+)$/iSU';

    private const UNKNOWN_COMMAND_MESSAGE = 'unknown command';
    private const LIST_PLAYERS_MESSAGE = 'players: %s';
    private const PLAYER_ALREADY_EXISTS_MESSAGE = '%s: already existing player';
    private const NOT_ENOUGH_PLAYERS_MESSAGE = 'There is no enough participants';

    private const HAS_STARTED_MESSAGE = 'You cannot add %s. The game has already started.';
    private const MOVE_UNREGISTERED_PLAYER_MESSAGE = 'You cannot move %s. The player does not exist.';
    private const MOVE_REGISTERED_PLAYER_MESSAGE = '%s rolls %s, %s. %s moves from %s to %s';
    private const HAS_WINNER_MESSAGE = 'We have a winner. The game is over!';

    private bool $hasStarted = false;

    private bool $hasWinner = false;

    private array $players = [];

    public function process(string $input): string
    {
        return $this->parseCommand($input);
    }

    private function parseCommand($input): string
    {
        if ($this->checkGameHasWinner()) {
            return self::HAS_WINNER_MESSAGE;
        }

        if (preg_match(self::ADD_PLAYER_REGEX, $input, $matches) === 1) {
            return $this->processAddPlayer($matches['player']);
        }

        if ($input === self::START_COMMAND) {
            return $this->processStart();
        }

        if (preg_match('/move (?P<player>.+)(\s+(?P<dice1>[1-6]),\s+(?P<dice2>[1-6]))?$/iSU', $input, $matches) === 1) {
            $dice1 = $this->prepareOrGenerateDice($matches['dice1'] ?? '');
            $dice2 = $this->prepareOrGenerateDice($matches['dice2'] ?? '');

            return $this->processMovePlayer($matches['player'], $dice1, $dice2);
        }

        return self::UNKNOWN_COMMAND_MESSAGE;
    }

    private function checkGameHasWinner(): bool
    {
        return $this->hasWinner === true;
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
        if ($this->hasStarted === true) {
            throw new DomainException(
                sprintf(self::HAS_STARTED_MESSAGE, $name)
            );
        }
    }

    private function prepareOrGenerateDice(string $number): int
    {
        return is_numeric($number) ? (int)$number : random_int(self::DICE_MIN, self::DICE_MAX);
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

        if ($this->isWinner($player)) {
            $this->hasWinner = true;

            return sprintf(
                self::MOVE_REGISTERED_PLAYER_MESSAGE . '. %s Wins!!',
                $name,
                $dice1,
                $dice2,
                $name,
                $this->getSpaceTitle($player->getPreviousPosition()),
                $this->getSpaceTitle($player->getCurrentPosition()),
                $name,
            );
        }

        if ($this->isOverlap($player)) {
            $oldPosition = $player->getPreviousPosition();
            $overlap = $player->getCurrentPosition() - self::WIN_SPACE;
            $newPosition = self::WIN_SPACE - $overlap;

            $player->updatePosition($newPosition);

            return sprintf(
                self::MOVE_REGISTERED_PLAYER_MESSAGE . '. %s bounces! Pippo returns to %s',
                $name,
                $dice1,
                $dice2,
                $name,
                $oldPosition,
                self::WIN_SPACE,
                $name,
                $newPosition,
            );
        }

        return sprintf(
            self::MOVE_REGISTERED_PLAYER_MESSAGE,
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
                sprintf(self::MOVE_UNREGISTERED_PLAYER_MESSAGE, $name)
            );
        }
    }

    private function getPlayerByName(string $name): Player
    {
        return $this->players[$name];
    }

    private function isWinner(Player $player): bool
    {
        return $player->getCurrentPosition() === self::WIN_SPACE;
    }

    private function isOverlap(Player $player): bool
    {
        return $player->getCurrentPosition() > self::WIN_SPACE;
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

        $this->hasStarted = true;

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
