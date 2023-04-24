<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

use Exception;

class DiceRoller
{
    protected const MIN_VALUE = 1;
    protected const MAX_VALUE = 6;

    public function roll(int $min = self::MIN_VALUE, int $max = self::MAX_VALUE): int
    {
        try {
            $randomValue = random_int($min, $max);
        } catch (Exception $e) {
            return rand($min, $max);
        }

        return $randomValue;
    }
}
