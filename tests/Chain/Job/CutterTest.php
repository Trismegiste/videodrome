<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\Cutter;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

class CutterTest extends TestCase {

    public function testExecute() {
        $video = __DIR__ . '/../../fixtures/cutter.mkv';
        $sut = new Cutter();
        $ret = $sut->execute([new MetaFileInfo($video, [
                'width' => 480, 'height' => 270,
                'duration' => 3,
                'start' => 3
        ])]);

        $this->assertCount(1, $ret);
        $this->assertEquals('cutter-cut.avi', (string) $ret[0]);
        $this->assertFileExists((string) $ret[0]);
        $this->assertTrue($ret[0]->isVideo());
        unlink($ret[0]);
    }

}
