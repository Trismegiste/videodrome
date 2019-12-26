<?php

$rep = "/home/flo/Bureau/";

$pdf = $rep . "Présentation-SaWo.pdf";
$marqueur = $rep . "Présentation-SaWo.txt";
$voix = $rep . "Présentation-SaWo.wav";

$timecode = file($marqueur);
if (count($timecode) !== getNbPage($pdf)) {
    throw new Exception("page count mismatch");
}

shell_exec("convert -density 200 $pdf -resize 1920x1080 diapo.png");

$vidname = [];
foreach ($timecode as $idx => $diapo) {
    $detail = preg_split('/[\s]+/', $diapo);
    $delta = $detail[1] - $detail[0];
    $output = "vid-$idx.avi";
    $vidname[] = $output;
    shell_exec("ffmpeg -y -framerate 5 -loop 1 -i diapo-$idx.png -t $delta -c:v huffyuv $output");
    unlink("./diapo-$idx.png");
}

shell_exec('ffmpeg -y -i "concat:' . implode('|', $vidname) . '" sans-son.mp4');
shell_exec("ffmpeg -y -i sans-son.mp4 -i $voix -shortest -strict -2 -c:v copy -c:a aac result.mp4");

//////////
function getNbPage($fch) {
    $tmp = shell_exec("pdfinfo " . $fch);
    $tmp = preg_match('/^Pages:[\s]+([\d]+)$/m', $tmp, $result);
    $nbPage = (int) $result[1];
    return $nbPage;
}

