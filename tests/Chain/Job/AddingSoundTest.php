<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\AddingSound;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;

class AddingSoundTest extends TestCase {

    public function testExecute() {
        $sut = new AddingSound(new VideoConcat(new PngToVideo(new PdfToPng(new ImpressToPdf()))));
        $ret = $sut->execute([__DIR__ . '/fixtures1.odp'], ['duration' => [1, 1, 1], 'sound' => __DIR__ . '/sound1.ogg']);
        $this->assertEquals(['fixtures1-0-compil-sound.mp4'], $ret);
        $this->assertFileExists($ret[0]);
        // clean
        unlink($ret[0]);
    }

}
