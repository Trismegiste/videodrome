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
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Util\AudacityMarker;
use Trismegiste\Videodrome\Util\CutterCfg;

/**
 * Extracts a sequence of video and resizes it
 */
class CutterResize extends Command {

    protected static $defaultName = 'trailer:cutter';

    protected function configure() {
        $this->setDescription("Generates multiple extracts from a folder full of video and a marker for timing")
                ->addArgument('folder', InputArgument::REQUIRED, "Folder full of pictures")
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker from the sound file")
                ->addOption('config', NULL, InputOption::VALUE_REQUIRED, "The config filename in the folder for cutting", "cutter.cfg")
                ->addOption('width', NULL, InputOption::VALUE_REQUIRED, "Video width in pixel", 1920)
                ->addOption('height', NULL, InputOption::VALUE_REQUIRED, "Video height in pixel", 1080);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln("Video extractor");
        $imageFolder = $input->getArgument('folder');
        $timecode = new AudacityMarker($input->getArgument('marker'));
        $config = new CutterCfg($imageFolder . '/' . $input->getOption('config'));

        $search = new Finder();
        $iter = $search->in($imageFolder)->name('/\.(mkv|avi|webm|mp4)$/')->files();

        $listing = new MediaList();
        foreach ($iter as $video) {
            foreach ($timecode as $key => $detail) {
                if (preg_match('/^' . $key . "\\./", $video->getFilename())) {
                    $metafile = new MediaFile($video, [
                        'duration' => $timecode->getDuration($key),
                        'cutBefore' => $config->getStart($key),
                        'width' => $input->getOption('width'),
                        'height' => $input->getOption('height')
                    ]);
                    $listing[] = $metafile;
                }
            }
        }

        $cor = new Cutter();
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute($listing);
    }

}
