<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\MediaFile;

class PdfToPngTest extends TestCase {

    public function testExecute() {
        $sut = new PdfToPng(new ImpressToPdf());
        $ret = $sut->execute(new MediaFile(__DIR__ . '/../../fixtures/fixtures1.odp', [
            'duration' => [1, 1, 1],
            'width' => 1920,
            'height' => 1080
        ]));

        $this->assertCount(3, $ret);
        foreach ($ret as $png) {
            $this->assertFileExists((string) $png);
            $this->assertEquals(1, $png->getMeta('duration'));
            $this->assertTrue($png->isPicture());
        }
        // clean
        $ret->unlink();
    }

}
