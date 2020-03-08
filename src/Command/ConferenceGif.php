<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\AnimatedGif;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\MediaFile;

/**
 * Presentation GIF generator
 */
class ConferenceGif extends Command {

    // the name of the command
    protected static $defaultName = 'conference:gif';

    protected function configure() {
        $this->setDescription("Generates a animated GIF from an Impress document")
                ->addArgument('impress', InputArgument::REQUIRED, "LibreOffice Impress document")
                ->addOption('delay', null, InputOption::VALUE_REQUIRED, "Delay (in seconds) between each slide", 5)
                ->addOption('width', null, InputOption::VALUE_REQUIRED, "Width of the video", 1920)
                ->addOption('height', null, InputOption::VALUE_REQUIRED, "Height of the video", 1080);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $impress = $input->getArgument('impress');

        $output->writeln("Conference GIF Generator");

        $job = new AnimatedGif(new PdfToPng(new ImpressToPdf()));
        //  $job = new PdfToPng(new ImpressToPdf());
        $job->setLogger(new ConsoleLogger($output));
        $job->execute(new MediaFile($impress, [
                'delay' => (float) $input->getOption('delay'),
                'width' => $input->getOption('width'),
                'height' => $input->getOption('height')
        ]));

        return 0;
    }

}
