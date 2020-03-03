<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\Cutter;

/**
 * Extracts a sequence of video and resizes it
 */
class CutterResize extends Command {

    protected static $defaultName = 'trailer:cutter';

    protected function configure() {
        $this->setDescription("Generates multiple extracts from a folder full of video and a marker for timing")
                ->addArgument('folder', InputArgument::REQUIRED, "Folder full of pictures")
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker from the sound file")
                ->addOption('config', NULL, InputOption::VALUE_REQUIRED, "The config filename in the folder for panning", "cutter.cfg")
                ->addOption('width', NULL, InputOption::VALUE_REQUIRED, "Video width in pixel", 1920)
                ->addOption('height', NULL, InputOption::VALUE_REQUIRED, "Video height in pixel", 1080);
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
            if (preg_match("/^([^\s]+)\s([.0-9]+)$/", $line, $extract)) {
                $cfg[$extract[1]] = $extract[2];
            }
        }

        return $cfg;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln("Video extractor");
        $imageFolder = $input->getArgument('folder');
        $timecode = $this->getTimecode($input->getArgument('marker'));
        $config = $this->getOptionalConfig($imageFolder, $input->getOption('config'));

        $search = new Finder();
        $iter = $search->in($imageFolder)->name('/\.(mkv|avi|webm|mp4)$/')->files();

        $listing = [];
        $duration = [];
        $starting = [];
        foreach ($iter as $picture) {
            foreach ($timecode as $detail) {
                $key = $detail['name'];
                if (preg_match('/^' . $key . "\\./", $picture->getFilename())) {
                    $listing[] = (string) $picture;
                    $duration[(string) $picture] = $detail['duration'];
                    $starting[(string) $picture] = (array_key_exists($key, $config)) ? $config[$key] : 0;
                }
            }
        }

        $cor = new Cutter();
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute($listing, [
            'duration' => $duration,
            'start' => $starting,
            'width' => $input->getOption('width'),
            'height' => $input->getOption('height'),
        ]);
    }

}
