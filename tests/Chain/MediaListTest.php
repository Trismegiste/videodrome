<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

class MediaListTest extends TestCase {

    public function simpleFixture() {
        return [[new MetaFileInfo(__FILE__)]];
    }

    public function testEmptyCreation() {
        $sut = new MediaList();
        $this->assertCount(0, $sut);
    }

    /** @dataProvider simpleFixture */
    public function testPushing($file) {
        $sut = new MediaList();
        $sut[] = $file;
        $this->assertCount(1, $sut);
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
        $this->assertEquals(1920, $sut->getData('width'));
    }

    /** @dataProvider simpleFixture 
     * @expectedException \OutOfBoundsException */
    public function testUnknownMetadata($file) {
        $sut = new MediaList([$file]);
        $sut->getData('width');
    }

    /** @dataProvider simpleFixture */
    public function testHasMetadata($file) {
        $sut = new MediaList([$file], ['width' => 1920]);
        $this->assertTrue($sut->hasData('width'));
        $this->assertFalse($sut->hasData('height'));
    }

    /** @dataProvider simpleFixture */
    public function testCreateChild($file) {
        $sut = new MediaList([$file], ['height' => 1080, 'width' => 1920]);
        $child = $sut->createChild([$file], ['width' => 2500]);
        $this->assertEquals(2500, $child->getData('width'));
        $this->assertEquals(1080, $child->getData('height'));
    }

    /** @expectedException \UnexpectedValueException */
    public function testCheckType() {
        new MediaList([new stdClass()]);
    }

}
