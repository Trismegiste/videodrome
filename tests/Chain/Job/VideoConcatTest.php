<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;

class VideoConcatTest extends TestCase {

    public function testExecute() {
        $sut = new VideoConcat(new PngToVideo(new PdfToPng(new ImpressToPdf())));
        $ret = $sut->execute([__DIR__ . '/../../fixtures/fixtures1.odp'], ['duration' => [1, 1, 1]]);
        $this->assertEquals(['fixtures1-0-compil.mp4'], $ret);
        $this->assertFileExists($ret[0]);
        // clean
        unlink($ret[0]);
    }

}
