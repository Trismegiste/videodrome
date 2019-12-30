<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Videodrome\Trailer\ImageResizeForPanning;
use Trismegiste\Videodrome\Trailer\PictureToPanning;

/**
 * Description of Slider
 */
class Slider extends Command {

    // the name of the command
    protected static $defaultName = 'slider:build';
    protected $outputFormat = ['w' => 1920, 'h' => 1080];
    protected $framerate = 30;

    protected function configure() {
        $this->setDescription("Generates a video slide from a picture")
                ->addArgument('picture', InputArgument::REQUIRED, "Picture file")
                ->addArgument('duration', InputArgument::REQUIRED, "Duration in seconds");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $picture = $input->getArgument('picture');
        $duration = (float) $input->getArgument('duration');

        $resizing = new ImageResizeForPanning($picture, $this->outputFormat['w'], $this->outputFormat['h']);
        $resizing->exec();
        $panning = new PictureToPanning($this->outputFormat['w'], $this->outputFormat['h'], $duration, $this->framerate, $resizing->getResizedName(), '+');
        $panning->exec();
        $panning->clean();
        $resizing->clean();
    }

}
