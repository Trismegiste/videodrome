<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\AddingSound;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;
use Trismegiste\Videodrome\Chain\MediaFile;

class AddingSoundTest extends TestCase {

    public function testExecute() {
        $sut = new AddingSound(new VideoConcat(new PngToVideo(new PdfToPng(new ImpressToPdf()))));
        $vid = $sut->execute(new MediaFile(__DIR__ . '/../../fixtures/fixtures1.odp', [
            'duration' => [1, 1, 1],
            'sound' => __DIR__ . '/../../fixtures/sound1.ogg',
            'width' => 1920,
            'height' => 1080
        ]));

        $this->assertTrue($vid->isLeaf());
        $this->assertEquals('fixtures1-0-compil-sound.mp4', (string) $vid);
        $this->assertFileExists((string) $vid);
        $this->assertTrue($vid->isVideo());
        // clean
        $vid->unlink();
    }

}
