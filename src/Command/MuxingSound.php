<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\AddingSound;
use Trismegiste\Videodrome\Chain\MediaFile;

/**
 * Adds soundtrack to a video
 */
class MuxingSound extends Command {

    protected static $defaultName = 'trailer:muxing';

    protected function configure() {
        $this->setDescription("Mixing video file and sound file into a new video file")
                ->addArgument('video', InputArgument::REQUIRED, "a video file")
                ->addArgument('sound', InputArgument::REQUIRED, "a sound file");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $video = $input->getArgument('video');
        $sound = $input->getArgument('sound');

        $output->writeln("Mixing video and sound");
        $cor = new AddingSound();
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute(new MediaFile($video, ['sound' => $sound]));

        return 0;
    }

}
