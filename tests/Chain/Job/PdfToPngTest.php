<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Chain\MediaType\Pdf;

class PdfToPngTest extends TestCase {

    public function testExecute() {
        $sut = new PdfToPng();
        $ret = $sut->execute(new Pdf(__DIR__ . '/../../fixtures/fixtures1.pdf', [
                    'duration' => [1, 1, 1],
                    'width' => 192,
                    'height' => 108
        ]));

        $this->assertCount(3, $ret);
        foreach ($ret as $png) {
            $this->assertFileExists((string) $png);
            $this->assertEquals(1, $png->getMeta('duration'));
            $this->assertEquals(192, $png->getMeta('width'));
            $this->assertEquals(108, $png->getMeta('height'));
            $this->assertTrue($png->isPicture());
        }
        // clean
        $ret->unlink();
    }

    public function testFailNotPdf1() {
        $this->expectException(JobException::class);
        $this->expectExceptionMessage('PDF');
        $sut = new PdfToPng();
        $sut->execute(new MediaList());
    }

    public function testFailNotPdf2() {
        $this->expectException(JobException::class);
        $this->expectExceptionMessage('PDF');
        $sut = new PdfToPng();
        $sut->execute(new MediaFile(__FILE__));
    }

}
