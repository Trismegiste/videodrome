<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImageExtender;

class ImageExtenderTest extends TestCase {

    public function testExecute() {
        $sut = new ImageExtender();
        $ret = $sut->execute([__DIR__ . '/picture1.jpg'], ['width' => 800, 'height' => 400]);
        $this->assertEquals(['picture1-extended.png'], $ret);
        $this->assertFileExists($ret[0]);
        $size = getimagesize($ret[0]);
        $this->assertGreaterThanOrEqual(800, $size[0]);
        $this->assertGreaterThanOrEqual(400, $size[1]);
        unlink($ret[0]);
    }

}
