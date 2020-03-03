<?php

use PHPUnit\Framework\TestCase;

class ImageExtenderTest extends TestCase {

    public function testExecute() {
        $sut = new \Trismegiste\Videodrome\Chain\Job\ImageExtender();
        $ret = $sut->execute([__DIR__ . '/picture1.jpg'], ['width' => 800, 'height' => 400]);
        $this->assertEquals(['picture1-extended.png'], $ret);
        $this->assertTrue(file_exists($ret[0]));
        $size = getimagesize($ret[0]);
        $this->assertGreaterThanOrEqual(800, $size[0]);
        $this->assertGreaterThanOrEqual(400, $size[1]);
        unlink($ret[0]);
    }

}
