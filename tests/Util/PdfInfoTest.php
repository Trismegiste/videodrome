<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Util\PdfInfo;

class PdfInfoTest extends TestCase {

    public function testInfo() {
        $sut = new PdfInfo(__DIR__ . '/../fixtures/pdfinfo.pdf');
        $this->assertEquals(3, $sut->getPageCount());
        $this->assertEquals(793.672, $sut->getWidth());
        $this->assertEquals(595.247, $sut->getHeight());
        $this->assertGreaterThan(174, $sut->getMinDensityFor(1920, 1080));
    }

}
