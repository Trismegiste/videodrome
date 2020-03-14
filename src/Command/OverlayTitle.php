<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\SvgOverlay;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

/**
 * Adds a SVG title on videos in a folder
 */
class OverlayTitle extends Command {

    protected static $defaultName = 'trailer:overlay';

    protected function configure() {
        $this->setDescription("Adds overlay titles on video. Titles are SVG files stored in a folder")
                ->addArgument('video', InputArgument::REQUIRED, "Folder full of video")
                ->addArgument('vector', InputArgument::REQUIRED, "Folder full of SVG")
                ->addOption('suffix', NULL, InputOption::VALUE_REQUIRED, "Suffix between filename key and extension", "-(extended|cut)");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $io->title("Video overlay with SVG");
        $vectorFolder = $input->getArgument('vector');
        $videoFolder = $input->getArgument('video');
        $suffix = $input->getOption('suffix');

        $search = new Finder();
        $iter = $search->in($vectorFolder)->name('*.svg')->files();

        $listing = new MediaList();
        foreach ($iter as $svg) {
            $finder = new Finder();
            $tmp = $finder->in($videoFolder)->name("/^" . $svg->getBasename('.svg') . "$suffix.avi$/")->getIterator();
            $tmp->rewind();

            if ($tmp->valid()) {
                $listing[] = new MediaFile($tmp->current(), ['svg' => (string) $svg]);
            }
        }
        $cor = new SvgOverlay();
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute($listing);

        return 0;
    }

}
