<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Util\AudacityMarker;
use Trismegiste\Videodrome\Util\CutterCfg;
use Trismegiste\Videodrome\Util\PanningCfg;

/**
 * Checks if all assets for building a trailer is ok
 */
class TrailerCheck extends Trailer {

    protected static $defaultName = 'trailer:check';

    protected function execute(InputInterface $input, OutputInterface $output) {
        $error = 0;
        $output->writeln("Check for all assets...");

        if (!file_exists($input->getArgument('marker'))) {
            throw new RuntimeException("No marker file found");
        }
        $marker = new AudacityMarker($input->getArgument('marker'));

        if (!file_exists($input->getArgument('picture') . '/' . $input->getOption('pixcfg'))) {
            throw new RuntimeException("No panning config file found");
        }
        $panningCfg = new PanningCfg($input->getArgument('picture') . '/' . $input->getOption('pixcfg'));

        if (!file_exists($input->getArgument('video') . '/' . $input->getOption('vidcfg'))) {
            throw new RuntimeException("No cutting config file found");
        }
        $cutterCfg = new CutterCfg($input->getArgument('video') . '/' . $input->getOption('vidcfg'));

        foreach ($marker as $key => $entry) {
            $search = new Finder();
            $iter = $search->in([$input->getArgument('picture'), $input->getArgument('video')])->name("/^$key\./")->files()->getIterator();
            // asset for key
            if (count($iter) !== 1) {
                $output->writeln("No picture of movie clip for '$key'");
                $error++;
            }
            // check if svg
            $svgOverlay = $input->getArgument('vector') . "/$key.svg";
            if (!file_exists($svgOverlay)) {
                $output->writeln("No SVG found for key '$key'");
                $error++;
            }
        }

        $output->writeln($error === 0 ? "Everything seems OK" : "Found $error errors");

        return $error;
    }

}