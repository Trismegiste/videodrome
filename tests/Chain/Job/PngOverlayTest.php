<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\PngOverlay;
use Trismegiste\Videodrome\Chain\Job\SvgToPng;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

class PngOverlayTest extends TestCase {

    public function testExecute() {
        $sut = new PngOverlay(new SvgToPng());
        $over = __DIR__ . '/../../fixtures/picture1';
        $video = __DIR__ . '/../../fixtures/cutter.mkv';
        $ret = $sut->execute([new MetaFileInfo($over . '.svg', ['video' => $video])]);

        $this->assertCount(1, $ret);
        $vid = $ret[0];
        $this->assertEquals('cutter-over.avi', (string) $vid);
        $this->assertFileExists((string) $vid);
        $this->assertTrue($vid->isVideo());
        unlink($vid);
    }

}
