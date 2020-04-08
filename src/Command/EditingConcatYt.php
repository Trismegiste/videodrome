<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\ConcatYt;
use Trismegiste\Videodrome\Chain\Job\LosslessCutterWithSound;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

/**
 * Concat video from an editing config and compress it for youtube
 */
class EditingConcatYt extends Command
{

    protected static $defaultName = 'edit:youtube';

    protected function configure()
    {
        $this->setDescription('Concat videos from a config and compress for Youtube')
            ->addArgument('config', InputArgument::REQUIRED, 'A json config file')
            ->addArgument('target', InputArgument::OPTIONAL, 'output filename (mp4)', 'output.mp4')
            ->addOption('width', NULL, InputOption::VALUE_REQUIRED, 'Width of the final video', 1920)
            ->addOption('height', NULL, InputOption::VALUE_REQUIRED, 'Height of the final video', 1080)
            ->addOption('fps', NULL, InputOption::VALUE_REQUIRED, 'Framerate of the final video', 30);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $config = json_decode(file_get_contents($input->getArgument('config')));

        $media = new MediaList([], ['target' => $input->getArgument('target')]);
        foreach ($config as $idx => $entry) {
            $media[] = new MediaFile($entry->video, [
                'width' => $input->getOption('width'),
                'height' => $input->getOption('height'),
                'target' => "tmpvid-$idx",
                'fps' => $input->getOption('fps'),
                'cutBefore' => $entry->start,
                'duration' => $entry->duration
            ]);
        }

        $cor = new ConcatYt(new LosslessCutterWithSound());
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute($media);

        return 0;
    }

}
