<?php

namespace Trismegiste\Videodrome\Chain\Job;

use RuntimeException;
use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Util\PdfInfo;

/**
 * PdfToPng converts a PDF file to a set of PNG
 */
class PdfToPng extends FileJob {

    const antialiasFactor = 1.3;

    protected function process(Media $pdf): Media {
        if (!$pdf->isLeaf()) {
            throw new JobException("Not a single file");
        }

        $info = new PdfInfo($pdf);
        $dpi = $info->getMinDensityFor($pdf->getMeta('width'), $pdf->getMeta('height')) * self::antialiasFactor;

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

        $card = $info->getPageCount();
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
