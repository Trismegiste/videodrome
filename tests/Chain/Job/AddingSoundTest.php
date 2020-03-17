<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\AddingSound;
use Trismegiste\Videodrome\Chain\Job\PdfToPng;
use Trismegiste\Videodrome\Chain\Job\PngToVideo;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Chain\MediaType\Pdf;

class AddingSoundTest extends TestCase {

    public function testExecute() {
        $sut = new AddingSound(new VideoConcat(new PngToVideo(new PdfToPng())));
        $vid = $sut->execute(new Pdf(__DIR__ . '/../../fixtures/fixtures1.pdf', [
                    'duration' => [1, 1, 1],
                    'sound' => __DIR__ . '/../../fixtures/sound1.ogg',
                    'width' => 192,
                    'height' => 108
        ]));

        $this->assertTrue($vid->isLeaf());
        $this->assertEquals('fixtures1-0-compil-sound.mp4', (string) $vid);
        $this->assertFileExists((string) $vid);
        $this->assertTrue($vid->isVideo());
        // clean
        $vid->unlink();
    }

    public function testFailNotLeaf() {
        $this->expectException(JobException::class);
        $sut = new AddingSound();
        $sut->execute(new MediaList());
    }

    public function testMissingSound() {
        $this->expectException(JobException::class);
        $this->expectExceptionMessage('AddingSound');
        $sut = new AddingSound();
        $sut->execute(new MediaFile(join_paths(__DIR__, '../../fixtures/cutter.mkv'), ['sound' => 'missing.wav']));
    }

}
