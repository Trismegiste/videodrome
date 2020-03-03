<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\Cutter;

class CutterTest extends TestCase {

    public function testExecute() {
        $video = __DIR__ . '/../../fixtures/cutter.mkv';
        $sut = new Cutter();
        $ret = $sut->execute([$video], [
            'width' => 480, 'height' => 270,
            'duration' => [$video => 3],
            'start' => [$video => 3]
        ]);
        $this->assertEquals(['cutter-cut.avi'], $ret);
        $this->assertFileExists($ret[0]);
        unlink($ret[0]);
    }

}
