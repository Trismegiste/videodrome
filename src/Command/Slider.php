<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;

/**
 * Description of Slider
 */
class Slider extends Command {

    // the name of the command
    protected static $defaultName = 'slider:build';
    protected $outputFormat = ['w' => 1920, 'h' => 1080];

    protected function configure() {
        $this->setDescription("Generates a video slide from a picture")
                ->addArgument('picture', InputArgument::REQUIRED, "Picture file")
                ->addArgument('duration', InputArgument::REQUIRED, "Duration in seconds");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $picture = $input->getArgument('picture');
        $duration = $input->getArgument('duration');

        $info = getimagesize($picture);
        $width = $info[0];
        $height = $info[1];
        $ratio = $width / $height;

        $nh = $this->outputFormat['w'] / $ratio;
        $nw = $this->outputFormat['h'] * $ratio;

        if ($nh >= $this->outputFormat['h']) {
            $resize = $this->outputFormat['w'] . 'x' . (int) $nh;
            $direction = "v";
        } else {
            $resize = (int) $nw . 'x' . $this->outputFormat['h'];
            $direction = ">";
        }
        $output->writeln("format $resize $direction");
    }

}
