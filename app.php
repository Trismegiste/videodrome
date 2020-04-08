<?php

// Main

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application("Videodrome", "1.6");

// register commands
$application->add(new \Trismegiste\Videodrome\Command\Conference());
$application->add(new \Trismegiste\Videodrome\Command\SystemCheck());
$application->add(new \Trismegiste\Videodrome\Command\Panning());
$application->add(new \Trismegiste\Videodrome\Command\CutterResize());
$application->add(new \Trismegiste\Videodrome\Command\OverlayTitle());
$application->add(new \Trismegiste\Videodrome\Command\Concatenator());
$application->add(new \Trismegiste\Videodrome\Command\MuxingSound());
$application->add(new \Trismegiste\Videodrome\Command\TrailerBuilder());
$application->add(new \Trismegiste\Videodrome\Command\TrailerCheck());
$application->add(new \Trismegiste\Videodrome\Command\ConferenceGif());
$application->add(new \Trismegiste\Videodrome\Command\TrailerDummy());
$application->add(new \Trismegiste\Videodrome\Command\EditingConfig());
$application->add(new \Trismegiste\Videodrome\Command\EditingSort());
$application->add(new \Trismegiste\Videodrome\Command\EditingConcatYt());
$application->add(new \Trismegiste\Videodrome\Command\MiscConcatYt());

$application->run();
