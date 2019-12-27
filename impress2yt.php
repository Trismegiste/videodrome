<?php

// autoload
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    include './lib/' . $class . '.php';
});

// QUICK N UGLY presentation video generator
// syntax: php impress2yt presentation.odp audacity-markers.txt sound.wav

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
        $output = "vid-$idx.avi";
        $vidname[] = $output;
        $obj = new videodrome\PngToVideo($diapo, $delta, $output);
        $vidGen->push($obj);
    }
    $vidGen->exec();

    shell_exec('ffmpeg -y -i "concat:' . implode('|', $vidname) . '" sans-son.mp4');
    shell_exec("ffmpeg -y -i sans-son.mp4 -i $voix -shortest -strict -2 -c:v copy -c:a aac result.mp4");
    shell_exec("rm sans-son.mp4");
    $vidGen->clean();
    $pdfTask->clean();
    $diapoTask->clean();
}
