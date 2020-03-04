<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\PngOverlay;
use Trismegiste\Videodrome\Chain\Job\SvgToPng;

/**
 * Adds a SVG title on videos in a folder
 */
class OverlayTitle extends Command {

    protected static $defaultName = 'trailer:cutter';

    protected function configure() {
        $this->setDescription("Adds overlay titles on video")
                ->addArgument('video', InputArgument::REQUIRED, "Folder full of video")
                ->addArgument('vector', InputArgument::REQUIRED, "Folder full of SVG")
                ->addOption('suffix', NULL, InputOption::VALUE_REQUIRED, "Suffix between filename key and extension", "-(extended|cut)");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln("Video overlay with SVG");
        $vectorFolder = $input->getArgument('vector');
        $videoFolder = $input->getArgument('video');
        $suffix = $input->getOption('suffix');

        $search = new Finder();
        $iter = $search->in($vectorFolder)->name('*.svg')->files();

        $listing = [];
        $videoList = [];
        foreach ($iter as $svg) {
            $finder = new Finder();
            $finder->in($videoFolder)->name("/^" . $svg->getFilename() . "$suffix.avi$/");
            if ($finder->count() === 1) {
                $video = iterator_to_array($finder);
                $listing[] = (string) $svg;
                $videoList[$svg->getFilename() . '.png'] = $video[0];
            }
        }

        $cor = new PngOverlay(new SvgToPng());
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute($listing, ['video' => $videoList]);
    }

}
