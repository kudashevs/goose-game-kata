<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata\Output;

interface OutputInterface
{
    /**
     * Write a line to an output.
     *
     * @param string $str
     */
    public function writeLine(string $str): void;

    /**
     * Terminate the output session.
     *
     * @param int $code
     */
    public function terminate(int $code): void;
}
