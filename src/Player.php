<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

class Player
{
    private string $name;

    private int $position = 0;

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
