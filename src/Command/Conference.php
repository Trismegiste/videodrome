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
    protected static $defaultName = 'video:conf';

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

        $pdfTask = new \Trismegiste\Videodrome\Conference\ImpressToPdf($impress);
        $pdfTask->exec();
        $pdf = $pdfTask->getPdf();

        $timecode = file($marqueur);

        $diapoTask = new \Trismegiste\Videodrome\Conference\PdfToPng($pdf);
        if (count($timecode) !== $diapoTask->getPdfPageCount()) {
            throw new \Exception("Page count mismatch");
        }
        $diapoTask->exec();

        $vidname = [];
        $vidGen = new \Trismegiste\Videodrome\LoopTask();
        foreach ($diapoTask->getDiapoName() as $idx => $diapo) {
            $detail = preg_split('/[\s]+/', $timecode[$idx]);
            $delta = $detail[1] - $detail[0];
            $obj = new \Trismegiste\Videodrome\Conference\PngToVideo($diapo, $delta);
            $vidGen->push($obj);
            $vidname[] = $obj->getOutputName();
        }
        $vidGen->exec();

        $concat = new \Trismegiste\Videodrome\Conference\VideoConcat($vidname, $voix);
        $concat->exec();
        $concat->clean();
        $vidGen->clean();
        $pdfTask->clean();
        $diapoTask->clean();

        return 0;
    }

}
