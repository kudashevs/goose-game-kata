<?php

namespace Kudashevs\GooseGameKata\Tests\Unit;

use Kudashevs\GooseGameKata\DiceRoller;
use PHPUnit\Framework\TestCase;

class DiceRollerTest extends TestCase
{
    /** @test */
    public function it_can_be_rolled()
    {
        $min = 1;
        $max = 6;

        $dice = new DiceRoller();
        $value = $dice->roll($min, $max);

        $this->assertTrue($value >= $min && $value <= $max);
    }
}
