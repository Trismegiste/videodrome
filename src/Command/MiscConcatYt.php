<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\ConcatYt;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

/**
 * Concat video stored in a folder and compress it for youtube
 */
class MiscConcatYt extends Command
{

    protected static $defaultName = 'misc:concat';

    protected function configure()
    {
        $this->setDescription('Concat videos from a folder and compress for Youtube')
            ->addArgument('folder', InputArgument::REQUIRED, 'A folder with video')
            ->addArgument('target', InputArgument::OPTIONAL, 'output filename (mp4)', 'output.mp4');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $search = new Finder();
        $search->in($input->getArgument('folder'))
            ->depth('< 1')
            ->files()
            ->name('/\.(avi|mp4|webm|3gp|mkv)$/')
            ->sortByName(true);

        $media = new MediaList([], ['target' => $input->getArgument('target')]);
        $io->section("These video will be concatenated :");
        foreach ($search as $entry) {
            $io->writeln((string) $entry);
            $media[] = new MediaFile((string) $entry);
        }

        $io->section("Processing, please wait...");
        $cor = new ConcatYt();
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute($media);

        return 0;
    }

}
