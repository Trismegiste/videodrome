<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\AddingSound;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

class AddingSoundTest extends TestCase {

    public function testExecute() {
        $sut = new AddingSound(new VideoConcat(new PngToVideo(new PdfToPng(new ImpressToPdf()))));
        $ret = $sut->execute([
            new MetaFileInfo(__DIR__ . '/../../fixtures/fixtures1.odp', [
                'duration' => [1, 1, 1],
                'sound' => __DIR__ . '/../../fixtures/sound1.ogg']
            )
        ]);

        $this->assertCount(1, $ret);
        $vid = $ret[0];
        $this->assertEquals('fixtures1-0-compil-sound.mp4', (string) $vid);
        $this->assertFileExists((string) $vid);
        $this->assertTrue($vid->isVideo());
        // clean
        unlink($ret[0]);
    }

}
