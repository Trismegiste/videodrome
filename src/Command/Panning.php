<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Videodrome\Chain\Job\ImageExtender;
use Trismegiste\Videodrome\Chain\Job\ImagePanning;

/**
 * Creates a panning from a folder full of image and a marker for timing
 */
class Panning extends Command {

    protected static $defaultName = 'trailer:panning';

    protected function configure() {
        $this->setDescription("Generates multiple pannings from a folder full of pictures and a marker for timing")
                ->addArgument('folder', InputArgument::REQUIRED, "a folder full of pictures")
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker from the sound file")
                ->addOption('config', NULL, InputOption::VALUE_REQUIRED, "The config filenae in the folder for panning", "videodrome.cfg");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $imageFolder = $input->getArgument('folder');
        $markerFile = $input->getArgument('marker');
        $configFile = $input->getOption('config');


        $cor = new ImagePanning(new ImageExtender());
    }

}
