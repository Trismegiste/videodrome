<?php

namespace Trismegiste\Videodrome\Chain\Job;

use RuntimeException;
use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MetaFileInfo;
use Trismegiste\Videodrome\Util\PdfInfo;

/**
 * PdfToPng converts a PDF file to a set of PNG
 */
class PdfToPng extends FileJob {

    const antialiasFactor = 1.3;

    protected function process(array $filename): array {
        list($pdf) = $filename;
        $info = new PdfInfo($pdf);
        $dpi = $info->getMinDensityFor($pdf->getData('width'), $pdf->getData('height')) * self::antialiasFactor;

        $exportName = $pdf->getFilenameNoExtension();
        $magick = new Process([
            'convert',
            '-density', $dpi,
            $pdf,
            '-resize', $pdf->getData('width') . 'x' . $pdf->getData('height') . '!', // discard aspect ratio
            $exportName . '.png'
        ]);
        $magick->setTimeout(null);
        $magick->mustRun();

        $card = $info->getPageCount();
        try {
            $result = [];
            for ($k = 0; $k < $card; $k++) {
                $tmpname = $exportName . "-$k.png";
                // manage metadata
                $metadata = $pdf->getMetadata();
                // explode duration metadata for each PNG
                if (array_key_exists('duration', $metadata)) {
                    $metadata['duration'] = $metadata['duration'][$k];
                }
                $result[] = new MetaFileInfo($tmpname, $metadata);
            }
        } catch (RuntimeException $ex) {
            throw new JobException("PdfToPng : " . $ex->getMessage());
        }

        $this->logger->info(count($result) . " PNG generated");

        return $result;
    }

}
