<?php

namespace Trismegiste\Videodrome\Chain\Job;

use RuntimeException;
use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

/**
 * PdfToPng converts a PDF file to a set of PNG
 */
class PdfToPng extends FileJob {

    const dpi = 200;
    const format = '1920x1080';

    protected function process(array $filename): array {
        list($pdf) = $filename;

        $exportName = $pdf->getFilenameNoExtension();
        $magick = new Process([
            'convert',
            '-density', self::dpi,
            $pdf,
            '-resize', self::format,
            $exportName . '.png'
        ]);
        $magick->setTimeout(null);
        $magick->mustRun();

        $card = $this->getPdfPageCount($pdf);
        try {
            $result = [];
            for ($k = 0; $k < $card; $k++) {
                $tmpname = $exportName . "-$k.png";
                $metadata = $pdf->getMetadata();
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

    private function getPdfPageCount($pdf) {
        $tmp = shell_exec("pdfinfo " . $pdf);
        $result = [];
        preg_match('/^Pages:[\s]+([\d]+)$/m', $tmp, $result);

        return (int) $result[1];
    }

}
