<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImageExtender;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

class ImageExtenderTest extends TestCase {

    public function testExecute() {
        $sut = new ImageExtender();
        $ret = $sut->execute(new MediaList([
            new MediaFile(__DIR__ . '/../../fixtures/picture1.jpg', ['width' => 800, 'height' => 400])
        ]));

        $this->assertCount(1, $ret);
        $img = $ret[0];
        $this->assertEquals('picture1-extended.png', (string) $img);
        $this->assertTrue($img->isPicture());
        $this->assertFileExists((string) $img);
        $size = getimagesize($img);
        $this->assertGreaterThanOrEqual(800, $size[0]);
        $this->assertGreaterThanOrEqual(400, $size[1]);
        unlink($img);
    }

}
