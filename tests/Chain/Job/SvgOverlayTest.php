<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\SvgOverlay;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

class SvgOverlayTest extends TestCase {

    public function testExecute() {
        $sut = new SvgOverlay();
        $over = __DIR__ . '/../../fixtures/picture1.svg';
        $video = __DIR__ . '/../../fixtures/cutter.mkv';
        $ret = $sut->execute([new MetaFileInfo($video, ['svg' => $over])]);

        $this->assertCount(1, $ret);
        $vid = $ret[0];
        $this->assertEquals('cutter-over.avi', (string) $vid);
        $this->assertFileExists((string) $vid);
        $this->assertTrue($vid->isVideo());
        unlink($vid);
    }

}
