<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\AddingSound;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;

/**
 * Presentation video generator
 */
class Conference extends Command {

    const dependencies = [
        ['libreoffice6.0', '--version', 'LibreOffice'],
        ['pdfinfo', '-v', 'Plopper'],
        ['convert', '-version', 'ImageMagick'],
        ['ffmpeg', '-version', 'ffmpeg'],
    ];

    // the name of the command
    protected static $defaultName = 'conference:build';

    protected function initialize(InputInterface $input, OutputInterface $output) {
        // check installed dependencies
        foreach (self::dependencies as $app) {
            $check = new Process([$app[0], $app[1]]);
            $check->run();
            if (!$check->isSuccessful()) {
                throw new RuntimeException($app[2] . ' is missing');
            }
        }
    }

    protected function configure() {
        $this->setDescription("Generates a video from an Impress document, a recorded voice and a timecode file from Audacity")
                ->addArgument('impress', InputArgument::REQUIRED, "LibreOffice Impress document")
                ->addArgument('voice', InputArgument::REQUIRED, "Sound file")
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker from the sound file");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $impress = $input->getArgument('impress');
        $voix = $input->getArgument('voice');
        $marqueur = $input->getArgument('marker');

        $output->writeln("Conference Video Generator");
        $timecode = file($marqueur);
        $duration = [];
        foreach ($timecode as $line) {
            $detail = preg_split('/[\s]+/', $line);
            $duration[] = $detail[1] - $detail[0];
        }

        $job = new AddingSound(new VideoConcat(new PngToVideo(new PdfToPng(new ImpressToPdf()))));
        $job->setLogger(new ConsoleLogger($output));
        $job->execute([$impress], ['duration' => $duration, 'sound' => $voix]);

        return 0;
    }

}
