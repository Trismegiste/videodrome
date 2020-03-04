<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

class VideoConcatTest extends TestCase {

    public function testExecute() {
        $sut = new VideoConcat(new PngToVideo(new PdfToPng(new ImpressToPdf())));
        $ret = $sut->execute([new MetaFileInfo(__DIR__ . '/../../fixtures/fixtures1.odp', ['duration' => [1, 1, 1]])]);

        $this->assertCount(1, $ret);
        $vid = $ret[0];
        $this->assertEquals('fixtures1-0-compil.mp4', (string) $vid);
        $this->assertFileExists((string) $vid);
        $this->assertTrue($vid->isVideo());
    //    $this->assertEquals(3, $vid->getData('duration'));
        // clean
        unlink($vid);
    }

}
