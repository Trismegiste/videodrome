<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Util\AudacityMarker;
use Trismegiste\Videodrome\Util\CutterCfg;
use Trismegiste\Videodrome\Util\PanningCfg;

/**
 * Checks if all assets for building a trailer is ok
 */
class TrailerCheck extends Trailer {

    protected static $defaultName = 'trailer:check';

    protected function configure() {
        parent::configure();
        $this->setDescription("Check all assets for building a trailer video");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $error = 0;
        $io->title("Check for all assets");

        // check sound file
        if (!file_exists($input->getArgument('sound'))) {
            throw new RuntimeException("No sound file found");
        }

        // check marker
        if (!file_exists($input->getArgument('marker'))) {
            throw new RuntimeException("No marker file found");
        }
        $marker = new AudacityMarker($input->getArgument('marker'));

        // check panning config
        $panningFile = join_paths($input->getArgument('picture'), $input->getOption('pixcfg'));
        if (!file_exists($panningFile)) {
            throw new RuntimeException("No panning config file found");
        }
        $panningCfg = new PanningCfg($panningFile);

        // check cutting config
        $cutterFile = join_paths($input->getArgument('video'), $input->getOption('vidcfg'));
        if (!file_exists($cutterFile)) {
            throw new RuntimeException("No cutting config file found");
        }
        $cutterCfg = new CutterCfg($cutterFile);

        foreach ($marker as $key => $entry) {
            $search = new Finder();
            $iter = $search->in([$input->getArgument('picture'), $input->getArgument('video')])->name("/^$key\./")->files()->getIterator();
            // asset for key
            if (count($iter) !== 1) {
                $io->caution("No picture of movie clip for '$key'");
                $error++;
            }
            // check if svg
            $svgOverlay = join_paths($input->getArgument('vector'), "$key.svg");
            if (!file_exists($svgOverlay)) {
                $io->caution("No SVG found for key '$key'");
                $error++;
            }
        }

        if ($error === 0) {
            $io->success("Everything seems OK");
        } else {
            $io->caution("Found $error errors");
        }

        $io->section("Timing");
        $tmp = iterator_to_array($marker);
        $io->table(['Key', 'Duration (sec)'], array_map(function($k, $v) {
                    return [$k, $v['duration']];
                }, array_keys($tmp), $tmp));


        return $error;
    }

}
