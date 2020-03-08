<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\CreateTitlePng;
use Trismegiste\Videodrome\Chain\MediaList;

class CreateTitlePngTest extends TestCase {

    const width = 1500;
    const height = 1000;

    public function testGenerate() {
        $sut = new CreateTitlePng();
        $ret = $sut->execute(new MediaList([], [
            'width' => self::width,
            'height' => self::height,
            'folder' => '.',
            'name' => ['img1', 'img2']
        ]));

        $this->assertCount(2, $ret);
        foreach ($ret as $img) {
            list($width, $height) = getimagesize($img);
            $this->assertEquals(self::width, $width);
            $this->assertEquals(self::height, $height);
        }
        $ret->unlink();
    }

}
