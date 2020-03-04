<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Check the system configuration
 */
class SystemCheck extends Command {

    protected static $defaultName = 'system:check';

    const dependencies = [
        ['libreoffice6.0', '--version', 'LibreOffice'],
        ['pdfinfo', '-v', 'Plopper'],
        ['convert', '-version', 'ImageMagick'],
        ['ffmpeg', '-version', 'ffmpeg'],
        ['inkscape', '--version', 'Inkscape'],
    ];

    protected function configure() {
        $this->setDescription("Check if software component are avaliable");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        // check installed dependencies
        foreach (self::dependencies as $app) {
            $output->write("Checking for {$app[2]}...");
            $check = new Process([$app[0], $app[1]]);
            $check->run();
            if (!$check->isSuccessful()) {
                throw new RuntimeException($app[2] . ' is missing');
            }
            $output->writeln(" OK");
        }

        $output->writeln("Everything seems alright");
    }

}
