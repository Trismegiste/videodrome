<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\SvgToPng;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

class SvgToPngTest extends TestCase {

    public function testConvert() {
        $sut = new SvgToPng();
        $ret = $sut->execute(new MediaList([
            new MediaFile(__DIR__ . '/../../fixtures/picture1.svg', [
                'width' => 500,
                'height' => 300
                    ])
        ]));

        $this->assertCount(1, $ret);
        $this->assertEquals('picture1.png', (string) $ret[0]);
        $this->assertFileExists((string) $ret[0]);
        $this->assertTrue($ret[0]->isPicture());
        $ret->unlink();
    }

}
