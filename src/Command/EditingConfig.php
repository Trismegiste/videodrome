<?php

namespace Trismegiste\Videodrome\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

/**
 * This commands generates a config file for cut & crop a set of movies
 */
class EditingConfig extends Command {

    protected static $defaultName = 'edit:config';

    protected function configure() {
        $this->setDescription("Generates a config file for cut, crop and concat a set of movies")
                ->addArgument('video', InputArgument::REQUIRED, 'A folder full of movies')
                ->addArgument('config', InputArgument::OPTIONAL, 'A json config file', "editing.json");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $jsonFile = $input->getArgument('config');
        if (file_exists($jsonFile)) {
            $config = json_decode(file_get_contents($jsonFile), true);
        } else {
            $config = [];
        }

        $search = new Finder();
        $search->in($input->getArgument('video'))->files()->name('/\.(avi|mp4|webm|3gp|mkv)$/')->sortByName();
        $video = [];
        foreach ($search as $item) {
            $video[] = $item;
        }

        do {
            $io->table(['#', 'name'], array_map(function($k, SplFileInfo $v) {
                        return [$k, $v->getBasename()];
                    }, array_keys($video), $video));

            $choice = $io->ask("You choice", 'q');
            if ($choice !== 'q') {
                $this->launchPlayer($video[$choice]);
                $begin = $this->convertToSeconds($io->ask("Start time", 0));
                $this->launchPlayer($video[$choice], $begin);
                $delta = $this->convertToSeconds($io->ask("End time")) - $begin;
                $config[] = [
                    'video' => (string) $video[$choice],
                    'start' => $begin,
                    'duration' => $delta,
                    'label' => $video[$choice]->getBasename()
                ];
                file_put_contents($jsonFile, json_encode($config));
            }
        } while ($choice !== 'q');
    }

    private function launchPlayer(string $path, float $beginAt = 0) {
        $ffplay = new Process('ffplay -vf '
                . '"drawtext=text=\'%{pts\:hms}\':box=1:x=(w-tw)/2:y=h-(2*lh):fontsize=42:fontfile=/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"'
                . " -ss $beginAt \"$path\"");
        $ffplay->mustRun();
    }

    private function convertToSeconds(string $input): float {
        $timecode = explode(':', $input);
        switch (count($timecode)) {
            case 1: $timing = $timecode[0];
                break;
            case 2 : $timing = 60 * $timecode[0] + $timecode[1];
                break;
            case 3 : $timing = 3600 * $timecode[0] + 60 * $timecode[1] + $timecode[2];
                break;
            default : throw new InvalidArgumentException("Unknown format");
        }

        return $timing;
    }

}
