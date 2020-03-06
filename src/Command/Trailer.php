<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Common config for Trailer commands
 */
abstract class Trailer extends Command {

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

}
