<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

/**
 * ImpressToPdf convert a LibreOffice Impress file to a PDF
 */
class ImpressToPdf extends FileJob {

    protected function process(array $filename): array {
        list($impress) = $filename;
        $pdf = $impress->getFilenameNoExtension() . '.pdf';
        $proc = new Process(['libreoffice6.0', '--convert-to', 'pdf', $impress]);
        $proc->mustRun();

        try {
            $generated = new MetaFileInfo($pdf, $impress->getMetadata());
        } catch (\RuntimeException $ex) {
            throw new JobException("ImpressToPdf : creation of $pdf failed");
        }

        $this->logger->info("$pdf generated");

        return [$generated];
    }

}
