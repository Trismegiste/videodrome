<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\Cutter;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

class CutterTest extends TestCase {

    public function testExecute() {
        $video = __DIR__ . '/../../fixtures/cutter.mkv';
        $sut = new Cutter();
        $ret = $sut->execute(new MediaList([
            new MediaFile($video, [
                'width' => 480, 'height' => 270,
                'duration' => 3,
                'cutBefore' => 3
                    ])
        ]));

        $this->assertFalse($ret->isLeaf());
        $this->assertCount(1, $ret);
        $vid = $ret[0];
        $this->assertEquals('cutter-cut.avi', (string) $vid);
        $this->assertFileExists((string) $vid);
        $this->assertTrue($vid->isVideo());
        $ret->unlink();
    }

}
