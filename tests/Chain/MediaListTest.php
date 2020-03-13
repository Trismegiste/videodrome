<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

class MediaListTest extends TestCase {

    public function simpleFixture() {
        return [[new MediaFile(__FILE__)]];
    }

    public function testEmptyCreation() {
        $sut = new MediaList();
        $this->assertCount(0, $sut);
        $this->assertInstanceOf(\Traversable::class, $sut->getIterator());
    }

    /** @dataProvider simpleFixture */
    public function testPushing($file) {
        $sut = new MediaList();
        $sut[] = $file;
        $sut[] = $file;
        $sut[] = $file;
        $this->assertCount(3, $sut);
    }

    /** @dataProvider simpleFixture */
    public function testCreationWithArray($file) {
        $sut = new MediaList([$file]);
        $this->assertCount(1, $sut);
    }

    /** @dataProvider simpleFixture */
    public function testGetter($file) {
        $sut = new MediaList([$file]);
        $this->assertEquals($file, $sut[0]);
    }

    /** @dataProvider simpleFixture */
    public function testIterator($file) {
        $sut = new MediaList([$file, $file, $file]);
        foreach ($sut as $item) {
            $this->assertEquals($file, $item);
        }
    }

    /** @dataProvider simpleFixture */
    public function testUnsetKey($file) {
        $sut = new MediaList([$file, $file, $file]);
        $this->assertCount(3, $sut);
        unset($sut[1]);
        $this->assertCount(2, $sut);
        $this->assertArrayNotHasKey(1, $sut);
    }

    /** @dataProvider simpleFixture */
    public function testMetadataGetter($file) {
        $sut = new MediaList([$file], ['width' => 1920]);
        $this->assertEquals(1920, $sut->getMeta('width'));
    }

    /** @dataProvider simpleFixture */
    public function testUnknownMetadata($file) {
        $this->expectException(OutOfBoundsException::class);
        $sut = new MediaList([$file]);
        $sut->getMeta('width');
    }

    /** @dataProvider simpleFixture */
    public function testHasMetadata($file) {
        $sut = new MediaList([$file], ['width' => 1920]);
        $this->assertTrue($sut->hasMeta('width'));
        $this->assertFalse($sut->hasMeta('height'));
    }

    public function testCheckType() {
        $this->expectException(UnexpectedValueException::class);
        new MediaList([new stdClass()]);
    }

    public function testLeaf() {
        $sut = new MediaList();
        $this->assertFalse($sut->isLeaf());
    }

}
