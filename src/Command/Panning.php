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
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Util\AudacityMarker;
use Trismegiste\Videodrome\Util\PanningCfg;

/**
 * Creates a panning from a folder full of image and a marker for timing
 */
class Panning extends Command {

    protected static $defaultName = 'trailer:panning';

    protected function configure() {
        $this->setDescription("Generates multiple pannings from a folder full of pictures and a marker for timing")
                ->addArgument('folder', InputArgument::REQUIRED, "Folder full of pictures")
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker from the sound file")
                ->addOption('config', NULL, InputOption::VALUE_REQUIRED, "The config filename in the folder for panning", "panning.cfg")
                ->addOption('width', NULL, InputOption::VALUE_REQUIRED, "Video width in pixel", 1920)
                ->addOption('height', NULL, InputOption::VALUE_REQUIRED, "Video height in pixel", 1080);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln("Panning generator");
        $imageFolder = $input->getArgument('folder');
        $timecode = new AudacityMarker($input->getArgument('marker'));
        $config = new PanningCfg(join_paths($imageFolder, $input->getOption('config')));

        $search = new Finder();
        $iter = $search->in($imageFolder)->name('/\.(jpg|png)$/')->files();

        $listing = new MediaList();
        foreach ($iter as $picture) {
            foreach ($timecode as $key => $detail) {
                if (preg_match('/^' . $key . "\\./", $picture->getFilename())) {
                    $metafile = new MediaFile($picture, [
                        'duration' => $timecode->getDuration($key),
                        'direction' => $config->getDirection($key),
                        'width' => $input->getOption('width'),
                        'height' => $input->getOption('height')
                    ]);
                    $listing[] = $metafile;
                }
            }
        }

        $cor = new ImagePanning(new ImageExtender());
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute($listing);
    }

}
