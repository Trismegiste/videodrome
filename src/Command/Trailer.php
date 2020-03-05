<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Chain\AggregateJob;
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
                $meta = [];
                if (in_array($ext, ['jpg', 'png'])) {
                    $meta = [
                        'duration' => $marker->getDuration($key),
                        'direction' => $panningCfg->getDirection($key),
                        'width' => $input->getOption('width'),
                        'height' => $input->getOption('height')
                    ];
                }
                if (in_array($ext, ['mkv', 'avi', 'webm', 'mp4'])) {
                    $meta = [
                        'duration' => $marker->getDuration($key),
                        'start' => $cutterCfg->getStart($key),
                        'width' => $input->getOption('width'),
                        'height' => $input->getOption('height')
                    ];
                }
                $svgOverlay = $input->getArgument('vector') . "/$key.svg";
                if (file_exists($svgOverlay)) {
                    $meta['svg'] = $svgOverlay;
                }
                $media[] = new MetaFileInfo((string) $found, $media);
            }
        }


        $cor = new AddingSound(new VideoConcat(new SvgOverlay(new AggregateJob([
            new ImagePanning(new ImageExtender()),
            new Cutter
        ]))));

        $output->writeln(count($media));
        foreach ($media as $f) {
            $output->writeln((string) $f);
        }
        //    $cor->execute($media);
    }

}
