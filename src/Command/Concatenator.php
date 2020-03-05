<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;
use Trismegiste\Videodrome\Chain\MetaFileInfo;
use Trismegiste\Videodrome\Util\AudacityMarker;

/**
 * Concat video with a marker file for sorting
 */
class Concatenator extends Command {

    protected static $defaultName = 'trailer:concat';

    protected function configure() {
        $this->setDescription("Concat video")
                ->addArgument('video', InputArgument::REQUIRED, "Folder full of video")
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker from the sound file");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $marker = new AudacityMarker($input->getArgument('marker'));
        $finder = new Finder();
        $finder->in($input->getArgument('video'))->files()->name('/-(cut|extended)-over.avi$/');
        $video = iterator_to_array($finder);

        $concat = [];
        foreach ($marker as $key => $timecode) {
            $found = array_filter($video, function ($v) use ($key) {
                return preg_match("/^$key-(cut|extended)-over.avi$/", $v->getFilename());
            });
            if (count($found) === 1) {
                $found = array_shift($found);
                $concat[] = new MetaFileInfo($found, ['start' => $timecode['start']]);
            } else {
                throw new RuntimeException("The key '$key' has " . count($found) . " video file(s)");
            }
        }

        $output->writeln("Concat video");
        $cor = new VideoConcat();
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute($concat);
    }

}
