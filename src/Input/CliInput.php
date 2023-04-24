<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata\Input;

class CliInput implements InputInterface
{
    public function readLine(): string
    {
        $input = (string)fgets(STDIN);

        /*
         * We want the input to be cleared from any new lines.
         */
        return preg_replace('/(\r)?\n/', '', $input);
    }
}
