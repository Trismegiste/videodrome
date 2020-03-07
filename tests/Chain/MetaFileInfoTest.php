<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

class MetaFileInfoTest extends TestCase {

    public function testMeta() {
        $sut = new MetaFileInfo(__FILE__, ['meta' => 555]);
        $this->assertEquals(555, $sut->getData('meta'));
    }

    /** @expectedException \RuntimeException */
    public function testNonExistingFileException() {
        new MetaFileInfo('zogzog');
    }

    public function testChild() {
        $sut = new MetaFileInfo(__FILE__, ['meta' => 555]);
        $child = $sut->createChild(__FILE__, ['meta' => 333]);
        $this->assertEquals(333, $child->getData('meta'));
    }

    public function testNoExtension() {
        $sut = new MetaFileInfo(__FILE__);
        $this->assertEquals('MetaFileInfoTest', $sut->getFilenameNoExtension());
    }

    public function testType() {
        $sut = new MetaFileInfo(__FILE__);
        $this->assertFalse($sut->isVideo());
        $this->assertFalse($sut->isPicture());
    }

}
