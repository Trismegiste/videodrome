<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Videodrome\Conference\VideoConcat;
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
    protected $timecode;

    protected function configure() {
        $this->setDescription("Generates a trailer-like video with pictures, timecode and sound")
                ->addArgument('config', InputArgument::REQUIRED, "a json config file");
    }

    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->config = json_decode(file_get_contents($input->getArgument('config')));
        $timing = file($this->config->file->marker);
        $this->timecode = [];
        foreach ($timing as $clip) {
            if (preg_match("/^([.0-9]+)\s([.0-9]+)\s([^\s]+)$/", $clip, $extract)) {
                $this->timecode[] = [
                    'start' => (float) $extract[1],
                    'duration' => $extract[2] - $extract[1],
                    'name' => $extract[3]
                ];
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        var_dump($this->config);

        $clipname = [];
        foreach ($this->timecode as $seq) {
            $bg = $this->config->file->background . $seq['name'] . '.jpg';
            $resizing = new ImageResizeForPanning($bg, $this->outputFormat['w'], $this->outputFormat['h']);
            $resizing->exec();
            $panning = new PictureToPanning($this->outputFormat['w'], $this->outputFormat['h'], $seq['duration'], $this->framerate, $resizing->getResizedName());
            $panning->exec();
            $clipname[] = $panning->getFilename();
            $panning->clean();
            $resizing->clean();
        }

        $compil = new VideoConcat($clipname, $this->config->file->sound);
        $compil->exec();
        $compil->clean();
    }

}
