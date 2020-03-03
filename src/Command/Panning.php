<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\ImageExtender;
use Trismegiste\Videodrome\Chain\Job\ImagePanning;

/**
 * Creates a panning from a folder full of image and a marker for timing
 */
class Panning extends Command {

    protected static $defaultName = 'trailer:panning';

    protected function configure() {
        $this->setDescription("Generates multiple pannings from a folder full of pictures and a marker for timing")
                ->addArgument('folder', InputArgument::REQUIRED, "Folder full of pictures")
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker from the sound file")
                ->addOption('config', NULL, InputOption::VALUE_REQUIRED, "The config filename in the folder for panning", "videodrome.cfg");
    }

    protected function getTimecode(string $fch): array {
        $timing = file($fch);
        $timecode = [];
        foreach ($timing as $clip) {
            if (preg_match("/^([.0-9]+)\s([.0-9]+)\s([^\s]+)$/", $clip, $extract)) {
                $timecode[] = [
                    'start' => (float) $extract[1],
                    'duration' => $extract[2] - $extract[1],
                    'name' => $extract[3]
                ];
            }
        }

        return $timecode;
    }

    protected function getOptionalConfig(string $dir, string $fch): array {
        $content = file($dir . '/' . $fch);
        $cfg = [];
        foreach ($content as $line) {
            if (preg_match("/^([^\s]+)\s([\+|-])$/", $line, $extract)) {
                $cfg[$extract[1]] = $extract[2];
            }
        }

        return $cfg;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $imageFolder = $input->getArgument('folder');
        $timecode = $this->getTimecode($input->getArgument('marker'));
        $config = $this->getOptionalConfig($imageFolder, $input->getOption('config'));

        $cor = new ImagePanning(new ImageExtender());
        $cor->setLogger(new ConsoleLogger($output));

        $search = new Finder();
        $iter = $search->in($imageFolder)->name('/\.(jpg|png)$/')->files();

        $listing = [];
        $duration = [];
        $direction = [];
        foreach ($iter as $picture) {
            foreach ($timecode as $detail) {
                $key = $detail['name'];
                if (preg_match('/^' . $key . "\\./", $picture->getFilename())) {
                    $listing[] = (string) $picture;
                    $duration[$key . '-extended.png'] = $detail['duration'];
                    $direction[$key . '-extended.png'] = (array_key_exists($key, $config)) ? $config[$key] : '+';
                }
            }
        }

        $cor->execute($listing, [
            'duration' => $duration,
            'direction' => $direction,
            'width' => 450,
            'height' => 500,
        ]);
    }

}
