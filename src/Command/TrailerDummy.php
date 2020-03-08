<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Chain\Job\CreateSvg;
use Trismegiste\Videodrome\Chain\Job\CreateTitlePng;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Util\AudacityMarker;

/**
 * Generates dummy assets from a sound marker file
 */
class TrailerDummy extends Command {

    protected static $defaultName = 'trailer:dummy';

    protected function configure() {
        $this->setDescription("Dummy generation of missing assets for trailer")
                ->addArgument('video', InputArgument::REQUIRED, "Empty folder for video")
                ->addArgument('picture', InputArgument::REQUIRED, "Empty folder for pictures")
                ->addArgument('vector', InputArgument::REQUIRED, "Empty folder for SVG")
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker from the sound file")
                ->addOption('width', NULL, InputOption::VALUE_REQUIRED, "Video width in pixel", 1920)
                ->addOption('height', NULL, InputOption::VALUE_REQUIRED, "Video height in pixel", 1080);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln("Dummy generation of missing assets for trailer");
        $marker = new AudacityMarker($input->getArgument('marker'));

        // panning config
        $panningFile = $input->getArgument('picture') . '/' . 'panning.cfg';
        if (!file_exists($panningFile)) {
            $output->writeln("Dummy generation of panning config");
            $panningCfg = fopen($panningFile, "w");
            foreach ($marker as $asset => $param) {
                fprintf($panningCfg, "%s +\n", $asset);
            }
            fclose($panningCfg);
        }

        // cutter config
        $cutterFile = $input->getArgument('video') . '/' . 'cutter.cfg';
        if (!file_exists($cutterFile)) {
            $output->writeln("Dummy generation of cutter config");
            $cutterCfg = fopen($cutterFile, "w");
            foreach ($marker as $asset => $param) {
                fprintf($cutterCfg, "%s 0\n", $asset);
            }
            fclose($cutterCfg);
        }

        // Title in SVG
        $svgList = [];
        foreach ($marker as $asset => $param) {
            $svg = $input->getArgument('vector') . "/$asset.svg";
            if (!file_exists($svg)) {
                $svgList[] = $asset;
            }
        }
        $cor = new CreateSvg();
        $cor->execute(new MediaList([], [
            'width' => $input->getOption('width'),
            'height' => $input->getOption('height'),
            'folder' => $input->getArgument('vector'),
            'name' => $svgList
        ]));

        // Missing pictures
        $pngList = [];
        foreach ($marker as $asset => $param) {
            $search = new Finder();
            $iter = $search->in([
                        $input->getArgument('picture'),
                        $input->getArgument('video')
                    ])
                    ->name("/^$asset\./")
                    ->files()
                    ->getIterator();
            $iter->rewind();
            if (!$iter->valid()) {
                $pngList[] = $asset;
            }
        }
        $cor = new CreateTitlePng();
        $cor->execute(new MediaList([], [
            'width' => 2 * $input->getOption('width'), // 2 multiplier for panning
            'height' => $input->getOption('height'),
            'folder' => $input->getArgument('picture'),
            'name' => $pngList
        ]));

        return 0;
    }

}
