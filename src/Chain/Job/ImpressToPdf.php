<?php

namespace Trismegiste\Videodrome\Chain\Job;

use RuntimeException;
use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MediaList;

/**
 * ImpressToPdf convert a LibreOffice Impress file to a PDF
 */
class ImpressToPdf extends FileJob {

    protected function process(MediaList $filename): MediaList {
        list($impress) = $filename;
        $pdf = $impress->getFilenameNoExtension() . '.pdf';
        $proc = new Process(['libreoffice6.0', '--convert-to', 'pdf', $impress]);
        $proc->mustRun();

        try {
            $generated = $impress->createChild($pdf);
        } catch (RuntimeException $ex) {
            throw new JobException("ImpressToPdf : creation of $pdf failed");
        }

        $this->logger->info("$pdf generated");

        return $filename->createChild([$generated]);
    }

}
