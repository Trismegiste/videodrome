<?php

namespace Trismegiste\Videodrome\Chain\Job;

use RuntimeException;
use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Chain\MediaType\MediaPdf;

/**
 * PdfToPng converts a PDF file to a set of PNG
 */
class PdfToPng extends FileJob {

    const antialiasFactor = 1.3;

    protected function process(Media $pdf): Media {
        if (!($pdf instanceof MediaPdf)) {
            throw new JobException("Not a PDF");
        }

        // note : why I don't use internal metadata from this PDF ? Because width and height are external data specific for this process.
        // Think as an "output" width and height in pixels for PNG. If there is a FileJob subclass which converts PDF to SVG for example,
        // pixels are irrelevant.
        $dpi = $pdf->getMinDensityFor($pdf->getMeta('width'), $pdf->getMeta('height')) * self::antialiasFactor;

        $exportName = $pdf->getFilenameNoExtension();
        $magick = new Process([
            'convert',
            '-density', $dpi,
            $pdf,
            '-resize', $pdf->getMeta('width') . 'x' . $pdf->getMeta('height') . '!', // discard aspect ratio
            $exportName . '.png'
        ]);
        $magick->setTimeout(null);
        $magick->mustRun();

        $card = $pdf->getPageCount();
        try {
            $metadataPdf = $pdf->getMetadataSet();
            $result = new MediaList([], $metadataPdf);
            for ($k = 0; $k < $card; $k++) {
                $tmpname = $exportName . "-$k.png";
                // explode duration metadata for each PNG
                $metadataPng = $metadataPdf;
                if ($pdf->hasMeta('duration')) {
                    $metadataPng['duration'] = $metadataPdf['duration'][$k];
                }
                $result[] = new MediaFile($tmpname, $metadataPng);
            }
        } catch (RuntimeException $ex) {
            throw new JobException("PdfToPng : " . $ex->getMessage());
        }

        $this->logger->info(count($result) . " PNG generated");

        return $result;
    }

}
