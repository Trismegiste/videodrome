<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImageExtender;
use Trismegiste\Videodrome\Chain\Job\ImagePanning;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

class ImagePanningTest extends TestCase {

    public function testExecute() {
        $sut = new ImagePanning(new ImageExtender());
        $ret = $sut->execute(new MediaList([
            new MediaFile(__DIR__ . '/../../fixtures/picture1.jpg', [
                'width' => 800, 'height' => 400,
                'duration' => 1,
                'direction' => '+'
                    ])
        ]));

        $this->assertCount(1, $ret);
        $vid = $ret[0];
        $this->assertEquals('picture1-extended.avi', (string) $vid);
        $this->assertTrue($vid->isVideo());
        $this->assertFileExists((string) $vid);
        // clean
        $ret->unlink();
    }

}
