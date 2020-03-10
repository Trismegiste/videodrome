<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Chain\AggregateJob;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\AddingSound;
use Trismegiste\Videodrome\Chain\Job\Cutter;
use Trismegiste\Videodrome\Chain\Job\ImageExtender;
use Trismegiste\Videodrome\Chain\Job\ImagePanning;
use Trismegiste\Videodrome\Chain\Job\SvgOverlay;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Command\Trailer;
use Trismegiste\Videodrome\Util\AudacityMarker;
use Trismegiste\Videodrome\Util\CutterCfg;
use Trismegiste\Videodrome\Util\PanningCfg;

/**
 * Build Trailer
 */
class TrailerBuilder extends Trailer {

    protected static $defaultName = 'trailer:build';

    protected function configure() {
        parent::configure();
        $this->setDescription("Build a trailer from a lot of assets : videos, sound, config files, marker...");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $marker = new AudacityMarker($input->getArgument('marker'));
        $panningCfg = new PanningCfg(join_paths($input->getArgument('picture'), $input->getOption('pixcfg')));
        $cutterCfg = new CutterCfg(join_paths($input->getArgument('video'), $input->getOption('vidcfg')));
        $media = new MediaList([], ['sound' => $input->getArgument('sound')]);

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
                $svgOverlay = join_paths($input->getArgument('vector'), "$key.svg");
                if (file_exists($svgOverlay)) {
                    $meta['svg'] = $svgOverlay;
                } else {
                    throw new RuntimeException("No SVG found for key '$key");
                }
                // create MetaFileInfo
                $media[] = new MediaFile((string) $found, $meta);
            }
        }

        if (count($media) !== count($marker->getIterator())) {
            throw new RuntimeException("Marker has " . count($marker) . " clips and " . count($media) . " was found");
        }

        $io->title("Build a trailer");
        $cor = new AddingSound(new VideoConcat(new SvgOverlay(new AggregateJob([
            new ImagePanning(new ImageExtender()),
            new Cutter()
        ]))));
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute($media);

        return 0;
    }

}
