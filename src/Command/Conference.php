<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;

/**
 * Presentation video generator
 */
class Conference extends Command {

    // the name of the command
    protected static $defaultName = 'conference:build';

    protected function initialize(InputInterface $input, OutputInterface $output) {

        // LibreOffice
        $check = new Process(['libreoffice6.0', '--version']);
        $check->run();
        if (!$check->isSuccessful()) {
            throw new \RuntimeException('LibreOffice is missing');
        }

        // Plopper  
        $check = new Process(['pdfinfo', '-v']);
        $check->run();
        if (!$check->isSuccessful()) {
            throw new \RuntimeException('Plopper is missing');
        }

        // ImageMagick
        $check = new Process(['convert', '-version']);
        $check->run();
        if (!$check->isSuccessful()) {
            throw new \RuntimeException('ImageMagick is missing');
        }

        // ffmpeg  
        $check = new Process(['ffmpeg', '-version']);
        $check->run();
        if (!$check->isSuccessful()) {
            throw new \RuntimeException('ffmpeg is missing');
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

        $progressBar = new \Symfony\Component\Console\Helper\ProgressBar($output, 5 * count($timecode));
        $progressBar->start();

        // PDF generator
        $pdfTask = new \Trismegiste\Videodrome\Conference\ImpressToPdf($impress);
        $pdfTask->exec();
        $progressBar->advance(count($timecode));

        // PNG generator
        $diapoTask = new \Trismegiste\Videodrome\Conference\PdfToPng($pdfTask->getPdf());
        if (count($timecode) !== $diapoTask->getPdfPageCount()) {
            throw new \Exception("Page count mismatch");
        }
        $diapoTask->exec();
        $progressBar->advance(count($timecode));

        // Convert to AVI
        $vidname = [];
        $vidGen = new \Trismegiste\Videodrome\LoopTask();
        foreach ($diapoTask->getDiapoName() as $idx => $diapo) {
            $detail = preg_split('/[\s]+/', $timecode[$idx]);
            $delta = $detail[1] - $detail[0];
            $obj = new \Trismegiste\Videodrome\Conference\PngToVideo($progressBar, $diapo, $delta);
            $vidGen->push($obj);
            $vidname[] = $obj->getOutputName();
        }
        $vidGen->exec();

        // Concat & sound
        $concat = new \Trismegiste\Videodrome\Conference\VideoConcat($vidname, $voix);
        $concat->exec();
        $progressBar->advance(count($timecode));

        // Cleaning
        $concat->clean();
        $vidGen->clean();
        $pdfTask->clean();
        $diapoTask->clean();
        $progressBar->finish();

        return 0;
    }

}
