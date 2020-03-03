<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;

class PngToVideoTest extends TestCase {

    public function testExecute() {
        $sut = new PngToVideo(new PdfToPng(new ImpressToPdf()));
        $ret = $sut->execute([__DIR__ . '/fixtures1.odp'], ['duration' => [1, 1, 1]]);
        foreach ($ret as $vid) {
            $this->assertTrue(file_exists($vid));
        }
        // clean
        foreach ($ret as $vid) {
            unlink($vid);
        }
    }

}
