<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\SvgToPng;

class SvgToPngTest extends TestCase {

    public function testConvert() {
        $sut = new SvgToPng();
        $ret = $sut->execute([__DIR__ . '/../../fixtures/picture1.svg']);
        $this->assertEquals(['picture1.png'], $ret);
        $this->assertFileExists($ret[0]);
        unlink($ret[0]);
    }

}
