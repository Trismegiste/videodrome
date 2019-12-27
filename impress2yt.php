<?php

// autoload
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    include './lib/' . $class . '.php';
});

/* Presentation video generator
 * 
 * syntax: php impress2yt presentation.odp audacity-markers.txt sound.wav
 * 
 * @todo use symfony/console
 */

if (4 !== count($argv)) {
    throw new Exception("Bad parameters count");
}
generateVideo($argv[1], $argv[2], $argv[3]);

function generateVideo($impress, $marqueur, $voix) {
    $pdfTask = new \videodrome\ImpressToPdf($impress);
    $pdfTask->exec();
    $pdf = $pdfTask->getPdf();

    $timecode = file($marqueur);

    $diapoTask = new \videodrome\PdfToPng($pdf);
    if (count($timecode) !== $diapoTask->getPdfPageCount()) {
        throw new Exception("page count mismatch");
    }
    $diapoTask->exec();

    $vidname = [];
    $vidGen = new \videodrome\LoopTask();
    foreach ($diapoTask->getDiapoName() as $idx => $diapo) {
        $detail = preg_split('/[\s]+/', $timecode[$idx]);
        $delta = $detail[1] - $detail[0];
        $obj = new videodrome\PngToVideo($diapo, $delta);
        $vidGen->push($obj);
        $vidname[] = $obj->getOutputName();
    }
    $vidGen->exec();

    $concat = new \videodrome\VideoConcat($vidname, $voix);
    $concat->exec();
    $concat->clean();
    $vidGen->clean();
    $pdfTask->clean();
    $diapoTask->clean();
}
