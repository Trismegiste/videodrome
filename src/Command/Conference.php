<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\AddingSound;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Util\AudacityMarker;

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
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker for the sound file")
                ->addOption('width', null, InputOption::VALUE_REQUIRED, "Width of the video", 1920)
                ->addOption('height', null, InputOption::VALUE_REQUIRED, "Height of the video", 1080);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $impress = $input->getArgument('impress');
        $voix = $input->getArgument('voice');
        $timecode = new AudacityMarker($input->getArgument('marker'));

        $io->title("Conference Video Generator");
        $duration = [];
        foreach ($timecode as $key => $detail) {
            $duration[] = $timecode->getDuration($key);
        }

        $job = new AddingSound(new VideoConcat(new PngToVideo(new PdfToPng(new ImpressToPdf()))));
        $job->setLogger(new ConsoleLogger($output));
        $job->execute(new MediaFile($impress, [
            'duration' => $duration,
            'sound' => $voix,
            'width' => $input->getOption('width'),
            'height' => $input->getOption('height')
        ]));

        return 0;
    }

}
