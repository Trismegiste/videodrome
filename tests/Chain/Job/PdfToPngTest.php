<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

class PdfToPngTest extends TestCase {

    public function testExecute() {
        $sut = new PdfToPng(new ImpressToPdf());
        $ret = $sut->execute([new MetaFileInfo(__DIR__ . '/../../fixtures/fixtures1.odp', ['duration' => [1, 1, 1]])]);
        foreach ($ret as $png) {
            $this->assertFileExists((string) $png);
            $this->assertEquals(1, $png->getData('duration'));
        }
        // clean
        foreach ($ret as $png) {
            unlink($png);
        }
    }

}
