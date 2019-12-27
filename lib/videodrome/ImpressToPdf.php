<?php

namespace videodrome;

/**
 * ImpressToPdf convert an Impress file to a PDF
 */
class ImpressToPdf implements Task {

    private $pdf;
    private $impress;

    public function __construct($fch) {
        $this->impress = $fch;
        $this->pdf = preg_replace('/(^|.+\/)([^\/]+)\.odp$/', '\\2.pdf', $this->impress);
    }

    public function exec() {
        shell_exec("libreoffice6.0 --convert-to pdf {$this->impress}");
    }

    public function clean() {
        shell_exec("rm {$this->pdf}");
    }

    public function getPdf() {
        return $this->pdf;
    }

}
