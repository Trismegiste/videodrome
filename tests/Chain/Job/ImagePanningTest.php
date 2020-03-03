<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImageExtender;
use Trismegiste\Videodrome\Chain\Job\ImagePanning;

class ImagePanningTest extends TestCase {

    public function testExecute() {
        $sut = new ImagePanning(new ImageExtender());
        $ret = $sut->execute([__DIR__ . '/picture1.jpg'], ['width' => 800, 'height' => 400, 'duration' => ['picture1-extended.png' => 1]]);
        $this->assertEquals(['picture1-extended.avi'], $ret);
        $this->assertFileExists($ret[0]);
        unlink($ret[0]);
    }

}
