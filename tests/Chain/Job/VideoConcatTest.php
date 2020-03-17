<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Chain\MediaType\Pdf;

class VideoConcatTest extends TestCase {

    public function testExecute() {
        $sut = new VideoConcat(new PngToVideo(new PdfToPng()));
        $vid = $sut->execute(new Pdf(__DIR__ . '/../../fixtures/fixtures1.pdf', [
                    'duration' => [1, 1, 1],
                    'width' => 192,
                    'height' => 108
        ]));

        $this->assertTrue($vid->isLeaf());
        $this->assertEquals('fixtures1-0-compil.mp4', (string) $vid);
        $this->assertFileExists((string) $vid);
        $this->assertTrue($vid->isVideo());
        $this->assertEquals(192, $vid->getMeta('width'));
        // clean
        $vid->unlink();
    }

    public function testExecuteSorted() {
        $clip = ['red-extended-over.avi', 'blue-cut-over.avi'];
        $input = new MediaList();
        foreach ($clip as $idx => $fch) {
            $input[] = new MediaFile(__DIR__ . '/../../fixtures/' . $fch, ['start' => 3 - $idx]);
        }

        $sut = new VideoConcat();
        $vid = $sut->execute($input);

        $this->assertTrue($vid->isLeaf());
        $this->assertEquals('blue-cut-over-compil.mp4', (string) $vid);
        $this->assertFileExists((string) $vid);
        $this->assertTrue($vid->isVideo());
        // clean
        $vid->unlink();
    }

    public function testEmptyList() {
        $this->expectException(JobException::class);
        $sut = new VideoConcat();
        $sut->execute(new MediaList([]));
    }

}
