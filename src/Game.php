<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

use DomainException;

class Game
{
    private const START_COMMAND = ' ';
    private const WIN_SPACE = 63;
    private const BRIDGE_SPACE = 6;
    private const BRIDGE_JUMP_TO = 12;
    private const GOOSE_SPACES = [5, 9, 14, 18, 23, 27];

    private const SPACE_NAMES = [
        0 => 'Start',
        6 => 'The Bridge',
    ];
    private const DICE_MIN = 1;
    private const DICE_MAX = 6;

    private const ADD_PLAYER_REGEX = '/add player (?P<player>.+)$/iSU';
    private const MOVE_PLAYER_REGEX = '/move (?P<player>.+)(\s+(?P<dice1>[' . self::DICE_MIN . '-' . self::DICE_MAX . ']),\s+(?P<dice2>[' . self::DICE_MIN . '-' . self::DICE_MAX . ']))?$/iSU';

    private const UNKNOWN_COMMAND_MESSAGE = 'unknown command';
    private const LIST_PLAYERS_MESSAGE = 'players: %s';
    private const PLAYER_ALREADY_EXISTS_MESSAGE = '%s: already existing player';
    private const NOT_ENOUGH_PLAYERS_MESSAGE = 'There is no enough participants';

    private const HAS_STARTED_MESSAGE = 'You cannot add %s. The game has already started.';
    private const MOVE_UNREGISTERED_PLAYER_MESSAGE = 'You cannot move %s. The player does not exist.';
    private const MOVE_REGISTERED_PLAYER_MESSAGE = '%s rolls %s, %s. %s moves from %s to %s';
    private const PLAYER_WINS_MESSAGE = '. %s Wins!!';
    private const OVERLAP_JUMP_BACK_MESSAGE = '. %s bounces! Pippo returns to %s';
    private const BRIDGE_JUMP_MESSAGE = '. %s jumps to %s';
    private const GOOSE_JUMP_MESSAGE = ', The Goose. %s moves again and goes to %s';
    private const HAS_WINNER_MESSAGE = 'We have a winner. The game is over!';

    private bool $hasStarted = false;

    private bool $hasWinner = false;

    private array $players = [];

    private DiceRoller $roller;

    public function __construct()
    {
        $this->roller = new DiceRoller();
    }

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

        if (preg_match(self::MOVE_PLAYER_REGEX, $input, $matches) === 1) {
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
        return is_numeric($number) ? (int)$number : $this->roller->roll(self::DICE_MIN, self::DICE_MAX);
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
                self::MOVE_REGISTERED_PLAYER_MESSAGE . self::PLAYER_WINS_MESSAGE,
                $name,
                $dice1,
                $dice2,
                $name,
                $this->getSpaceTitle($player->getPreviousPosition()),
                $this->getSpaceTitle($player->getCurrentPosition()),
                $name,
            );
        }

        if ($this->isBridge($player)) {
            $oldPosition = $player->getPreviousPosition();
            $newPosition = $player->getCurrentPosition();

            $player->updatePosition(self::BRIDGE_JUMP_TO);

            return sprintf(
                self::MOVE_REGISTERED_PLAYER_MESSAGE . self::BRIDGE_JUMP_MESSAGE,
                $name,
                $dice1,
                $dice2,
                $name,
                $this->getSpaceTitle($oldPosition),
                $this->getSpaceTitle($newPosition),
                $name,
                self::BRIDGE_JUMP_TO,
            );
        }

        if ($this->isGoose($player)) {
            $oldPosition = $player->getPreviousPosition();
            $newPosition = $player->getCurrentPosition();
            $jumpPosition = $newPosition + ($newPosition - $oldPosition);

            $player->updatePosition($jumpPosition);

            return sprintf(
                self::MOVE_REGISTERED_PLAYER_MESSAGE . self::GOOSE_JUMP_MESSAGE,
                $name,
                $dice1,
                $dice2,
                $name,
                $this->getSpaceTitle($oldPosition),
                $this->getSpaceTitle($newPosition),
                $name,
                $jumpPosition,
            );
        }

        if ($this->isOverlap($player)) {
            $oldPosition = $player->getPreviousPosition();
            $overlap = $player->getCurrentPosition() - self::WIN_SPACE;
            $newPosition = self::WIN_SPACE - $overlap;

            $player->updatePosition($newPosition);

            return sprintf(
                self::MOVE_REGISTERED_PLAYER_MESSAGE . self::OVERLAP_JUMP_BACK_MESSAGE,
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

    private function isBridge(Player $player): bool
    {
        return $player->getCurrentPosition() === self::BRIDGE_SPACE;
    }

    private function isGoose(Player $player)
    {
        return in_array($player->getCurrentPosition(), self::GOOSE_SPACES);
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

    public function updateDiceRoller(DiceRoller $roller): void
    {
        $this->roller = $roller;
    }
}
