<?php

namespace videodrome;

/**
 * Description of PdfToPng
 */
class PdfToPng implements Task {

    private $pdf;

    public function __construct($fch) {
        $this->pdf = $fch;
    }

    public function clean() {
        shell_exec("rm diapo-*.png");
    }

    public function exec() {
        shell_exec("convert -density 200 {$this->pdf} -resize 1920x1080 diapo.png");
    }

}
