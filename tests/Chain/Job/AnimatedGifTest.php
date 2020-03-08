<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\AnimatedGif;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\MediaFile;

class AnimatedGifTest extends TestCase {

    public function testExecute() {
        $sut = new AnimatedGif(new PdfToPng(new ImpressToPdf()));
        $ret = $sut->execute(new MediaFile(__DIR__ . '/../../fixtures/fixtures1.odp', [
            'delay' => 6,
            'width' => 160,
            'height' => 90
        ]));

        $this->assertTrue($ret->isLeaf());
        $this->assertFileExists((string) $ret);
        list($width, $height) = getimagesize($ret);
        $this->assertEquals(160, $width);
        $this->assertEquals(90, $height);
        $ret->unlink();
    }

}
