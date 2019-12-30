<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Videodrome\Trailer\ImageResizeForPanning;
use Trismegiste\Videodrome\Trailer\PictureToPanning;

/**
 * Trailer creates a trailer-like video with pictures, timecode and sound
 */
class Trailer extends Command {

    // the name of the command
    protected static $defaultName = 'trailer:build';
    protected $outputFormat = ['w' => 1920, 'h' => 1080];
    protected $framerate = 30;
    protected $config;

    protected function configure() {
        $this->setDescription("Generates a trailer-like video with pictures, timecode and sound")
                ->addArgument('config', InputArgument::REQUIRED, "a json config file");
    }

    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->config = json_decode(file_get_contents($input->getArgument('config')));
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        var_dump($this->config);
    }

}
