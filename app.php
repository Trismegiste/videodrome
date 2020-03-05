<?php

// Main

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

// register commands
$application->add(new \Trismegiste\Videodrome\Command\Conference());
$application->add(new \Trismegiste\Videodrome\Command\SystemCheck());
$application->add(new \Trismegiste\Videodrome\Command\Panning());
$application->add(new \Trismegiste\Videodrome\Command\CutterResize());
$application->add(new \Trismegiste\Videodrome\Command\OverlayTitle());
$application->add(new \Trismegiste\Videodrome\Command\Concatenator());
$application->add(new \Trismegiste\Videodrome\Command\MuxingSound());
$application->add(new \Trismegiste\Videodrome\Command\Trailer());

$application->run();
