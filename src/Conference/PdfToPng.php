<?php

namespace Trismegiste\Videodrome\Conference;

use Trismegiste\Videodrome\Task;
use Symfony\Component\Process\Process;

/**
 * PdfToPng converts a PDF file to a set of PNG
 */
class PdfToPng implements Task {

    private $pdf;
    private $pageCount = null;
    private $format = '1920x1080';
    private $dpi = 200;

    public function __construct($fch) {
        $this->pdf = $fch;
    }

    public function clean() {
        shell_exec("rm diapo-*.png");
    }

    public function exec() {
        $magick = new Process("convert -density {$this->dpi} {$this->pdf} -resize {$this->format} diapo.png");
        $magick->mustRun();
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
            $r[] = "diapo-$k.png"; // @todo this could potentially bug after 9, need further investigation how convert generates names
        }

        return $r;
    }

}
