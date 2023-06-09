<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata\Domain;

class Player
{
    private const DEFAULT_POSITION = 0;

    private int $previousPosition = self::DEFAULT_POSITION;

    private int $currentPosition = self::DEFAULT_POSITION;

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function move(int $first, int $second): void
    {
        $this->previousPosition = $this->currentPosition;

        $this->currentPosition += ($first + $second);
    }

    public function getCurrentPosition(): int
    {
        return $this->currentPosition;
    }

    public function getPreviousPosition(): int
    {
        return $this->previousPosition;
    }

    public function updatePosition(int $position): void
    {
        $this->previousPosition = $this->currentPosition;

        $this->currentPosition = $position;
    }
}
