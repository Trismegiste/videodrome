<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ConcatYt;
use Trismegiste\Videodrome\Chain\Job\LosslessCutterWithSound;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Util\Ffprobe;

class ConcatYtTest extends TestCase {

    public function testExecute() {
        $sut = new ConcatYt(new LosslessCutterWithSound());
        $list = new MediaList([], ['target' => 'final.mp4']);
        for ($idx = 0; $idx < 3; $idx++) {
            $list[] = new MediaFile(join_paths(__DIR__, '../../fixtures/cutter.mkv'), [
                'width' => 300,
                'height' => 200,
                'fps' => 30,
                'target' => "tmp-$idx",
                'cutBefore' => $idx,
                'duration' => 2
            ]);
        }
        $ret = $sut->execute($list);
        $this->assertTrue($ret->isLeaf());
        $this->assertFileExists((string) $ret);
        $info = new Ffprobe($ret);
        $this->assertLessThan(0.1, abs($info->getDuration() - 6));  // rounding frame
        $ret->unlink();
    }

}
