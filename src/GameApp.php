<?php

declare(strict_types=1);

namespace Kudashevs\GooseGameKata;

use Kudashevs\GooseGameKata\Domain\Game;
use Kudashevs\GooseGameKata\Input\InputInterface;
use Kudashevs\GooseGameKata\Output\OutputInterface;

class GameApp
{
    private const WELCOME_MESSAGE = 'Welcome to the Goose Game. Enter your command:';
    private const STOP_WORD = 'Stop!';

    private OutputInterface $output;
    private InputInterface $input;
    private Game $game;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->game = new Game();
    }

    public function run(): void
    {
        $this->output->writeLine(self::WELCOME_MESSAGE);

        while (($command = $this->input->readLine()) !== self::STOP_WORD) {
            $this->output->writeLine(
                $this->game->process($command)
            );
        }

        $this->output->terminate(0);
    }
}
