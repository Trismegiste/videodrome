<?php

// Main

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

// register commands
$application->add(new \Trismegiste\Videodrome\Command\Conference());
$application->add(new \Trismegiste\Videodrome\Command\Trailer());
$application->add(new \Trismegiste\Videodrome\Command\SystemCheck());
$application->add(new \Trismegiste\Videodrome\Command\Panning());

$application->run();
