#!/usr/bin/env php
<?php
declare(strict_types=1);

use Kudashevs\GooseGameKata\GameApp;
use Kudashevs\GooseGameKata\Input\CliInput;
use Kudashevs\GooseGameKata\Output\CliOutput;

require_once __DIR__ . '/../vendor/autoload.php';

if (PHP_VERSION_ID < 70400) {
    fwrite(STDERR, "The app requires a minimum version of PHP 7.4.0.\n");
    exit(1);
}

$input = new CliInput();
$output = new CliOutput();

$app = new GameApp($input, $output);
$app->run();
