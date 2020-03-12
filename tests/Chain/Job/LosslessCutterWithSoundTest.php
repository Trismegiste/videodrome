<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\LosslessCutterWithSound;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Util\Ffprobe;

class LosslessCutterWithSoundTest extends TestCase {

    public function testExecute() {
        $sut = new LosslessCutterWithSound();
        $ret = $sut->execute(new MediaList([
            new MediaFile(__DIR__ . '/../../fixtures/cutter.mkv', [
                'width' => 300,
                'height' => 200,
                'fps' => 30,
                'target' => "lossless",
                'cutBefore' => 2,
                'duration' => 3
                    ])
        ]));

        $this->assertCount(1, $ret);
        $output = $ret[0];
        $this->assertFileExists((string) $output);
        $info = new Ffprobe($output);
        $this->assertEquals(200, $info->getHeight());
        $this->assertEquals(300, $info->getWidth());
        $this->assertEquals(3, $info->getDuration());
    }

}
