<?php

namespace Trismegiste\Videodrome\Command;

use RuntimeException;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This commands sorts video in a config file for editing
 */
class EditingSort extends Command {

    const defaultCfgName = "editing.json";

    protected static $defaultName = 'edit:sort';
    protected $headless = false;

    protected function configure() {
        $this->setDescription("Reorder video in a config file for ediiting")
                ->addArgument('config', InputArgument::REQUIRED, 'A json config file');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $io->title('Re-ordering of video in a config files for video editing');

        $jsonFile = $input->getArgument('config');
        $config = json_decode(file_get_contents($jsonFile));

        do {
            // print table
            $io->table(['#', 'label', 'path', 'start', 'duration'], array_map(function($k, stdClass $param) {
                        return [$k, $param->label, $param->video, $param->start, $param->duration];
                    }, array_keys($config), $config));

            $choice = $io->ask("You choice : '<Number> <insertBefore>' or 's' for save or 'c' for cancel", 's', function($a) use ($config) {
                $a = trim($a);

                if (in_array($a, ['s', 'c'])) {
                    return $a;
                }

                $found = [];
                if (preg_match('/^(\d+)\s+(\d+)$/', $a, $found) &&
                        array_key_exists($found[1], $config) &&
                        array_key_exists($found[2], $config)) {
                    return (object) ['key' => $found[1], 'insertBefore' => $found[2]];
                }

                throw new RuntimeException('Bad choice');
            });

            if (is_object($choice)) {
                $param = $config[$choice->key];
                array_splice($config, $choice->insertBefore, 1, [$param, $config[$choice->insertBefore]]);
                array_splice($config, $choice->key + ($choice->key > $choice->insertBefore ? 1 : 0), 1);
            }
        } while (is_object($choice));

        if ($choice === 's') {
            file_put_contents($jsonFile, json_encode($config));
        }

        return 0;
    }

}
