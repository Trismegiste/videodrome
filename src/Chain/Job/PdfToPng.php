<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;

/**
 * PdfToPng converts a PDF file to a set of PNG
 */
class PdfToPng extends FileJob {

    const dpi = 200;
    const format = '1920x1080';

    protected function process(array $filename, array $notused): array {
        list($pdf) = $filename;

        $exportName = pathinfo($pdf, PATHINFO_FILENAME);
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
        $result = [];
        for ($k = 0; $k < $card; $k++) {
            $tmpname = $exportName . "-$k.png";
            if (!file_exists($tmpname)) {
                throw new JobException("PdfToPng : creation of $tmpname failed");
            }
            $result[] = $tmpname;
        }

        return $result;
    }

    private function getPdfPageCount($pdf) {
        $tmp = shell_exec("pdfinfo " . $pdf);
        $result = [];
        preg_match('/^Pages:[\s]+([\d]+)$/m', $tmp, $result);

        return (int) $result[1];
    }

}