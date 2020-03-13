<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\MediaFile;

class MediaFileTest extends TestCase {

    public function testMeta() {
        $sut = new MediaFile(__FILE__, ['meta' => 555]);
        $this->assertEquals(555, $sut->getMeta('meta'));
    }

    /** @expectedException \RuntimeException */
    public function testNonExistingFileException() {
        new MediaFile('zogzog');
    }

    public function testNoExtension() {
        $sut = new MediaFile(__FILE__);
        $this->assertEquals('MediaFileTest', $sut->getFilenameNoExtension());
    }

    public function testType() {
        $sut = new MediaFile(__FILE__);
        $this->assertFalse($sut->isVideo());
        $this->assertFalse($sut->isPicture());
    }

    public function testVideoType() {
        $sut = new MediaFile(join_paths(__DIR__, '../fixtures/cutter.mkv'));
        $this->assertTrue($sut->isVideo());
        $this->assertFalse($sut->isPicture());
    }

    public function testImageType() {
        $sut = new MediaFile(join_paths(__DIR__, '../fixtures/picture1.jpg'));
        $this->assertFalse($sut->isVideo());
        $this->assertTrue($sut->isPicture());
    }

}
