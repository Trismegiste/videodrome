<?php

$rep = "/home/flo/Bureau/";

$pdf = $rep . "Présentation-SaWo.pdf";
$marqueur = $rep . "Présentation-SaWo.txt";
$voix = $rep . "Présentation-SaWo.wav";

$timecode = file($marqueur);

foreach ($timecode as $diapo) {
    $detail = preg_split('/[\s]+/', $diapo);
    var_dump($detail);
}


$tmp = shell_exec("pdfinfo " . $pdf);
$tmp = preg_match('/^Pages:[\s]+([\d]+)$/m', $tmp, $result);
$nbPage = (int) $result[1];
var_dump($nbPage);
