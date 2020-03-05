<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Util\CutterCfg;

class CutterCfgTest extends TestCase {

    public function testRead() {
        $sut = new CutterCfg(__DIR__ . '/../fixtures/cutter.cfg');
        $this->assertCount(1, $sut);
        $this->assertEquals(2, $sut->getStart('cutter'));
        $this->assertEquals(0, $sut->getStart('doesnotexist'));
        $this->assertEquals(333, $sut->getStart('doesnotexist', 333));
    }

}
