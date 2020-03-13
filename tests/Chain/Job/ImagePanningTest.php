<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImageExtender;
use Trismegiste\Videodrome\Chain\Job\ImagePanning;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Util\Ffprobe;

class ImagePanningTest extends TestCase {

    public function fixtures() {
        return [
            [join_paths(__DIR__, '../../fixtures/picture1.jpg'), 200, 100, '-'],
            [join_paths(__DIR__, '../../fixtures/picture1.jpg'), 200, 100, '+'],
            [join_paths(__DIR__, '../../fixtures/picture1.jpg'), 100, 200, '-'],
            [join_paths(__DIR__, '../../fixtures/picture1.jpg'), 100, 200, '+'],
            [join_paths(__DIR__, '../../fixtures/picture1.jpg'), 474, 307, '+'],
        ];
    }

    /** @dataProvider fixtures */
    public function testExecute($image, $w, $h, $dir) {
        $sut = new ImagePanning(new ImageExtender());
        $ret = $sut->execute(new MediaList([
            new MediaFile($image, [
                'width' => $w, 'height' => $h,
                'duration' => 1,
                'direction' => $dir
                    ])
        ]));

        $this->assertCount(1, $ret);
        $vid = $ret[0];
        $this->assertEquals('picture1-extended.avi', (string) $vid);
        $this->assertTrue($vid->isVideo());
        $this->assertFileExists((string) $vid);
        $info = new Ffprobe($vid);
        $this->assertEquals($w, $info->getWidth());
        $this->assertEquals($h, $info->getHeight());
        // clean
        $ret->unlink();
    }

}
