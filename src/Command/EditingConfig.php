<?php

namespace Trismegiste\Videodrome\Command;

use RuntimeException;
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

    const timecodePattern = "/^(((?'hour'\\d{1,2}):)?((?'minute'\\d{1,2}):))?(?'second'\\d{1,2}(\\.\\d+)?)$/";
    const defaultCfgName = "editing.json";

    protected static $defaultName = 'edit:config';
    protected $headless = false;

    protected function configure() {
        $this->setDescription("Generates a config file for cut, crop and concat a set of movies")
                ->addArgument('video', InputArgument::REQUIRED, 'A folder full of movies')
                ->addArgument('config', InputArgument::OPTIONAL, 'A json config file', self::defaultCfgName)
                ->addOption('headless', 'x', \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'No player is lauch - Text mode only');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->headless = $input->getOption('headless');
        $io = new SymfonyStyle($input, $output);
        $jsonFile = $input->getArgument('config');
        if (file_exists($jsonFile)) {
            $config = json_decode(file_get_contents($jsonFile), true);
        } else {
            $config = [];
        }

        $search = new Finder();
        $search->in($input->getArgument('video'))->files()->name('/\.(avi|mp4|webm|3gp|mkv)$/')->sortByName();
        $video = array_values(iterator_to_array($search));

        do {
            // print table
            $io->table(['#', 'name'], array_map(function($k, SplFileInfo $v) {
                        return [$k, $v->getBasename()];
                    }, array_keys($video), $video));

            $choice = $io->ask("You choice", 'q', function($a) use ($video) {
                if (($a === 'q') || (array_key_exists($a, $video))) {
                    return $a;
                }
                throw new RuntimeException('Bad choice');
            });

            if ($choice !== 'q') {
                // a valid nuber a video
                $validator = function($timecode): float {
                    $result = [];
                    if (!preg_match(self::timecodePattern, $timecode, $result)) {
                        throw new RuntimeException("Unknown format");
                    }

                    return $result['hour'] * 3600 + $result['minute'] * 60 + $result['second'];
                };

                $this->launchPlayer($video[$choice]);
                $begin = $io->ask("Start time", 0, $validator);

                $this->launchPlayer($video[$choice], $begin);
                $delta = $io->ask("End time", null, $validator) - $begin;

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
        if (!$this->headless) {
            $ffplay = new Process('ffplay -vf '
                    . '"drawtext=text=\'%{pts\:hms}\':box=1:x=(w-tw)/2:y=h-(2*lh):fontsize=42"'
                    . " -ss $beginAt \"$path\"");
            $ffplay->setTimeout(null);
            $ffplay->mustRun();
        }
    }

}
