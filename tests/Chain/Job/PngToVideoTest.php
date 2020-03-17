<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\MediaType\Pdf;

class PngToVideoTest extends TestCase {

    public function testExecute() {
        $sut = new PngToVideo(new PdfToPng());
        $ret = $sut->execute(new Pdf(__DIR__ . '/../../fixtures/fixtures1.pdf', [
            'duration' => [1, 1, 1],
            'width' => 192,
            'height' => 108
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
