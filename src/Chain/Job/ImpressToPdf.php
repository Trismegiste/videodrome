<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;

/**
 * ImpressToPdf convert a LibreOffice Impress file to a PDF
 */
class ImpressToPdf extends FileJob {

    protected function process(array $filename, array $dummy): array {
        list($impress) = $filename;
        $tmp = pathinfo($impress);
        $pdf = $tmp['filename'] . '.pdf';
        $proc = new Process(['libreoffice6.0', '--convert-to', 'pdf', $impress]);
        $proc->mustRun();
        if (!file_exists($pdf)) {
            throw new JobException("ImpressToPdf : creation of $pdf failed");
        }
        $this->logger->info("$pdf generated");

        return [$pdf];
    }

}
