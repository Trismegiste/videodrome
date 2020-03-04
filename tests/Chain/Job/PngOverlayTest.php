<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\PngOverlay;
use Trismegiste\Videodrome\Chain\Job\SvgToPng;

class PngOverlayTest extends TestCase {

    public function testExecute() {
        $sut = new PngOverlay(new SvgToPng());
        $over = __DIR__ . '/../../fixtures/picture1';
        $video = __DIR__ . '/../../fixtures/cutter.mkv';
        $ret = $sut->execute([$over . '.svg'], ['video' => ['picture1.png' => $video]]);
        $this->assertEquals(['cutter-over.avi'], $ret);
        $this->assertFileExists($ret[0]);
        unlink($ret[0]);
    }

}
