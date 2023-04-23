<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

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

    public function getPosition(): int
    {
        return $this->currentPosition;
    }

    public function getPreviousPosition(): int
    {
        return $this->previousPosition;
    }
}
