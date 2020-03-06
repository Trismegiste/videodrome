<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Chain\AggregateJob;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\AddingSound;
use Trismegiste\Videodrome\Chain\Job\Cutter;
use Trismegiste\Videodrome\Chain\Job\ImageExtender;
use Trismegiste\Videodrome\Chain\Job\ImagePanning;
use Trismegiste\Videodrome\Chain\Job\SvgOverlay;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;
use Trismegiste\Videodrome\Chain\MetaFileInfo;
use Trismegiste\Videodrome\Util\AudacityMarker;
use Trismegiste\Videodrome\Util\CutterCfg;
use Trismegiste\Videodrome\Util\PanningCfg;

/**
 * Build Trailer
 */
class Trailer extends Command {

    protected static $defaultName = 'trailer:build';

    protected function configure() {
        $this->setDescription("Build a trailer from a lot of assets : videos, sound, config files, marker...")
                ->addArgument('video', InputArgument::REQUIRED, "Folder full of video to extract clips")
                ->addOption('vidcfg', NULL, InputOption::VALUE_REQUIRED, "The config filename in the video folder for cutting clips", "cutter.cfg")
                ->addArgument('picture', InputArgument::REQUIRED, "Folder full of picture for making panning")
                ->addOption('pixcfg', NULL, InputOption::VALUE_REQUIRED, "The config filename in the picture folder for panning", "panning.cfg")
                ->addArgument('vector', InputArgument::REQUIRED, "Folder full of SVG for overlaying (title)")
                ->addArgument('sound', InputArgument::REQUIRED, "a sound file")
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker from the sound file")
                ->addOption('width', NULL, InputOption::VALUE_REQUIRED, "Video width in pixel", 1920)
                ->addOption('height', NULL, InputOption::VALUE_REQUIRED, "Video height in pixel", 1080);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $marker = new AudacityMarker($input->getArgument('marker'));
        $panningCfg = new PanningCfg($input->getArgument('picture') . '/' . $input->getOption('pixcfg'));
        $cutterCfg = new CutterCfg($input->getArgument('video') . '/' . $input->getOption('vidcfg'));
        $media = [];

        foreach ($marker as $key => $entry) {
            $search = new Finder();
            $iter = $search->in([$input->getArgument('picture'), $input->getArgument('video')])->name("/^$key\./")->files()->getIterator();
            $iter->rewind();
            if ($iter->valid()) {
                $found = $iter->current();
                $ext = $found->getExtension();
                $meta = [
                    'start' => $marker->getStart($key),
                    'duration' => $marker->getDuration($key),
                    'width' => $input->getOption('width'),
                    'height' => $input->getOption('height')
                ];
                // meta depending on video or picture :
                if (in_array($ext, ['jpg', 'png'])) {
                    $meta['direction'] = $panningCfg->getDirection($key);
                }
                if (in_array($ext, ['mkv', 'avi', 'webm', 'mp4'])) {
                    $meta['cutBefore'] = $cutterCfg->getStart($key);
                }
                // search for SVG overlay :
                $svgOverlay = $input->getArgument('vector') . "/$key.svg";
                if (file_exists($svgOverlay)) {
                    $meta['svg'] = $svgOverlay;
                } else {
                    throw new RuntimeException("No SVG found for key '$key");
                }
                // adding sound file :
                $meta['sound'] = $input->getArgument('sound');
                // create MetaFileInfo
                $media[] = new MetaFileInfo((string) $found, $meta);
            }
        }

        if (count($media) !== count($marker->getIterator())) {
            throw new RuntimeException("Marker has " . count($marker) . " clips and " . count($media) . " was found");
        }

        $output->writeln("Build a trailer");
        $cor = new AddingSound(new VideoConcat(new SvgOverlay(new AggregateJob([
            new ImagePanning(new ImageExtender()),
            new Cutter()
        ]))));
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute($media);

        return 0;
    }

}