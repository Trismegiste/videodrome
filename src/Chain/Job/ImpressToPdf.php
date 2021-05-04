<?php

namespace Trismegiste\Videodrome\Chain\Job;

use RuntimeException;
use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaType\Pdf;

/**
 * ImpressToPdf convert a LibreOffice Impress file into a PDF
 */
class ImpressToPdf extends FileJob {

    protected function process(Media $impress): Media {
        if (!$impress->isLeaf()) {
            throw new JobException('Not a single file');
        }
        $pdf = $impress->getFilenameNoExtension() . '.pdf';
        $proc = new Process(['libreoffice6.0', '--convert-to', 'pdf', $impress]);
        $proc->setTimeout(600);
        $proc->mustRun();

        try {
            $generated = new Pdf($pdf, $impress->getMetadataSet());
        } catch (RuntimeException $ex) {
            throw new JobException("ImpressToPdf : creation of $pdf failed. Perhaps LibreOffice is currently running ?");
        }

        $this->logger->info("$pdf generated");

        return $generated;
    }

}
