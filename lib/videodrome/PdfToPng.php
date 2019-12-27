<?php

namespace videodrome;

/**
 * Description of PdfToPng
 */
class PdfToPng implements Task {

    private $pdf;
    private $pageCount = null;

    public function __construct($fch) {
        $this->pdf = $fch;
    }

    public function clean() {
        shell_exec("rm diapo-*.png");
    }

    public function exec() {
        shell_exec("convert -density 200 {$this->pdf} -resize 1920x1080 diapo.png");
    }

    public function getPdfPageCount() {
        if (is_null($this->pageCount)) {
            $tmp = shell_exec("pdfinfo " . $this->pdf);
            $tmp = preg_match('/^Pages:[\s]+([\d]+)$/m', $tmp, $result);
            $this->pageCount = (int) $result[1];
        }

        return $this->pageCount;
    }

    public function getDiapoName() {
        $r = [];
        for ($k = 0; $k < $this->getPdfPageCount(); $k++) {
            $r[] = "diapo-$k.png";
        }

        return $r;
    }

}
