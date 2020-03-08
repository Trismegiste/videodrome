<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\MediaFile;

class PngToVideoTest extends TestCase {

    public function testExecute() {
        $sut = new PngToVideo(new PdfToPng(new ImpressToPdf()));
        $ret = $sut->execute(new MediaFile(__DIR__ . '/../../fixtures/fixtures1.odp', [
            'duration' => [1, 1, 1],
            'width' => 1920,
            'height' => 1080
        ]));
        $this->assertFalse($ret->isLeaf());
        $this->assertCount(3, $ret);
        foreach ($ret as $vid) {
            $this->assertFileExists((string) $vid);
            $this->assertTrue($vid->isVideo());
            $this->assertEquals(1, $vid->getMeta('duration'));
        }
        // clean
        $ret->unlink();
    }

}
