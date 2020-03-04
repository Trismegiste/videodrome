<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\AddingSound;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

/**
 * Presentation video generator
 */
class Conference extends Command {

    // the name of the command
    protected static $defaultName = 'conference:build';

    protected function configure() {
        $this->setDescription("Generates a video from an Impress document, a recorded voice and a timecode file")
                ->addArgument('impress', InputArgument::REQUIRED, "LibreOffice Impress document")
                ->addArgument('voice', InputArgument::REQUIRED, "Sound file")
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker for the sound file");
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
        $job->execute([new MetaFileInfo($impress, ['duration' => $duration, 'sound' => $voix])]);

        return 0;
    }

}
