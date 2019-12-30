<?php

namespace Trismegiste\Videodrome\Conference;

use Trismegiste\Videodrome\Task;
use Symfony\Component\Process\Process;

/**
 * ImpressToPdf convert an Impress file to a PDF
 */
class ImpressToPdf implements Task {

    private $pdf;
    private $impress;

    public function __construct($fch) {
        $this->impress = $fch;
        $this->pdf = preg_replace('/(^|.+\/)([^\/]+)\.odp$/', '\\2.pdf', $this->impress); // @todo use pathinfo FFS !
    }

    public function exec() {
        $libre = new Process(['libreoffice6.0', '--convert-to', 'pdf', $this->impress]);
        $libre->mustRun();
    }

    public function clean() {
        shell_exec("rm {$this->pdf}");
    }

    public function getPdf() {
        return $this->pdf;
    }

}
