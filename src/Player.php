<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

class Player
{
    private const DEFAULT_POSITION = 0;

    private string $name;

    private int $position = self::DEFAULT_POSITION;

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
        $this->position += ($first + $second);
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
