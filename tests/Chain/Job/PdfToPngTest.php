<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;

class PdfToPngTest extends TestCase {

    public function testExecute() {
        $sut = new PdfToPng(new ImpressToPdf());
        $ret = $sut->execute([__DIR__ . '/fixtures1.odp']);
        foreach ($ret as $png) {
            $this->assertTrue(file_exists($png));
        }
        // clean
        foreach ($ret as $png) {
            unlink($png);
        }
    }

}
