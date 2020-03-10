<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\PngOverlay;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

class PngOverlayTest extends TestCase {

    public function testExecute() {
        $sut = new PngOverlay();
        $over = __DIR__ . '/../../fixtures/picture3.png';
        $video = __DIR__ . '/../../fixtures/cutter.mkv';
        $ret = $sut->execute(new MediaList([
            new MediaFile($over, ['video' => $video])
        ]));

        $this->assertCount(1, $ret);
        $vid = $ret[0];
        $this->assertEquals('cutter-over.avi', (string) $vid);
        $this->assertFileExists((string) $vid);
        $this->assertTrue($vid->isVideo());
        $ret->unlink();
    }

}
