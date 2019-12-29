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
    protected $framerate = 30;

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

        $nh = round($this->outputFormat['w'] / $ratio);
        $nw = round($this->outputFormat['h'] * $ratio);

        if ($nh >= $this->outputFormat['h']) {
            $resize = $this->outputFormat['w'] . 'x' . $nh;
            $direction = "v";
            $speed = -($nh - $this->outputFormat['h']) / $duration;
            $equation = "y=$speed*t";
        } else {
            $resize = $nw . 'x' . $this->outputFormat['h'];
            $direction = ">";
            $speed = ($nw - $this->outputFormat['w']) / $duration;
            $equation = "x=$speed*t";
        }
        $output->writeln("format $resize $direction $speed");

        shell_exec("convert $picture -resize $resize resized.png");
        shell_exec("ffmpeg -y -f lavfi -i color=c=blue:s={$this->outputFormat['w']}x{$this->outputFormat['h']}:d=$duration:r=30 -c:v huffyuv yolo.avi");
        shell_exec("ffmpeg -y -i yolo.avi -i resized.png -filter_complex \"[0:v][1:v]overlay=$equation:enable='between(t,0,$duration)'\" -c:v huffyuv result.avi");
    }

}
