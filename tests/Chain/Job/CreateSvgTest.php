<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\CreateSvg;
use Trismegiste\Videodrome\Chain\MediaList;

class CreateSvgTest extends TestCase {

    const width = 1500;
    const height = 1000;

    public function testExecute() {
        $sut = new CreateSvg();
        $ret = $sut->execute(new MediaList([], [
            'width' => self::width,
            'height' => self::height,
            'folder' => '.',
            'name' => ['img1', 'img2']
        ]));

        $this->assertCount(2, $ret);
        foreach ($ret as $img) {
            $this->assertFileExists((string) $img);
        }
        $ret->unlink();
    }

}
